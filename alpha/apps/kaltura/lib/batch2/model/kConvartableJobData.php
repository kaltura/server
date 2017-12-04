<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kConvartableJobData extends kJobData
{
	/**
	 * @var array<kSourceFileSyncDescriptor>
	 */
	private $srcFileSyncs;
	
	/**
	 * @var int
	 */
	private $engineVersion;
	
	/**
	 * @var int
	 */
	private $flavorParamsOutputId;
		
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
	 * @var array
	 * key-value pair for holding plugin specific data
	 */
	private $pluginData;
	
	/**
	 * @var flavorParamsOutput
	 * @deprecated
	 */
	private $flavorParamsOutput;
	
	/**
	 * @var string
	 * @deprecated
	 */
	private $srcFileSyncLocalPath;
	
	/**
	 * The translated path as used by the scheduler
	 * @var string
	 * @deprecated
	 */
	private $actualSrcFileSyncLocalPath;
	
	/**
	 * @var string
	 * @deprecated
	 */
	private $srcFileSyncRemoteUrl;

	/**
	 * Migrates old members to new used members
	 */
	public function migrateOldSerializedData()
	{
		if($this->srcFileSyncLocalPath || $this->srcFileSyncRemoteUrl || $this->actualSrcFileSyncLocalPath)
		{
			$srcDescriptor = new kSourceFileSyncDescriptor();
			$srcDescriptor->setActualFileSyncLocalPath($this->actualSrcFileSyncLocalPath);
			$srcDescriptor->setFileSyncLocalPath($this->srcFileSyncLocalPath);
			$srcDescriptor->setFileSyncRemoteUrl($this->srcFileSyncRemoteUrl);
			$this->srcFileSyncs = array($srcDescriptor);
		}
	}
	
	/**
	 * @return the $srcFileSyncs
	 */
	public function getSrcFileSyncs() 
	{
		return $this->srcFileSyncs;
	}

	/**
	 * @param array<kSourceFileSyncDescriptor> $srcFileSyncs
	 */
	public function setSrcFileSyncs($srcFileSyncs) 
	{
		$this->srcFileSyncs = $srcFileSyncs;
	}

	/**
	 * @return the $srcFileSyncLocalPath
	 */
	public function getSrcFileSyncLocalPath()
	{
		$srcDescriptor = (is_array($this->srcFileSyncs) && count($this->srcFileSyncs) ? reset($this->srcFileSyncs) : null);
		
		if(!$srcDescriptor)
			return null;
		/* @var $srcDescriptor kSourceFileSyncDescriptor */
		return $srcDescriptor->getFileSyncLocalPath();
	}
	
	/**
	 * @param $srcFileSyncRemoteUrl the $srcFileSyncRemoteUrl to set
	 */
	public function setSrcFileSyncRemoteUrl($srcFileSyncRemoteUrl)
	{
		$srcDescriptor = (is_array($this->srcFileSyncs) && count($this->srcFileSyncs) ? reset($this->srcFileSyncs) : null);
		
		if(!$srcDescriptor)
		{
			$srcDescriptor = new kSourceFileSyncDescriptor();
			$srcDescriptor->setFileSyncRemoteUrl($srcFileSyncRemoteUrl);	
			$this->srcFileSyncs = array($srcDescriptor);
		}
		else
		{
			$srcDescriptor->setFileSyncRemoteUrl($srcFileSyncRemoteUrl);
		}		
	}

	/**
	 * @return the $srcFileSyncRemoteUrl
	 */
	public function getSrcFileSyncRemoteUrl()
	{
		$srcDescriptor = (is_array($this->srcFileSyncs) && count($this->srcFileSyncs) ? reset($this->srcFileSyncs) : null);
		
		if(!$srcDescriptor)
			return null;
		/* @var $srcDescriptor kSourceFileSyncDescriptor */
		return $srcDescriptor->getFileSyncRemoteUrl();
	}

	/**
	 * @param $flavorParamsOutput the $flavorParamsOutput to set
	 */
	public function setFlavorParamsOutput($flavorParamsOutput)
	{
// 		$this->flavorParamsOutput = $flavorParamsOutput;
	}

	/**
	 * @param FileSync $fileSync
	 */
	public function setSrcFileSyncLocalPath($fileSync)
	{
		$srcDescriptor = (is_array($this->srcFileSyncs) && count($this->srcFileSyncs) ? reset($this->srcFileSyncs) : null);
		
		if(!$srcDescriptor)
		{
			$srcDescriptor = new kSourceFileSyncDescriptor();
			$srcDescriptor->setPathAndKeyByFileSync($fileSync);
			$this->srcFileSyncs = array($srcDescriptor);
		}
		else
		{
			$srcDescriptor->setPathAndKeyByFileSync($fileSync);
		}	
	}

	/**
	 * @return the $actualSrcFileSyncLocalPath
	 */
	public function getActualSrcFileSyncLocalPath()
	{
		$srcDescriptor = (is_array($this->srcFileSyncs) && count($this->srcFileSyncs) ? reset($this->srcFileSyncs) : null);
		
		if(!$srcDescriptor)
			return null;
		/* @var $srcDescriptor kSourceFileSyncDescriptor */
		return $srcDescriptor->getActualFileSyncLocalPath();
	}

	/**
	 * @param $actualSrcFileSyncLocalPath the $actualSrcFileSyncLocalPath to set
	 */
	public function setActualSrcFileSyncLocalPath($actualSrcFileSyncLocalPath)
	{
		$srcDescriptor = (is_array($this->srcFileSyncs) && count($this->srcFileSyncs) ? reset($this->srcFileSyncs) : null);
		
		if(!$srcDescriptor)
		{
			$srcDescriptor = new kSourceFileSyncDescriptor();
			$srcDescriptor->setActualFileSyncLocalPath($actualSrcFileSyncLocalPath);	
			$this->srcFileSyncs = array($srcDescriptor);
		}
		else
		{
			$srcDescriptor->setActualFileSyncLocalPath($actualSrcFileSyncLocalPath);	
		}		
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
// 		if ($this->flavorParamsOutput)
// 			return $this->flavorParamsOutput;
			
		if (is_null($this->flavorParamsOutputId))
			return null;
			
		return assetParamsOutputPeer::retrieveByPK($this->flavorParamsOutputId);
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
		$flavorParamsOutput = assetParamsOutputPeer::retrieveByPK($this->flavorParamsOutputId);
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
	
	/**
	 * @return the $pluginData
	 */
	public function getPluginData()
	{
		return $this->pluginData;
	}

	/**
	 * @param $pluginData the $pluginData to set
	 */
	public function setPluginData($pluginData)
	{
		$this->pluginData = $pluginData;
	}

}
