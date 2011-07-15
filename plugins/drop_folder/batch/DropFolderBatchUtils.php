<?php

class DropFolderBatchUtils
{
    
    /** 
     * Init a kFileTransferManager acccording to folder type and login to the server
     * @param KalturaDropFolder $folder
     * @throws Exception
     * 
     * @return kFileTransferMgr
     */
	public static function getFileTransferManager(KalturaDropFolder $folder)
	{
	    $fileTransferMgr = null;
	    $host = $port = $username = $password = $privateKey = $publicKey = null;
	    
	    switch ($folder->type)
	    {
	        case KalturaDropFolderType::LOCAL:
	            $fileTransferMgr = new localMgr();
	            $host = $port = $username = $password = true;
                break;
            case KalturaDropFolderType::FTP:
	            $fileTransferMgr = kFileTransferMgr::getInstance(kFileTransferMgrType::FTP);
	            $host = $folder->host;
	            $port = $folder->port;
	            $username = $folder->username;
	            $password = $folder->password;
	            break;
	        case KalturaDropFolderType::SFTP:
	            $fileTransferMgr = kFileTransferMgr::getInstance(kFileTransferMgrType::SFTP);
	        case KalturaDropFolderType::SCP:
	            $fileTransferMgr = kFileTransferMgr::getInstance(kFileTransferMgrType::SCP);
	            $host = $folder->host;
	            $port = $folder->port;
	            $username = $folder->username;
	            $password = $folder->password;
	            $privateKey = isset($folder->privateKey) ? $folder->privateKey : null;
	            $publicKey = isset($folder->publicKey) ? $folder->publicKey : null;
	            break;
	            
	        default:
	            throw new Exception('Unsupported drop folder type ['.$folder->type.']', null, null);
	    }
	    try
	    {
        	// login to server
        	if (!$privateKey || !$publicKey) {
        	    $fileTransferMgr->login($host, $username, $password, $port);
        	}
        	else {
        	    $privateKeyFile = self::getTempFileWithContent($privateKey, 'privateKey');
        	    $publicKeyFile = self::getTempFileWithContent($publicKeyFile, 'publicKey');
        	    $fileTransferMgr->loginPubKey($host, $username, $publicKeyFile, $privateKeyFile, null, $port);
        	}
	    }
	    catch (Exception $e)
	    {
	        throw $e;
	    }
		return $fileTransferMgr;		
	}
	
	
	/**
	 * Lazy saving of file content to a temporary path, the file will exist in this location until the temp files are purged
	 * @param string $fileContent
	 * @param string $prefix
	 * @return string path to temporary file location
	 */
	protected static function getTempFileWithContent($fileContent, $prefix = '') 
	{
		$tempDirectory = sys_get_temp_dir();
		$fileLocation = tempnam($tempDirectory, $prefix);
		while (file_exists($fileLocation)) {
		    $fileLocation += '_'.time();
		}
		
		file_put_contents($fileLocation, $fileContent);
		
		return $fileLocation;
	}
	
	
    
}