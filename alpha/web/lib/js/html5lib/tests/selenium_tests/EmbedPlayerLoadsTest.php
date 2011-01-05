<?php

if (!defined('MEDIAWIKI') || !defined('SELENIUMTEST')) {
	echo "This script cannot be run standalone";
	exit(1);
}

// create test suite
$wgSeleniumTestSuites['EmbedPlayerLoadingTests'] = new SeleniumTestSuite('Embed Player Loading Test Suite');
$wgSeleniumTestSuites['EmbedPlayerLoadingTests']->addTest(new EmbedPlayerLoads());
	
class EmbedPlayerLoads extends SeleniumTestCase
{
	public $name = "Embed Player Loading Test";

	public function runTest()
	{
    global $wgSeleniumTestsWikiUrl;
    $this->open($wgSeleniumTestsWikiUrl.'/modules/EmbedPlayer/tests/Player_Themeable.html');
    
    $this->waitForPageToLoad(10000);
    
    $this->isElementPresent("//div[@class='mwplayer_interface k-player']", 10000);
    $this->isElementPresent("//div[@class='mwplayer_interface mv-player']", 10000);
    $this->isElementPresent("//div[@class='ui-state-default play-btn-large']", 10000);

	}

}
