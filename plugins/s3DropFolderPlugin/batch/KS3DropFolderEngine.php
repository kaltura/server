<?php
/**
 * @package plugins.S3DropFolder
 */
class KS3DropFolderEngine extends KDropFolderFileTransferEngine
{
	protected $physicalFilesByPath = array();

	protected function validatePhysicalFile ($physicalFile)
	{
		//Since using object storage, the files we get in the initial list must exist.
		return true;
	}

	protected function getLastModificationTime($fullPath)
	{
		//Since using object storage, modification time does not increase when client writes it.
		return $this->physicalFilesByPath[$fullPath]->modificationTime;
	}

	protected function getFileSize($fullPath)
	{
		//Since using object storage, file size does not increase when client writes it.
		return $this->physicalFilesByPath[$fullPath]->fileSize;
	}

	protected function logPhysicalFile($fullPath,$physicalFileInfo)
	{
		$this->physicalFilesByPath[$fullPath] = $physicalFileInfo;
	}
}