<?php
class kWebexDropFolderContentProcessorJobData extends kDropFolderContentProcessorJobData
{
	/**
	 * @var string
	 */
	protected $description;
	
	/**
	 * @var string
	 */
	protected $webexHostId;
	
	/**
	 * @var int
	 */
	protected $dropFolderId;

	
	public function getDescription ()
	{
		return $this->description;
	}
	
	public function setDescription ($v)
	{
		$this->description = $v;
	}
	
	public function getWebexHostId ()
	{
		return $this->webexHostId;
	}
	
	public function setWebexHostId ($v)
	{
		$this->webexHostId = $v;
	}
	
	public function getDropFolderId ()
	{
		return $this->dropFolderId;
	}
	
	public function setDropFolderId ($v)
	{
		$this->dropFolderId = $v;
	}
	
	public function setData (DropFolder $folder, DropFolderFile $dropFolderFileForObject, $dropFolderFileIds)
	{
		/* @var $dropFolderFileForObject WebexDropFolderFile */
		parent::setData ($folder, $dropFolderFileForObject, $dropFolderFileIds);
		$this->dropFolderId = $folder->getId();
		$this->description = $dropFolderFileForObject->getDescription();
		$this->webexHostId = $dropFolderFileForObject->getWebexHostId();
	}
}
