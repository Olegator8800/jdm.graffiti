<?php

namespace Jdm\Graffiti\Persistence;

use Jdm\Graffiti\Persistence\FieldDescription;
use Jdm\Graffiti\Persistence\Type;
use CDatabase;
use InvalidArgumentException;

abstract class AbstractRepository
{
    /**
     * @var array
     */
    protected $objectsForPersist = [];

    /**
     * @var CDatabase
     */
    protected $connection;

    /**
     * @var FieldDescription[]
     */
    protected $fieldDescriptions;

    public function __construct()
    {
        $this->connection = $GLOBALS['DB'];
        $this->fieldDescriptions = $this->getFieldDescriptions();
    }

    /**
     * @param object $entity
     * @throws InvalidArgumentException
     */
    public function add($entity)
    {
        $entityClass = $this->getEntityClassName();

        if (!($entity instanceof $entityClass)) {
            $message = sprintf('Entity must be instance "%s". Passed "%s"', $entityClass, get_class($entity));
            throw new InvalidArgumentException($message);
        }

        $this->objectsForPersist[] = $entity;

        return $this;
    }

    public function commit()
    {
        foreach ($this->objectsForPersist as $key => $object) {
            $data = [];
            $isNew = !(method_exists($object, 'getId') && $object->getId());

            if ($isNew) {
                $this->beforeCreate($object);
            } else {
                $this->beforeUpdate($object);
            }

            foreach ($this->fieldDescriptions as $description) {
                $type = $description->getType();
                $prefix = $type == Type::BOOLEAN ? 'is' : 'get';
                $method = $prefix.$description->getPropertyName();
                $value = $this->prepareValueToDB($object->$method(), $type);
                $data[$description->getFieldName()] = $value;
            }

            if ($isNew) {
                $this->connection->insert($this->getTableName(), $data);
            } else {
                $this->connection->update($this->getTableName(), $data, "WHERE ID='".$object->getId()."'");
            }

            unset($this->objectsForPersist[$key]);
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getLastInsertId()
    {
        return $this->connection->lastID();
    }

    /**
     * @return FieldDescription[]
     */
    abstract protected function getFieldDescriptions();

    /**
     * @return string
     */
    abstract protected function getTableName();

    /**
     * @return string
     */
    abstract protected function getEntityClassName();

    protected function prepareLimit($limit, $page)
    {
        if (empty($limit)) {
            return '';
        }

        $page = max(1, (int) $page);
        $count = (($page - 1) * $limit);

        return sprintf(' LIMIT %s, %s', $count, $limit);
    }

    /**
     * @return int|null
     */
    public function getCount()
    {
        $sql = $sql = sprintf('SELECT COUNT(*) FROM %s', $this->getTableName());

        $statement = $this->connection->query($sql);

        $result = $statement->fetch();

        if (empty($result)) {
            return null;
        }

        return $result['COUNT(*)'];
    }

    /**
     * @param  array $sort
     *
     * @return string
     */
    protected function prepareSort($sort)
    {
        if (empty($sort)) {
            return '';
        }

        $orederQuery = [];

        foreach ($sort as $field => $sortingMethod) {
            $orederQuery[] = sprintf('%s %s', $field, strtoupper($sortingMethod));
        }

        return ' ORDER BY '.implode(', ', $orederQuery);
    }

    /**
     * @param  array $result
     * @param  string $className Entity class name
     *
     * @return object
     */
    protected function createObjectFromAssocArray(array $result, $className)
    {
        $injectValue = function ($fieldDescriptions, $result) {
            foreach ($fieldDescriptions as $description) {
                $this->{$description->getPropertyName()} = $result[$description->getFieldName()];
            }
        };

        $object = (new \ReflectionClass($className))->newInstanceWithoutConstructor();

        $injectValue = $injectValue->bindTo($object, $object);
        $result = $this->prepareValueFromAssocArray($result);
        $injectValue($this->fieldDescriptions, $result);

        //для php <5.4
        /*$object = unserialize(sprintf('O:%d:"%s":0:{}', strlen($className), $className));
        $result = $this->prepareValueFromAssocArray($result);

        $reflectionClass = new \ReflectionClass($className);

        foreach ($this->fieldDescriptions as $description) {
            $reflectionProperty = $reflectionClass->getProperty($description->getPropertyName());

            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($object, $result[$description->getFieldName()]);
        }*/

        return $object;
    }

    /**
     * Create entity, fields value of which containing value of $result
     *
     * @param  array $result
     *
     * @return object
     */
    protected function createEntity(array $result)
    {
        return $this->createObjectFromAssocArray($result, $this->getEntityClassName());
    }

    /**
     * @param  array $result
     *
     * @return array
     */
    protected function prepareValueFromAssocArray(array $result)
    {
        foreach ($this->fieldDescriptions as $description) {
            $fieldName = $description->getFieldName();
            $result[$fieldName] = $this->prepareValue($result[$fieldName], $description->getType());
        }

        return $result;
    }

    /**
     * @param  mixed $value
     * @param  string $type
     *
     * @return mixed
     */
    protected function prepareValue($value, $type)
    {
        if ($type === Type::DATETIME) {
            if ($value) {
                $value = new \DateTime($value);
            }
        }

        if ($type === Type::INTEGER) {
            $value = intval($value);
        }

        if ($type === Type::DICTIONARY) {
            $result = json_decode($value, true);
            $value = $result && is_array($result) ? $result : [];
        }

        return $value;
    }

    /**
     * @param  mixed $value
     * @param  string $type
     *
     * @return mixed
     */
    protected function prepareValueToDB($value, $type)
    {
        switch ($type) {
            case Type::DATETIME:
                if ($value instanceof \DateTime) {
                    $value = sprintf("'%s'", $value->format('Y-m-d H:i:s'));
                } else {
                    $value = 'NULL';
                }
                break;

            case Type::STRING:
                $value = sprintf("'%s'", $value);
                break;

            case Type::DICTIONARY:
                $value = sprintf("'%s'", json_encode((is_array($value) ? $value : [])));
                break;
        }

        return $value;
    }

    public function beforeUpdate($entity)
    {
        # doesn't have default implementation
    }

    public function beforeCreate($entity)
    {
        # doesn't have default implementation
    }
}
