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
	
}
