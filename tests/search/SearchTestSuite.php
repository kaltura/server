<?php
require_once("tests/bootstrapTests.php");

class SearchTestSuite extends KalturaAutomaticTestSuite
{
	public function __construct()
	{
		parent::__construct(__CLASS__, pathinfo(__FILE__, PATHINFO_DIRNAME));	
	}
	
	public static function suite ()
    {
        return new self();
    }
}
