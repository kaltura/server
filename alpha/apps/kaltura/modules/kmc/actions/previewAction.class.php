<?php
/**
 * @package Core
 * @subpackage KMC
 */
class previewAction extends kalturaAction
{
	public function execute()
	{
		// Preview page moved into /extwidget/preview
		$url = '/index.php';
		$url .= str_replace('/kmc', '/extwidget', $_SERVER['PATH_INFO']);
		$url .= '?' . $_SERVER['QUERY_STRING'];
		header("location: $url");
	}
}