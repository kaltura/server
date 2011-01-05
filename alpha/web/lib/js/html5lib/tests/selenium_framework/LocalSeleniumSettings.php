<?php

require_once 'selenium_tests/EmbedPlayerLoadingTest.php';

/** List of Selenium test classes. These must be registered with the autoloader. */
$wgSeleniumTests = array(
	'EmbedPlayerLoads'
);

/** Hostname of selenium server */
$wgSeleniumTestsSeleniumHost = 'localhost';

/** URL of the wiki to be tested. By default, the local wiki is used. */
$wgSeleniumTestsWikiUrl = 'http://mwEmbed';

/** Port used by selenium server. */
$wgSeleniumServerPort = 4444;

/** Wiki login username. Used by Selenium to log onto the wiki. */
$wgSeleniumTestsWikiUser      = 'Wikiuser';

/** Wiki login password. Used by Selenium to log onto the wiki. */
$wgSeleniumTestsWikiPassword  = '';

/** Actually, use this browser */
$wgSeleniumTestsUseBrowser = 'firefox';


// Hostname of selenium server
//$wgSeleniumTestsSeleniumHost = 'grid.tesla.usability.wikimedia.org';

// URL of the wiki to be tested. Consult web server configuration.
//$wgSeleniumTestsWikiUrl = 'http://prototype.wikimedia.org/mwe-gadget-testing';

// Port used by selenium server (optional - default is 4444)
$wgSeleniumServerPort = 4444;

// Wiki login. Used by Selenium to log onto the wiki
$wgSeleniumTestsWikiUser      = 'Wikisysop';
$wgSeleniumTestsWikiPassword  = 'password';

// Common browsers on Windows platform. Modify for other platforms or
// other Windows browsers
// Use the *chrome handler in order to be able to test file uploads
// further solution suggestions: http://www.brokenbuild.com/blog/2007/06/07/testing-file-uploads-with-selenium-rc-and-firefoxor-reducing-javascript-security-in-firefox-for-fun-and-profit/
// $wgSeleniumTestsBrowsers['firefox']   = '*firefox c:\\Program Files (x86)\\Mozilla Firefox\\firefox.exe';
/** $wgSeleniumTestsBrowsers['osx-firefox']   = 'Firefox on OS X Snow Leopard';
$wgSeleniumTestsBrowsers['win-opera']   = 'Opera on Windows';
$wgSeleniumTestsBrowsers['win-chrome']   = 'Google Chrome on Windows';
$wgSeleniumTestsBrowsers['osx-safari']   = 'Safari on OS X Snow Leopard';
$wgSeleniumTestsBrowsers['win-ff35']   = 'Firefox 3.5 on Windows';
$wgSeleniumTestsBrowsers['osx-opera']   = 'Opera on OS X Snow Leopard';
$wgSeleniumTestsBrowsers['osx-chrome']   = 'Google Chrome on OS X Snow Leopard';
$wgSeleniumTestsBrowsers['lin-ff35']   = 'Firefox 3.5 on Linux';
$wgSeleniumTestsBrowsers['lin-ff36']   = 'Firefox 3.6 on Linux';
$wgSeleniumTestsBrowsers['win-safari']   = 'Safari on Windows';
$wgSeleniumTestsBrowsers['lin-ff3']   = 'Firefox 3 on Linux';
$wgSeleniumTestsBrowsers['win-ie8']   = 'IE 8 on Windows';
//$wgSeleniumTestsBrowsers['win-ie']   = 'IE on Windows';

 * Common browsers on Windows platform. Modify for other platforms or
 * other Windows browsers.
 * Use the *chrome handler in order to be able to test file uploads.
 * Further solution suggestions: http://www.brokenbuild.com/blog/2007/06/07/testing-file-uploads-with-selenium-rc-and-firefoxor-reducing-javascript-security-in-firefox-for-fun-and-profit/
 */
$wgSeleniumTestsBrowsers = array(
	'firefox' => '*firefox /usr/bin/firefox',
	'iexplorer' => '*iexploreproxy',
	'opera' => '*chrome /usr/bin/opera',
);


// Actually, use this browser
//$wgSeleniumTestsUseBrowser = 'osx-chrome';

// Set command line mode
$wgSeleniumTestsRunMode = 'cli';

?>
