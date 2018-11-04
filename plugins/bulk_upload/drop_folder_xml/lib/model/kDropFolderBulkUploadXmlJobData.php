<?php
/**
 * @package plugins.dropFolderXmlBulkUpload
 * @subpackage model.data
 */
class kDropFolderBulkUploadXmlJobData extends kBulkUploadXmlJobData
{
	/**
	 * The bulk upload drop folder id
	 * @var int
	 */
	protected $dropFolderId;

	/**
	 * @return int $privileges
	 */
	public function getDropFolderId()
	{
		return $this->dropFolderId;
	}

	/**
	 * @param int $dropFolderId
	 */
	public function setDropFolderId($dropFolderId)
	{
		$this->dropFolderId = $dropFolderId;
	}
}