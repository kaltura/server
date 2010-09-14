<?php

/** 
 * @package Core
 * @subpackage Batch
 */
class kConvertCollectionJobData extends kConvartableJobData
{
	/**
	 * @var string
	 */
	private $destDirLocalPath;
	
	/**
	 * @var string
	 */
	private $destDirRemoteUrl;
	
	/**
	 * @var string
	 */
	private $destFileName;
	
	/**
	 * @var string
	 */
	private $inputXmlLocalPath;
	
	
	/**
	 * @var string
	 */
	private $inputXmlRemoteUrl;
	
	
	/**
	 * @var string
	 */
	private $commandLinesStr;
	
	
	/**
	 * @var array<kConvertCollectionFlavorData>
	 */
	private $flavors;
	
	/**
	 * @return the $destDirLocalPath
	 */
	public function getDestDirLocalPath()
	{
		return $this->destDirLocalPath;
	}

	/**
	 * @return the $destDirRemoteUrl
	 */
	public function getDestDirRemoteUrl()
	{
		return $this->destDirRemoteUrl;
	}

	/**
	 * @return the $destFileName
	 */
	public function getDestFileName()
	{
		return $this->destFileName;
	}

	/**
	 * @return the $inputXmlLocalPath
	 */
	public function getInputXmlLocalPath()
	{
		return $this->inputXmlLocalPath;
	}

	/**
	 * @return the $inputXmlRemoteUrl
	 */
	public function getInputXmlRemoteUrl()
	{
		return $this->inputXmlRemoteUrl;
	}


	/**
	 * @param $destDirLocalPath the $destDirLocalPath to set
	 */
	public function setDestDirLocalPath($destDirLocalPath)
	{
		$this->destDirLocalPath = $destDirLocalPath;
	}

	/**
	 * @param $destDirRemoteUrl the $destDirRemoteUrl to set
	 */
	public function setDestDirRemoteUrl($destDirRemoteUrl)
	{
		$this->destDirRemoteUrl = $destDirRemoteUrl;
	}

	/**
	 * @param $destFileName the $destFileName to set
	 */
	public function setDestFileName($destFileName)
	{
		$this->destFileName = $destFileName;
	}

	/**
	 * @param $inputXmlLocalPath the $inputXmlLocalPath to set
	 */
	public function setInputXmlLocalPath($inputXmlLocalPath)
	{
		$this->inputXmlLocalPath = $inputXmlLocalPath;
	}

	/**
	 * @param $inputXmlRemoteUrl the $inputXmlRemoteUrl to set
	 */
	public function setInputXmlRemoteUrl($inputXmlRemoteUrl)
	{
		$this->inputXmlRemoteUrl = $inputXmlRemoteUrl;
	}

	/**
	 * @return the $commandLinesStr
	 */
	public function getCommandLinesStr()
	{
		return $this->commandLinesStr;
	}

	/**
	 * @param $commandLinesStr the $commandLinesStr to set
	 */
	public function setCommandLinesStr($commandLinesStr)
	{
		$this->commandLinesStr = $commandLinesStr;
	}
	
	/**
	 * @return array<kConvertCollectionFlavorData>
	 */
	public function getFlavors()
	{
		return $this->flavors;
	}

	/**
	 * @param $flavors the $flavors to set
	 */
	public function setFlavors(array $flavors)
	{
		$this->flavors = $flavors;
	}

	/**
	 * @param kConvertCollectionFlavorData $flavor the $flavor to add
	 */
	public function addFlavor(kConvertCollectionFlavorData $flavor)
	{
		if(!is_array($this->flavors))
			$this->flavors = array();
			
		$this->flavors[] = $flavor;
	}


}

?>