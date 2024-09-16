<?php

class uiConfV2Redirect extends BaseObject
{
	/**
	 * @var int
	 */
	protected $v7Id;

	/**
	 * @var bool
	 */
	protected $isApproved;

	/**
	 * @var bool
	 */
	protected $translatePlugins;

	public function setV7Id($v)
	{
		$this->v7Id = $v;
	}

	public function setIsApproved($v)
	{
		$this->isApproved = $v;
	}

	public function setTranslatePlugins($v)
	{
		$this->translatePlugins = $v;
	}

	public function getV7Id()
	{
		return $this->v7Id;
	}

	public function getIsApproved()
	{
		return $this->isApproved;
	}

	public function getTranslatePlugins()
	{
		return $this->translatePlugins;
	}
}