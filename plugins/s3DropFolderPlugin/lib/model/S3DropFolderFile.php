<?php
/**
 * @package plugins.dropFolder
 * @subpackage model
 */
class S3DropFolderFile extends DropFolderFile
{
	public function __construct ()
	{
		parent::__construct();
		$type = S3DropFolderPlugin::getDropFolderTypeCoreValue(S3DropFolderType::S3DROPFOLDER);
		$this->setType($type);
	}
	
	public function getNameForParsing ()
	{
		return $this->getFileName();
	}

	public function getFileUrl()
	{
		$dbDropFolder = DropFolderPeer::retrieveByPK($this->getDropFolderId());
		$s3Options = $dbDropFolder->getDropFolderParams();
		
		if ($s3Options['useS3Arn'] && empty($s3Options['s3Arn']))
		{
			$msg = "Drop Folder ID [{$dbDropFolder->getId()}] enabled 'Bucket Policy Allow Access' but 's3Arn' value under 's3_drop_folder' in 'runtime_config' map is missing";
			throw new kFileTransferMgrException($msg, kFileTransferMgrException::otherError);
		}
		
		
		$s3TransferMgr = kFileTransferMgr::getInstance(kFileTransferMgrType::S3, $s3Options);
		$s3TransferMgr->login($dbDropFolder->getS3Host(), $dbDropFolder->getS3UserId(), $dbDropFolder->getS3Password());
		$expiry = time() + 5 * 86400;
		$fullName = $dbDropFolder->getPath() . '/' . $this->getFileName();
		$fileUrl = $s3TransferMgr->getFileUrl($fullName, $expiry);
		KalturaLog::debug('File url  '. print_r($fileUrl, true));
		return $fileUrl;
	}
}
