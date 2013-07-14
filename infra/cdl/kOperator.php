<?php
/**
 * @package infra
 * @subpackage Conversion
 */
class kOperator
{
	public function __construct($opr=null)
	{
		if($opr==null){
//			KalturaLog::info("no input");
			return;
		}
//		KalturaLog::info(print_r($opr,true));
		$this->id = $opr->id;
		$this->extra = isset($opr->extra) ? $opr->extra : null;
		$this->command = isset($opr->command) ? $opr->command : null;
		$this->config = isset($opr->config) ? $opr->config : null;
		$this->params = isset($opr->params) ? $opr->params : null;
		$this->className = isset($opr->className) ? $opr->className : null;
		$this->isOptional = isset($opr->isOptional) ? $opr->isOptional : null;
	}
	
	/**
	 * @var int
	 */
	public $id;
	
	/**
	 * @var string
	 */
	public $extra;
	
	/**
	 * @var string
	 */
	public $command;
	
	/**
	 * @var string
	 */
	public $config;
	
	/**
	 * @var int
	 */
	public $extracMediaEnabled=1;
	
	/**
	 * @var int
	 */
	public $thumbEnabled=1;
	
	/**
	 * Operator params to override the 'global' flavor params settings.
	 * Initaly used for MAC 'pre-conversion'.
	 * @var string
	 */
	public $params;

	/**
	 * @var string
	 */
	public $className;
	
	/**
	 * @var int - when set to 1, the operator will not cause a failure even when the actual activation fails.
	 */
	public $isOptional=0;
}