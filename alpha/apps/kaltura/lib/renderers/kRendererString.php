<?php

require_once(dirname(__file__) . '/../request/infraRequestUtils.class.php');
require_once(dirname(__file__) . '/kRendererBase.php');

/*
 * @package server-infra
 * @subpackage renderers
 */
class kRendererString implements kRendererBase
{
	protected $content;
	protected $contentType;
	protected $maxAge;
	protected $lastModified;
	protected $errorStatus;
	
	public function __construct($content, $contentType, $maxAge = 8640000, $lastModified = null, $errorStatus = null)
	{
		$this->content = $content;
		$this->contentType = $contentType;
		$this->maxAge = $maxAge;
		$this->lastModified = $lastModified;
		$this->errorStatus = $errorStatus;
	}
	
	public function validate()
	{
		return true;
	}
	
	public function output()
	{
		header('Content-Length: '.strlen($this->content));
		if ($this->contentType)
			header('Content-Type: '.$this->contentType);
		header("Access-Control-Allow-Origin:*");
		if ($this->errorStatus)
		{
			header('HTTP/1.1 500 Internal Server Error');
		}
		
		infraRequestUtils::sendCachingHeaders($this->maxAge, false, $this->lastModified);

		echo $this->content;
	}
}
