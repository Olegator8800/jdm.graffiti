<?php

namespace Jdm\Graffiti\Entity;

use Jdm\Graffiti\Entity\Picture;
use Jdm\Graffiti\Persistence\AbstractRepository;
use Jdm\Graffiti\Persistence\FieldDescription;
use Jdm\Graffiti\Persistence\Type;
use DateTime;

class PictureRepository extends AbstractRepository
{
    /**
     * @param  int $id
     *
     * @return Picture|null
     */
    public function findById($id)
    {
        $sql = sprintf('SELECT * FROM %s WHERE id = %s LIMIT 1', $this->getTableName(), intval($id));

        $statement = $this->connection->query($sql);

        $result = $statement->fetch();

        if (empty($result)) {
            return null;
        }

        return $this->createEntity($result);
    }

    /**
     * @param  integer $page
     * @param  int     $limit
     * @param  array   $sort
     *
     * @return Picture[]
     */
    public function findAll($page = 1, $limit = null, $sort = [])
    {
        $sql = sprintf('SELECT * FROM %s', $this->getTableName());
        $sql .= $this->prepareSort($sort);
        $sql .= $this->prepareLimit($limit, $page);

        $statement = $this->connection->query($sql);

        $result = [];

        while ($order = $statement->fetch()) {
            $result[] = $this->createEntity($order);
        }

        return $result;
    }

    /**
     * @return string
     */
    protected function getTableName()
    {
        return 'jdm_graffiti';
    }

    /**
     * @return string
     */
    protected function getEntityClassName()
    {
        return 'Jdm\Graffiti\Entity\Picture';
    }

    /**
     * @return FieldDescription[]
     */
    protected function getFieldDescriptions()
    {
        return [
            new FieldDescription('id',        'id',         Type::INTEGER),
            new FieldDescription('name',      'name',       Type::STRING),
            new FieldDescription('password',  'password',   Type::STRING),
            new FieldDescription('salt',      'salt',       Type::STRING),
            new FieldDescription('createdAt', 'created_at', Type::DATETIME),
            new FieldDescription('updatedAt', 'updated_at', Type::DATETIME),
        ];
    }
}
