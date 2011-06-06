<?php

class exampleTest extends PHPUnit_Framework_TestCase{
	public function testExample1(){ 
			$res = 0;
		
			//assert that 0 errors were generated
			$this->assertEquals(0, $res);
	}
}
