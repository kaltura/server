<?php
require_once("tests/bootstrapTests.php");

class SiteRestrictionTests extends PHPUnit_Framework_TestCase 
{
	/**
	 * Check that invalid domains cannot be set
	 */
	public function testDomainNamesValidation()
	{
		$restriction = new siteRestriction();
		$restriction->setSiteList("facebook.com");
		$this->assertEquals("facebook.com", $restriction->getSiteList());
		
		$restriction->setSiteList("www.facebook.com");
		$this->assertEquals("www.facebook.com", $restriction->getSiteList());
		
		$restriction->setSiteList("org"); 
		$this->assertEquals("org", $restriction->getSiteList());
		
		$restriction->setSiteList(".org"); 
		$this->assertEquals("", $restriction->getSiteList());
		
		$restriction->setSiteList("http://www.facebook.com/");
		$this->assertEquals("", $restriction->getSiteList());
		
		$restriction->setSiteList("http://www.facebook.com");
		$this->assertEquals("", $restriction->getSiteList());
		
		$restriction->setSiteList("http://www.facebook.com/, facebook.com"); // should ignore if one domain is invalid, but use the other
		$this->assertEquals("facebook.com", $restriction->getSiteList());
		
		$restriction->setSiteList("www.facebook.com,www.facebook.com"); // should concat the same values
		$this->assertEquals("www.facebook.com", $restriction->getSiteList());
		
		$restriction->setSiteList("www1.facebook.com, www.facebook.com,facebook.com"); // space should be removed
		$this->assertEquals("www1.facebook.com,www.facebook.com,facebook.com", $restriction->getSiteList());
	}
	
	/**
	 * Check that domain is stripped correctly from referrer url 
	 */
	public function testReferrerDomainStriping()
	{
		$restriction = new siteRestriction();
		$restriction->setAccessControl(new accessControl());
		
		$restriction->setType(siteRestriction::SITE_RESTRICTION_TYPE_ALLOW_LIST);
		$restriction->setSiteList("facebook.com");
		$restriction->getAccessControlScope()->setReferrer("http://facebook.com/");
		$this->assertTrue($restriction->isValid());
		
		$restriction->setSiteList("www.facebook.com");
		$restriction->getAccessControlScope()->setReferrer("http://www.facebook.com/");
		$this->assertTrue($restriction->isValid());
		
		$restriction->setSiteList("www.facebook.com");
		$restriction->getAccessControlScope()->setReferrer("http://wwwfacebookcom/");
		$this->assertFalse($restriction->isValid());
		
		$restriction->setSiteList("www.facebook.com");
		$restriction->getAccessControlScope()->setReferrer("www.facebook.com");
		$this->assertTrue($restriction->isValid());
		
		$restriction->setSiteList("www.facebook.com");
		$restriction->getAccessControlScope()->setReferrer("www.facebook.com/");
		$this->assertTrue($restriction->isValid());
		
		$restriction->setSiteList("www.facebook.com");
		$restriction->getAccessControlScope()->setReferrer("http://www.facebook.com");
		$this->assertTrue($restriction->isValid());
	}
	
	/**
	 * Allow list has www.facebook.com, referrer is www.facebook.com  (should allow)
	 */
	public function testAllowListWhenExactDomain()
	{
		$restriction = new siteRestriction();
		$restriction->setAccessControl(new accessControl());
		$restriction->setType(siteRestriction::SITE_RESTRICTION_TYPE_ALLOW_LIST);
		$restriction->setSiteList("www.facebook.com");
		$restriction->getAccessControlScope()->setReferrer("http://www.facebook.com/");
		$this->assertTrue($restriction->isValid());
	}
	
	/**
	 * Allow list has facebook.com, referrer is www.facebook.com (should allow)
	 * Allow list has facebook.com, referrer is fakefaceook.com (should deny)
	 */
	public function testAllowListWhenReferrerIsDomainSiteIsSubDomain()
	{
		$restriction = new siteRestriction();
		$restriction->setAccessControl(new accessControl());
		$restriction->setType(siteRestriction::SITE_RESTRICTION_TYPE_ALLOW_LIST);
		$restriction->setSiteList("facebook.com");
		$restriction->getAccessControlScope()->setReferrer("http://www.facebook.com/");
		$this->assertTrue($restriction->isValid());
		
		$restriction->setSiteList("facebook.com");
		$restriction->getAccessControlScope()->setReferrer("http://fakefacebook.com/");
		$this->assertFalse($restriction->isValid());
	}
	
	/**
	 * Allow list has www.facebook.com, referrer is facebook.com (should deny)
	 */
	public function testAllowListWhenReferrerIsSubDomainSiteIsDomain()
	{
		$restriction = new siteRestriction();
		$restriction->setAccessControl(new accessControl());
		$restriction->setType(siteRestriction::SITE_RESTRICTION_TYPE_ALLOW_LIST);
		$restriction->setSiteList("www.facebook.com");
		$restriction->getAccessControlScope()->setReferrer("http://facebook.com/");
		$this->assertFalse($restriction->isValid());
	}
	
	/**
	 * Allow list when no referrer
	 */
	public function testAllowListWhenNoReferrer()
	{
		$restriction = new siteRestriction();
		$restriction->setAccessControl(new accessControl());
		$restriction->setType(siteRestriction::SITE_RESTRICTION_TYPE_ALLOW_LIST);
		$restriction->setSiteList("facebook.com");
		$restriction->getAccessControlScope()->setReferrer(null);
		$this->assertFalse($restriction->isValid());
		
		$restriction = new siteRestriction();
		$restriction->setAccessControl(new accessControl());
		$restriction->setType(siteRestriction::SITE_RESTRICTION_TYPE_ALLOW_LIST);
		$restriction->setSiteList("facebook.com");
		$restriction->getAccessControlScope()->setReferrer("");
		$this->assertFalse($restriction->isValid());
	}
	
	/**
	 * Allow when list is empty (should deny)
	 */
	public function testAllowListWhenListIsEmpty()
	{
		$restriction = new siteRestriction();
		$restriction->setAccessControl(new accessControl());
		$restriction->setType(siteRestriction::SITE_RESTRICTION_TYPE_ALLOW_LIST);
		$restriction->setSiteList(null);
		$restriction->getAccessControlScope()->setReferrer("http://www.facebook.com/");
		$this->assertFalse($restriction->isValid());
		
		$restriction = new siteRestriction();
		$restriction->setAccessControl(new accessControl());
		$restriction->setType(siteRestriction::SITE_RESTRICTION_TYPE_ALLOW_LIST);
		$restriction->setSiteList("");
		$restriction->getAccessControlScope()->setReferrer("http://www.facebook.com/");
		$this->assertFalse($restriction->isValid());
	}
	
	/**
	 * Allow list has couple domains
	 */
	public function testAllowListWithCoupleDomains()
	{
		$restriction = new siteRestriction();
		$restriction->setAccessControl(new accessControl());
		$restriction->setType(siteRestriction::SITE_RESTRICTION_TYPE_ALLOW_LIST);
		$restriction->setSiteList("www.myspace.com,www.facebook.com");
		
		$restriction->getAccessControlScope()->setReferrer("http://www.facebook.com/");
		$this->assertTrue($restriction->isValid());
		
		$restriction->getAccessControlScope()->setReferrer("http://www.myspace.com/");
		$this->assertTrue($restriction->isValid());
		
		$restriction->getAccessControlScope()->setReferrer("http://facebook.com/");
		$this->assertFalse($restriction->isValid());
		
		$restriction->getAccessControlScope()->setReferrer("http://www.google.com/");
		$this->assertFalse($restriction->isValid());
	}
	
	/**
	 * Restrict list has www.facebook.com, referrer is www.facebook.com  (should deny)
	 */
	public function testRestrictListWhenExactDomain()
	{
		$restriction = new siteRestriction();
		$restriction->setAccessControl(new accessControl());
		$restriction->setType(siteRestriction::SITE_RESTRICTION_TYPE_RESTRICT_LIST);
		$restriction->setSiteList("www.facebook.com");
		$restriction->getAccessControlScope()->setReferrer("http://www.facebook.com/");
		$this->assertFalse($restriction->isValid());
	}
	
	/**
	 * Restrict list has facebook.com, referrer is www.facebook.com (should deny)
	 * Restrict list has facebook.com, referrer is fakefaceook.com (should allow)
	 */
	public function testRestrictListWhenReferrerIsDomainSiteIsSubDomain()
	{
		$restriction = new siteRestriction();
		$restriction->setAccessControl(new accessControl());
		$restriction->setType(siteRestriction::SITE_RESTRICTION_TYPE_RESTRICT_LIST);
		$restriction->setSiteList("facebook.com");
		$restriction->getAccessControlScope()->setReferrer("http://www.facebook.com/");
		$this->assertFalse($restriction->isValid());
		
		$restriction->setSiteList("facebook.com");
		$restriction->getAccessControlScope()->setReferrer("http://fakefacebook.com/");
		$this->assertTrue($restriction->isValid());
	}
	
	/**
	 * Restrict list has www.facebook.com, referrer is facebook.com (should allow)
	 */
	public function testRestrictListWhenReferrerIsSubDomainSiteIsDomain()
	{
		$restriction = new siteRestriction();
		$restriction->setAccessControl(new accessControl());
		$restriction->setType(siteRestriction::SITE_RESTRICTION_TYPE_RESTRICT_LIST);
		$restriction->setSiteList("www.facebook.com");
		$restriction->getAccessControlScope()->setReferrer("http://facebook.com/");
		$this->assertTrue($restriction->isValid());
	}
	
	/**
	 * Restrict list when no referrer
	 */
	public function testRestrictListWhenNoReferrer()
	{
		$restriction = new siteRestriction();
		$restriction->setAccessControl(new accessControl());
		$restriction->setType(siteRestriction::SITE_RESTRICTION_TYPE_RESTRICT_LIST);
		$restriction->setSiteList("facebook.com");
		$restriction->getAccessControlScope()->setReferrer(null);
		$this->assertFalse($restriction->isValid());
		
		$restriction = new siteRestriction();
		$restriction->setAccessControl(new accessControl());
		$restriction->setType(siteRestriction::SITE_RESTRICTION_TYPE_RESTRICT_LIST);
		$restriction->setSiteList("facebook.com");
		$restriction->getAccessControlScope()->setReferrer("");
		$this->assertFalse($restriction->isValid());
	}
	
	/**
	 * Restrict when list is empty (should allow)
	 */
	public function testRestrictListWhenListIsEmpty()
	{
		$restriction = new siteRestriction();
		$restriction->setAccessControl(new accessControl());
		$restriction->setType(siteRestriction::SITE_RESTRICTION_TYPE_RESTRICT_LIST);
		$restriction->setSiteList(null);
		$restriction->getAccessControlScope()->setReferrer("http://www.facebook.com/");
		$this->assertTrue($restriction->isValid());
		
		$restriction = new siteRestriction();
		$restriction->setAccessControl(new accessControl());
		$restriction->setType(siteRestriction::SITE_RESTRICTION_TYPE_RESTRICT_LIST);
		$restriction->setSiteList("");
		$restriction->getAccessControlScope()->setReferrer("http://www.facebook.com/");
		$this->assertTrue($restriction->isValid());
	}
	
	/**
	 * Restrict list has couple domains
	 */
	public function testRestrictListWithCoupleDomains()
	{
		$restriction = new siteRestriction();
		$restriction->setAccessControl(new accessControl());
		$restriction->setType(siteRestriction::SITE_RESTRICTION_TYPE_RESTRICT_LIST);
		$restriction->setSiteList("www.myspace.com,www.facebook.com");
		
		$restriction->getAccessControlScope()->setReferrer("http://www.facebook.com/");
		$this->assertFalse($restriction->isValid());
		
		$restriction->getAccessControlScope()->setReferrer("http://www.myspace.com/");
		$this->assertFalse($restriction->isValid());
		
		$restriction->getAccessControlScope()->setReferrer("http://facebook.com/");
		$this->assertTrue($restriction->isValid());
		
		$restriction->getAccessControlScope()->setReferrer("http://www.google.com/");
		$this->assertTrue($restriction->isValid());
	}
}
?>