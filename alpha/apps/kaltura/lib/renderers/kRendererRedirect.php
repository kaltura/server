<?php

require_once(dirname(__file__) . '/kRendererBase.php');

/*
 * @package server-infra
 * @subpackage renderers
 */
class kRendererRedirect implements kRendererBase
{
	public $url;
	
	public function __construct($url)
	{
		$this->url = $url;
	}
	
	public function validate()
	{
		return true;
	}
	
	public function output()
	{
		header("Location: {$this->url}");
	}
}
