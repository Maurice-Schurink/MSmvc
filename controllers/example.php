<?php

namespace controllers;

use system\MS_controller;

class example extends MS_controller
{
	/**
	 * @return mixed: the html page to return
	 */
	public function index() {
		return $this->view('example',['test'=>'works']);
	}

	/**
	 * @return bool: we return true because the framework booted successful if it could execute this.
	 */
	public function phpUnit() {
		return TRUE;
	}
}