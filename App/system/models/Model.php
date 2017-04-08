<?php

namespace App\system\models;

use App\system\models\properties\Property;

/**
 * Class Model: this is the model class to be extended of the model
 * @package system\models
 */
abstract class Model {

    /**
     * MS_resource name to be used for the connection
     * if no name is given the default will be used
     * @var string
     */
    protected $dataBaseConnection = NULL;

    /**
     * array filled with Property objects
     * @var array
     */
    private $fieldCollection;

    /**
     * name of the model
     * @var string
     */
    private $modelName;

    /**
     * Model constructor.
     */
    final function __construct() {
        $this->setModelName();
    }

    /**
     * @return null
     */
    public function getDataBaseConnection() {
        return $this->dataBaseConnection;
    }

    /**
     * @return mixed
     */
    public function getFieldCollection() {
        return $this->fieldCollection;
    }

    /**
     * @param \App\system\models\properties\Property $property
     *
     * @internal param \system\models\properties\Property $type type of the property
     */
    protected function addField(Property $property) {
        $this->fieldCollection[] = $property;
    }


    /**
     * we loop though the passed data and through the fields
     * will only fill the current model
     *
     * @param $data : fill the model with an associate array
     *
     * @throws \Exception
     */
    public function fillModel($data) {
        foreach ($data as $name => $item) {
            foreach ($this->fieldCollection as $field) {
                if ($field->name == $name) {
                    $this->fillProperty($field, $item);
                    break;
                }
            }
        }
    }

    /**
     * @param \App\system\models\properties\Property      $name
     * @param                                             $data
     */
    private function fillProperty(Property $name, $data) {
        $name->setValue($data);
        $name->validateProperty();
    }

    /**
     * this function will set the model name
     */
    private function setModelName() {
        $modelInformation = new \ReflectionClass($this);
        $this->modelName = $modelInformation->getShortName();
    }

    /**
     * @return string
     */
    public function getLongModelName() {
        return $this->modelName;
    }

    /**
     * @return string
     */
    public function getShortModelName() {
        return str_replace("Model","",$this->modelName);
    }

    /**
     * will return true if the object properties match
     *
     * @param \App\system\models\Model $otherModel
     *
     * @return bool
     */
    public final function compare(Model $otherModel){
        if($this == $otherModel){
            return TRUE;
        }
        else{
            return FALSE;
        }
    }

    /**
     * within this method you may setup the model
     * @return mixed
     */
    abstract public function up();

    /**
     * within this method you may setup the model
     * @return mixed
     */
}