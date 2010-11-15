<?php

/** 
 * @package Core
 * @subpackage Batch
 */
class kRemoteConvertJobData extends kConvartableJobData
{
	/**
	 * @var string
	 */
	private $srcFileUrl;
	
	/**
	 * Should be set by the API
	 * 
	 * @var string
	 */
	private $destFileUrl;
	
	
	
	/**
	 * @return the $srcFileUrl
	 */
	public function getSrcFileUrl()
	{
		return $this->srcFileUrl;
	}

	/**
	 * @return the $destFileUrl
	 */
	public function getDestFileUrl()
	{
		return $this->destFileUrl;
	}

	/**
	 * @param $srcFileUrl the $srcFileUrl to set
	 */
	public function setSrcFileUrl($srcFileUrl)
	{
		$this->srcFileUrl = $srcFileUrl;
	}

	/**
	 * @param $destFileUrl the $destFileUrl to set
	 */
	public function setDestFileUrl($destFileUrl)
	{
		$this->destFileUrl = $destFileUrl;
	}



}
