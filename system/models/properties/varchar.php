<?php
namespace system\models\properties;

class varchar extends MS_property{

	/**
	 * @return bool
	 * @throws \Exception
	 */

	function validateProperty(){
		if(is_string(strval($this->value))){
			return true;
		}
		else{
			throw new \Exception('the property is invalid');
		}
		// TODO: Implement validateProperty() method.
	}
}