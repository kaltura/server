<?php
require_once("tests/bootstrapTests.php");

class CountryRestrictionTests extends PHPUnit_Framework_TestCase 
{
	public function testAllowList()
	{
		$accessControl = $this->prepateAccessControlWithScopeForIp("64.208.37.74"); // google bot (us))
		
		$restriction = new countryRestriction();
		$restriction->setAccessControl($accessControl);
		$restriction->setType(countryRestriction::COUNTRY_RESTRICTION_TYPE_ALLOW_LIST);
		 
		$restriction->setCountryList("US");
		$this->assertTrue($restriction->isValid());
		
		$restriction->setCountryList("FR");
		$this->assertFalse($restriction->isValid());
		
		$restriction->setCountryList("DE,US,FR");
		$this->assertTrue($restriction->isValid());
		
		$restriction->setCountryList("DE,FR");
		$this->assertFalse($restriction->isValid());
	}
	
	public function testDenyList()
	{
		$accessControl = $this->prepateAccessControlWithScopeForIp("217.150.156.17"); // Deutsche Telekom (DE)
		
		$restriction = new countryRestriction();
		$restriction->setAccessControl($accessControl);
		$restriction->setType(countryRestriction::COUNTRY_RESTRICTION_TYPE_RESTRICT_LIST);
		 
		$restriction->setCountryList("DE");
		$this->assertFalse($restriction->isValid());
		
		$restriction->setCountryList("FR");
		$this->assertTrue($restriction->isValid());
		
		$restriction->setCountryList("DE,US,FR");
		$this->assertFalse($restriction->isValid());
		
		$restriction->setCountryList("US,FR");
		$this->assertTrue($restriction->isValid());
	}
	
	private function prepateAccessControlWithScopeForIp($ip)
	{
		$_SERVER["REMOTE_ADDR"] = $ip;
		$scope = accessControlScope::partialInit();
		$accessControl = new accessControl();
		$accessControl->setScope($scope);
		return $accessControl;
	}
}
?>