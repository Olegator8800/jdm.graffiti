<?php

namespace Jdm\Graffiti\Persistence;

class FieldDescription
{
    /**
     * @var string
     */
    private $propertyName;

    /**
     * @var string
     */
    private $fieldName;

    /**
     * @var string
     */
    private $type;

    /**
     * @param string $propertyName
     * @param string $fieldName
     * @param string $type
     */
    public function __construct($propertyName, $fieldName, $type)
    {
        $this->fieldName = $fieldName;
        $this->propertyName = $propertyName;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @return string
     */
    public function getPropertyName()
    {
        return $this->propertyName;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
