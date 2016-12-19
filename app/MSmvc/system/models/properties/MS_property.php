<?php

namespace MSmvc\system\models\properties;

/**
 * Class MS_property: this abstract class will be used as a blueprint for the database field
 * @package system\models\properties
 */
abstract class MS_property {
    public $name;
    public $length = 25;
    public $type = 'varchar';
    public $default = NULL;
    public $collation;
    public $attributes;
    public $notNull = FALSE;
    public $externalResourceTable = NULL;
    protected $autoIncrement;
    public $value;

    /**
     * @return bool: true if the validation is correct
     * @throws \Exception: exception of the type invalidPropertyException
     */
    abstract function validateProperty();

}