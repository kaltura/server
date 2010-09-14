<?php
chdir(dirname( __FILE__ ) . "/../../");
require_once("bootstrap.php");

/**
 * @package Scheduler
 * @subpackage Debug
 */
class KAsyncCurlTest extends PHPUnit_Framework_TestCase 
{
	public function testRedirectFileSize()
	{
		$curlWrapper = new KCurlWrapper('http://scoregolf.com/Web-Media/Video/2008/May/pumafinal.flv');
		$curlHeaderResponse = $curlWrapper->getHeader();
		var_dump($curlHeaderResponse);
	}
}

?>