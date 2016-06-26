<?php

class TestsAutoloader
{
	static public function autoload($class)
	{
		$classPath = '/../' . str_replace('_', '/', $class) . '.php';
		require_once(dirname(__file__) . $classPath);
	}

	static public function register()
	{
		spl_autoload_register(array("TestsAutoloader", "autoload"));
	}
}
