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
	
	
	public function setData (DropFolder $folder, DropFolderFile $dropFolderFileForObject, $dropFolderFileIds)
	{
		/* @var $dropFolderFileForObject WebexDropFolderFile */
		parent::setData ($folder, $dropFolderFileForObject, $dropFolderFileIds);
		$this->description = $dropFolderFileForObject->getDescription();
		$this->webexHostId = $dropFolderFileForObject->getWebexHostId();
	}
}
