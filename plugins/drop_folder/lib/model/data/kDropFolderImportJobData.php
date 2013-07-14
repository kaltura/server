<?php

class kDropFolderImportJobData extends kSshImportJobData
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

    
  
}