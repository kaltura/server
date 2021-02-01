<?php
/**
 * @package plugins.dropFolder
 * @subpackage model
 */
class S3DropFolderFile extends DropFolderFile
{
	public function getNameForParsing ()
	{
		return $this->getFileName();
	}

	public function getFileUrl()
	{
		$dbDropFolder = DropFolderPeer::retrieveByPK($this->getDropFolderId());
		$s3Options = $dbDropFolder->getDropFolderParams();
		$s3TransferMgr = kFileTransferMgr::getInstance(kFileTransferMgrType::S3, $s3Options);
		$s3TransferMgr->login($dbDropFolder->getS3Host(), $dbDropFolder->getS3UserId(), $dbDropFolder->getS3Password());
		$expiry = time() + 5 * 86400;
		$fullName = $dbDropFolder->getPath() . '/' . $this->getFileName();
		$fileUrl = $s3TransferMgr->getFileUrl($fullName, $expiry);
		KalturaLog::debug('File url  '. print_r($fileUrl, true));
		return $fileUrl;
	}
}
