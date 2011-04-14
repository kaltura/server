<?php

class DropFolderContentFileHandler extends DropFolderFileHandler
{
	/* (non-PHPdoc)
	 * @see DropFolderFileHandler::setConfig()
	 */
	public function setConfig(DropFolderFileHandlerConfig $config) {
		// TODO Auto-generated method stub
	}

	/* (non-PHPdoc)
	 * @see DropFolderFileHandler::getType()
	 */
	public function getType() {
		return DropFolderFileHandlerType::CONTENT;
	}

	/**
	 * Add a new entry with the given drop folder file as the resource.
	 * Entry's ingestion profile id should be the one defined on the file's drop folder object.
	 */
	public function handleFile($dropFolderFileId)
	{
		$dropFolderFileResource = new KalturaDropFolderFileResource();
		$dropFolderFileResource->dropFolderFileId = $dropFolderFileId;
		
		//TODO: add a new entry using the $dropFolderFileResource
		//TODO: the entry should be set with conversion profile from the drop folder's $ingestionProfileId property
		
	}
}