<?php
session_start();

class Session
{
	private static $session;
	public static  $driver = ['driver' => 'file', 'location' => '/storage/sessions'];

	public function driverInteraction() {
		switch(self::$driver['driver']) {
			case 'file':
				session_save_path(self::$driver['location']);
				break;
		}
	}

	public static function add($key, $value) {

	}
	//todo: we make a dataset of the current dataset and then we manipulate it and let the pipeline set the header just before calling the view page
}