<?php
require_once(dirname(__FILE__) . '/../../bootstrap.php');

class thumbnailTest extends PHPUnit_Framework_TestCase
{
	public function testXsd()
	{
		$path = realpath(__DIR__ . '/xsd');
		$xsdDir = dir($path);
		while (false !== ($entry = $xsdDir->read())) 
		{
			if($entry == '.' || $entry == '..' || $entry == '.svn')
				continue;
				
			if(is_dir("$path/$entry"))
				$this->doTest(realpath("$path/$entry"));
		}
		$xsdDir->close();
	}
	
	public function doTest($path)
	{
		$xsdFiles = array();
		$xsdDir = dir($path);
		while (false !== ($entry = $xsdDir->read())) 
		{
			$matches = null;
			if(!preg_match('/(\d+)\.xsd/', $entry, $matches))
				continue;
				
			$version = $matches[1];
			$xsdFiles[$version] = realpath("$path/$entry");
		}
		$xsdDir->close();
		$this->assertGreaterThan(0, count($xsdFiles), "Folder [$path] has no XSD files");
		
		$versions = array_keys($xsdFiles);
		sort($versions);
		$prevVersion = array_shift($versions);
		foreach($versions as $currentVersion)
		{
			$fromXsd = $xsdFiles[$prevVersion];
			$toXsd = $xsdFiles[$currentVersion];
			
			$xsl = kXsd::compareXsd($fromXsd, $toXsd);
			$this->assertTrue($xsl, "XSD [$fromXsd => $toXsd] Created XSL");
			
			$prevVersion = $currentVersion;
		}
	}
}