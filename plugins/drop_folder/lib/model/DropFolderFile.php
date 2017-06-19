<?php


/**
 * Skeleton subclass for representing a row from the 'drop_folder_file' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.dropFolder
 * @subpackage model
 */
class DropFolderFile extends BaseDropFolderFile implements IBaseObject
{

	const CUSTOM_DATA_LAST_MODIFICATION_TIME = 'last_modification_time';
	const CUSTOM_DATA_BATCH_JOB_ID = 'batch_job_id';
	const CUSTOM_DATA_USER_ID = 'user_id';
	
	public function setFileSize($size)
	{
		if ($size !== $this->getFileSize()) {
		    self::setFileSizeLastSetAt(time());
		}
		
		parent::setFileSize($size);
	}
	
	public function getFileSizeLastSetAt($format = null)
	{
		return parent::getFileSizeLastSetAt($format);
	}
	
	
	public function getLastModificationTime()
	{
	    return $this->getFromCustomData(self::CUSTOM_DATA_LAST_MODIFICATION_TIME);
	}
	
	public function setLastModificationTime($time)
	{
	    $this->putInCustomData(self::CUSTOM_DATA_LAST_MODIFICATION_TIME, $time);
	}
	
	public function getBatchJobId()
	{
	    return $this->getFromCustomData(self::CUSTOM_DATA_BATCH_JOB_ID);
	}
	
	public function setBatchJobId($id)
	{
	    $this->putInCustomData(self::CUSTOM_DATA_BATCH_JOB_ID, $id);
	}
	
	public function getParsedUserId()
	{
	    return $this->getFromCustomData(self::CUSTOM_DATA_USER_ID);
	}
	
	public function setParsedUserId($id)
	{
	    $this->putInCustomData(self::CUSTOM_DATA_USER_ID, $id);
	}
		
	public function getCacheInvalidationKeys()
	{
		return array("dropFolderFile:id=".strtolower($this->getId()), "dropFolderFile:fileName=".strtolower($this->getFileName()), "dropFolderFile:dropFolderId=".strtolower($this->getDropFolderId()));
	}
	
	public function setStatus($v)
	{
		if($this->getStatus() != DropFolderFileStatus::PURGED)
			return parent::setStatus($v);
			
		return $this;
	}
	
	/* (non-PHPdoc)
	 * @see BaseDropFolderFile::preUpdate()
	 */
	public function preUpdate(PropelPDO $con = null)
	{
		$errorStatusesArray = array(DropFolderFileStatus::ERROR_DELETING, DropFolderFileStatus::ERROR_DOWNLOADING, DropFolderFileStatus::ERROR_HANDLING);
		if($this->isColumnModified(DropFolderFilePeer::STATUS) && $this->getStatus() == DropFolderFileStatus::PURGED)
		{
			$this->setDeletedDropFolderFileId($this->getId());
		}
		if($this->isColumnModified(DropFolderFilePeer::STATUS) 
		   && in_array($this->getColumnsOldValue(DropFolderFilePeer::STATUS), $errorStatusesArray)
		   && !in_array($this->getStatus(), $errorStatusesArray))
		{
			$this->setErrorCode(null);
			$this->setErrorDescription(null);
		}
		
		return parent::preUpdate($con);
	}
	
	/* (non-PHPdoc)
	 * @see BaseDropFolderFile::preInsert()
	 */
	public function preInsert(PropelPDO $con = null) 
	{
		$this->setMd5FileName(md5($this->getFileName()));		
		return parent::preInsert($con);
	}

	
	public function getFileUrl ()
	{
		$dropFolder = DropFolderPeer::retrieveByPK($this->getDropFolderId());
		return $dropFolder->getFolderUrl() . '/' .$this->getFileName();
	} 
	
	public function getNameForParsing ()
	{
		return $this->getFileName();
	}

	public function copyInto($copyObj, $deepCopy = false)
	{
		if ($deepCopy)
			$this->setCustomDataObj();
		parent::copyInto($copyObj, $deepCopy);
	}
} // DropFolderFile
