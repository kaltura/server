<?php
require_once(dirname(__file__) . '/../request/infraRequestUtils.class.php');
require_once(dirname(__file__) . '/kRendererBase.php');
/*
 * @package server-infra
* @subpackage renderers
*/
class kRendererDieError implements kRendererBase
{
	/**
	 * 
	 * @var string
	 */
	private $code;
	
	/**
	 *
	 * @var string
	 */
	private $message;
	
	public function __construct(KalturaAPIException $e)
	{
	KalturaLog::debug(print_r($e));
		$this->code = $e->getCode();
		$this->message = $e->getMessage();
	}
	
	public function validate()
	{
		return true;
	}
	
	public function output()
	{
		header('X-Kaltura:error- ' . $this->code);
		header("X-Kaltura-App: exiting on error {$this->code} - {$this->message}");
		
		KExternalErrors::dieGracefully();
	}
}
