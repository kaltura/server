<?php

class kDropFolderFileResource extends kLocalFileResource
{
    /**
	 * @var int
	 */
	private $dropFolderFileId;
	
	    
    /**
     * @return the $dropFolderFileId
     */
    public function getDropFolderFileId ()
    {
        return $this->dropFolderFileId;
    }

	/**
     * @param int $dropFolderFileId
     */
    public function setDropFolderFileId ($dropFolderFileId)
    {
        $this->dropFolderFileId = $dropFolderFileId;
    }

	function getType()
    {
        return 'kLocalFileResource';
    }
    
	/**
	 * @param BaseObject $object
	 */
	public function attachCreatedObject(BaseObject $object)
	{
	    $dropFolderFile = DropFolderFilePeer::retrieveByPK($this->getDropFolderFileId());
		$dropFolder = DropFolderPeer::retrieveByPK($dropFolderFile->getDropFolderId());
    	$entryId = $asset = null;
	    
    	// create import job for remote drop folder files
	    if ($dropFolder instanceof RemoteDropFolder)
	    {
	        // get params
    	    if ($object instanceof asset)
    	    {
    	        $entryId = $object->getEntryId();
    	        $asset = $object;
    	    }
    	    else if ($object instanceof entry)
    	    {
    	        $entryId = $object->getId();
    	        $asset = null;
    	    }
    	    else {
    	        return;
    	    }    	    
	    
	        $importUrl = $dropFolderFile->getFileUrl();
	    
    	    $jobData = $dropFolder->getImportJobData();
			if ($jobData)
    	    	$jobData->setDropFolderFileId($this->getDropFolderFileId());
    	    
    	    // add job
    	    kJobsManager::addImportJob(
    	        null,
    	        $entryId,
    	        $dropFolderFile->getPartnerId(),
    	        $importUrl,
    	        $asset,
    	        $dropFolder->getFileTransferMgrType(),
    	        $jobData
    	    );
    	    
    	    // set file status to DOWNLOADING
    	    $dropFolderFile->setStatus(DropFolderFileStatus::DOWNLOADING);
    	    $dropFolderFile->save();
	    }	    
	}
    
    
    
}