<?php
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
	
	
}