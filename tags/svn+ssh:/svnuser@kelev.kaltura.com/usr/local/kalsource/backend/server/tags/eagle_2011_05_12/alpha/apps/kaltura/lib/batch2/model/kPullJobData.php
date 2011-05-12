<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kPullJobData extends kJobData
{
	/**
	 * @var string
	 */
	private $srcFileUrl;
	
	/**
	 * @var string
	 */
	private $destFileLocalPath;
	
	
	
	/**
	 * @return the $srcFileUrl
	 */
	public function getSrcFileUrl()
	{
		return $this->srcFileUrl;
	}

	/**
	 * @return the $destFileLocalPath
	 */
	public function getDestFileLocalPath()
	{
		return $this->destFileLocalPath;
	}

	/**
	 * @param $srcFileUrl the $srcFileUrl to set
	 */
	public function setSrcFileUrl($srcFileUrl)
	{
		$this->srcFileUrl = $srcFileUrl;
	}

	/**
	 * @param $destFileLocalPath the $destFileLocalPath to set
	 */
	public function setDestFileLocalPath($destFileLocalPath)
	{
		$this->destFileLocalPath = $destFileLocalPath;
	}

	
	
}
