<?php

/**
 *  
 * @package Core
 * @subpackage Batch
 */
class kConvartableJobData
{
	/**
	 * @var string
	 */
	private $srcFileSyncLocalPath;
	
	/**
	 * The translated path as used by the scheduler
	 * @var string
	 */
	private $actualSrcFileSyncLocalPath;
	
	/**
	 * @var string
	 */
	private $srcFileSyncRemoteUrl;
	
	/**
	 * @var int
	 */
	private $engineVersion;
	
	/**
	 * @var int
	 */
	private $flavorParamsOutputId;
	
	/**
	 * @var flavorParamsOutput
	 */
	private $flavorParamsOutput;
	
	/**
	 * @var int
	 */
	private $mediaInfoId;
	
	/**
	 * @var int
	 */
	private $currentOperationSet = 0;
	
	/**
	 * @var int
	 */
	private $currentOperationIndex = 0;
	
	

	/**
	 * @return the $srcFileSyncLocalPath
	 */
	public function getSrcFileSyncLocalPath()
	{
		return $this->srcFileSyncLocalPath;
	}
	
	/**
	 * @param $srcFileSyncRemoteUrl the $srcFileSyncRemoteUrl to set
	 */
	public function setSrcFileSyncRemoteUrl($srcFileSyncRemoteUrl)
	{
		$this->srcFileSyncRemoteUrl = $srcFileSyncRemoteUrl;
	}

	/**
	 * @return the $srcFileSyncRemoteUrl
	 */
	public function getSrcFileSyncRemoteUrl()
	{
		return $this->srcFileSyncRemoteUrl;
	}

	/**
	 * @param $flavorParamsOutput the $flavorParamsOutput to set
	 */
	public function setFlavorParamsOutput($flavorParamsOutput)
	{
		$this->flavorParamsOutput = $flavorParamsOutput;
	}

	/**
	 * @param $srcFileSyncLocalPath the $srcFileSyncLocalPath to set
	 */
	public function setSrcFileSyncLocalPath($srcFileSyncLocalPath)
	{
		$this->srcFileSyncLocalPath = $srcFileSyncLocalPath;
	}

	/**
	 * @return the $actualSrcFileSyncLocalPath
	 */
	public function getActualSrcFileSyncLocalPath()
	{
		return $this->actualSrcFileSyncLocalPath;
	}

	/**
	 * @param $actualSrcFileSyncLocalPath the $actualSrcFileSyncLocalPath to set
	 */
	public function setActualSrcFileSyncLocalPath($actualSrcFileSyncLocalPath)
	{
		$this->actualSrcFileSyncLocalPath = $actualSrcFileSyncLocalPath;
	}
	
	/**
	 * @param $flavorParamsOutputId the $flavorParamsOutputId to set
	 */
	public function setFlavorParamsOutputId($flavorParamsOutputId)
	{
		$this->flavorParamsOutputId = $flavorParamsOutputId;
	}

	/**
	 * @return flavorParamsOutput the $flavorParamsOutput
	 */
	public function getFlavorParamsOutput()
	{
		return $this->flavorParamsOutput;
	}

	/**
	 * @return int the $flavorParamsOutputId
	 */
	public function getFlavorParamsOutputId()
	{
		return $this->flavorParamsOutputId;
	}


	/**
	 * @return int the $mediaInfoId
	 */
	public function getMediaInfoId()
	{
		return $this->mediaInfoId;
	}

	/**
	 * @param $mediaInfoId the $mediaInfoId to set
	 */
	public function setMediaInfoId($mediaInfoId)
	{
		$this->mediaInfoId = $mediaInfoId;
	}

	/**
	 * @return the ready behavior
	 */
	public function getReadyBehavior()
	{
		$flavorParamsOutput = flavorParamsOutputPeer::retrieveByPK($this->flavorParamsOutputId);
		if($flavorParamsOutput)
			return $flavorParamsOutput->getReadyBehavior();
			
		return null;
	}
	
	/**
	 * @return the $currentOperationSet
	 */
	public function getCurrentOperationSet()
	{
		return $this->currentOperationSet;
	}

	/**
	 * @return the $currentOperationIndex
	 */
	public function getCurrentOperationIndex()
	{
		return $this->currentOperationIndex;
	}

	/**
	 * @param $currentOperationSet the $currentOperationSet to set
	 */
	public function setCurrentOperationSet($currentOperationSet)
	{
		$this->currentOperationSet = $currentOperationSet;
	}

	/**
	 * @param $currentOperationIndex the $currentOperationIndex to set
	 */
	public function setCurrentOperationIndex($currentOperationIndex)
	{
		$this->currentOperationIndex = $currentOperationIndex;
	}

	/**
	 * Moves to the next operation set
	 */
	public function incrementOperationSet()
	{
		$this->currentOperationSet++;
		$this->currentOperationIndex = -1;
	}
	
	/**
	 * @return the $engineVersion
	 */
	public function getEngineVersion()
	{
		return $this->engineVersion;
	}

	/**
	 * @param $engineVersion the $engineVersion to set
	 */
	public function setEngineVersion($engineVersion)
	{
		$this->engineVersion = $engineVersion;
	}

}