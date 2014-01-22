<?php
class kDropFolderContentProcessorJobData extends kJobData
{
    /**
     * @var string
     */
    private $dropFolderFileIds;
     
     /**
     * @var string
     */    
    private $parsedSlug;
    
   	/**
	 * @var int
	 */
	private $contentMatchPolicy;
	
	/**
	 * 
	 * @var int
	 */
	private $conversionProfileId;
	
	/**
	 * @var string
	 */
	private $parsedUserId;
	
	/**
	 * @var int
	 */
	protected $dropFolderId;
    
	/**
     * @return the $dropFolderFileIds
     */
    public function getDropFolderFileIds ()
    {
        return $this->dropFolderFileIds;
    }

	/**
     * @param string $dropFolderFileId
     */
    public function setDropFolderFileIds ($dropFolderFileIds)
    {
        $this->dropFolderFileIds = $dropFolderFileIds;
    }

    /**
	 * @return the $parsedSlug
	 */
	public function getParsedSlug() 
	{
		return $this->parsedSlug;
	}

	/**
	 * @param string $parsedSlug
	 */
	public function setParsedSlug($parsedSlug) 
	{
		$this->parsedSlug = $parsedSlug;
	}
	
	/**
	 * @return the $contentMatchPolicy
	 */
	public function getContentMatchPolicy() 
	{
		return $this->contentMatchPolicy;
	}

	/**
	 * @param DropFolderContentFileHandlerMatchPolicy $contentMatchPolicy
	 */
	public function setContentMatchPolicy($contentMatchPolicy) 
	{
		$this->contentMatchPolicy = $contentMatchPolicy;
	}
	
	/**
	 * @return the $conversionProfileId
	 */
	public function getConversionProfileId() 
	{
		return $this->conversionProfileId;
	}

	/**
	 * @param int $conversionProfileId
	 */
	public function setConversionProfileId($conversionProfileId) 
	{
		$this->conversionProfileId = $conversionProfileId;
	}
	
	/**
	 * @return string
	 */
	public function getParsedUserId() 
	{
		return $this->parsedUserId;
	}

	/**
	 * @param string $v
	 */
	public function setParsedUserId($v) 
	{
		$this->parsedUserId = $v;
	}
	
	public function getDropFolderId ()
	{
		return $this->dropFolderId;
	}
	
	public function setDropFolderId ($v)
	{
		$this->dropFolderId = $v;
	}


    public static function getInstance ($dropFolderType)
	{
		$res = null;
		switch ($dropFolderType)
		{
			case DropFolderType::FTP:
			case DropFolderType::LOCAL:
			case DropFolderType::SFTP:
			case DropFolderType::SCP:
			case DropFolderType::S3:
				$res = new kDropFolderContentProcessorJobData();
			default:
				$res = KalturaPluginManager::loadObject('kDropFolderContentProcessorJobData', $dropFolderType);
		}
		
		if (!$res)
			$res = new kDropFolderContentProcessorJobData();
		
		return $res;
	}
	
	public function setData (DropFolder $folder, DropFolderFile $dropFolderFileForObject, $dropFolderFileIds)
	{
		$this->dropFolderId = $folder->getId();
		$this->setConversionProfileId($folder->getConversionProfileId());
		$this->setParsedSlug($dropFolderFileForObject->getParsedSlug());
		$this->setContentMatchPolicy($folder->getFileHandlerConfig()->getContentMatchPolicy());
		$this->setDropFolderFileIds($dropFolderFileIds);
		if ($dropFolderFileForObject->getParsedUserId())
		{
			$this->setParsedUserId($dropFolderFileForObject->getParsedUserId()); 
		}
	}
    
  
}