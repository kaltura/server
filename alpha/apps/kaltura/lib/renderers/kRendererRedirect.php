<?php

require_once(dirname(__file__) . '/kRendererBase.php');

/*
 * @package server-infra
 * @subpackage renderers
 */
class kRendererRedirect implements kRendererBase
{
	private $url;
	private $statusCode;
	public function __construct($url,$statusCode = KCurlHeaderResponse::HTTP_STATUS_REDIRECT_METHOD)
	{
		$this->url = $url;
		$this->statusCode = $statusCode;
	}
	
	public function validate()
	{
		return true;
	}
	
	public function output()
	{
		header("Location: $this->url" , true, $this->statusCode);
	}
}
