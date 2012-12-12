<?php

class KPhysicalDropFolderUtils
{
	/**
	* @var kFileTransferMgr
	*/
	public $fileTransferMgr = null;
	
	/**
	* @var KalturaDropFolder
	*/
	private $folder = null;
	
	/**
	 * @var string
	 */
	private $tempDirectory = null;
	
	public function __construct(KalturaDropFolder $folder)
	{
		$this->folder = $folder;
		$this->fileTransferMgr = self::getFileTransferManager($folder);
	}

	/**
	 * Local drop folder - constract full path
	 * Remote drop folder - download file to a local temp directory and return the temp file path
	 * @param string $fileName
	 * @param int $fileId
	 * @throws Exception
	 */
	public function getLocalFilePath($fileName, $fileId)
	{
		$dropFolderFilePath = $this->folder->path.'/'.$fileName;
	    
	    // local drop folder
	    if ($this->folder->type == KalturaDropFolderType::LOCAL) 
	    {
	        $dropFolderFilePath = realpath($dropFolderFilePath);
	        return $dropFolderFilePath;
	    }
	    else
	    {
	    	// remote drop folder	
	    	if($this->tempDirectory == null)
	    	{
	    		$this->tempDirectory = sys_get_temp_dir();
				if (!is_dir($this->tempDirectory)) 
				{
					KalturaLog::err('Missing temporary directory');
					throw new Exception('Missing temporary directory');
				}
	    	}    
			$tempFilePath = tempnam($this->tempDirectory, 'parse_dropFolderFileId_'.$fileId.'_');		
			$this->fileTransferMgr->getFile($dropFolderFilePath, $tempFilePath);
			return $tempFilePath;
	    }			    		
	}
	
    /** 
     * Init a kFileTransferManager acccording to folder type and login to the server
     * @param KalturaDropFolder $folder
     * @throws Exception
     * 
     * @return kFileTransferMgr
     */
	private static function getFileTransferManager(KalturaDropFolder $folder)
	{
	    $fileTransferMgr = kFileTransferMgr::getInstance(self::getFileTransferMgrType($folder->type)); //TODO
	    
	    $host =null; $username=null; $password=null; $port=null;
	    $privateKey = null; $publicKey = null;
	    
	    if($folder instanceof KalturaRemoteDropFolder)
	    {
	   		$host = $folder->host;
	    	$port = $folder->port;
	    	$username = $folder->username;
	    	$password = $folder->password;
	    }  
	    if($folder instanceof KalturaSshDropFolder)
	    {
	    	$privateKey = $folder->privateKey;
	    	$publicKey = $folder->publicKey;
	    	$passPhrase = $folder->passPhrase;  	    	
	    }

        // login to server
        if ($privateKey || $publicKey) 
        {
	       	$privateKeyFile = self::getTempFileWithContent($privateKey, 'privateKey');
        	$publicKeyFile = self::getTempFileWithContent($publicKey, 'publicKey');
        	$fileTransferMgr->loginPubKey($host, $username, $publicKeyFile, $privateKeyFile, $passPhrase, $port);        	
        }
        else 
        {
        	$fileTransferMgr->login($host, $username, $password, $port);        	
        }
		return $fileTransferMgr;		
	}
	
	
	/**
	 * Lazy saving of file content to a temporary path, the file will exist in this location until the temp files are purged
	 * @param string $fileContent
	 * @param string $prefix
	 * @return string path to temporary file location
	 */
	private static function getTempFileWithContent($fileContent, $prefix = '') 
	{
		if(!$fileContent)
			return null;
		$tempDirectory = sys_get_temp_dir();
		$fileLocation = tempnam($tempDirectory, $prefix);		
		file_put_contents($fileLocation, $fileContent);
		return $fileLocation;
	}
	
	/**
	 * This mapping is required since the Enum values of the drop folder and file transfer manager are not the same
	 * @param int $dropFolderType
	 */
	private static function getFileTransferMgrType($dropFolderType)
	{
		switch ($dropFolderType)
		{
			case KalturaDropFolderType::LOCAL:
				return kFileTransferMgrType::LOCAL;
			case KalturaDropFolderType::FTP:
				return kFileTransferMgrType::FTP;
			case KalturaDropFolderType::SCP:
				return kFileTransferMgrType::SCP;
			case KalturaDropFolderType::SFTP:
				return kFileTransferMgrType::SFTP;
			case KalturaDropFolderType::S3:
				return kFileTransferMgrType::S3;
			default:
				return $dropFolderType;				
		}
		
	}
}