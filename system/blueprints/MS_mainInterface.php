<?php
namespace blueprints;
interface MS_mainInterface
{
	/**
	 * @param $name  : the key to use for the magic method
	 * @param $value : the value to use for the magic method
	 *
	 * @return mixed: the interface
	 */
	public function __set($name, $value);

	/**
	 * @param $name : the key to use for the magic method
	 *
	 * @return mixed: the interface
	 */
	public function __get($name);
}