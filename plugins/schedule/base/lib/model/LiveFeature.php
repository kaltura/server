<?php
/**
 * @package plugins.schedule
 * @subpackage model
 */
abstract class LiveFeature extends BaseObject
{
	/**
	 * feature name
	 * @var string
	 */
	protected $systemName;

	/**
	 * pre start time in seconds
	 * @var int
	 */
	protected $preStartTime;

	/**
	 * post end time in seconds
	 * @var int
	 */
	protected $postEndTime;

	abstract function getApiType();

	/**
	 * @param string $v
	 */
	public function setSystemName($v)
	{
		$this->systemName = $v;
	}

	public function getSystemName()
	{
		return $this->systemName;
	}

	/**
	 * @param int $v
	 */
	public function setPreStartTime($v)
	{
		$this->preStartTime = $v;
	}

	public function getPreStartTime()
	{
		return $this->preStartTime;
	}

	/**
	 * @param int $v
	 */
	public function setPostEndTime($v)
	{
		$this->postEndTime = $v;
	}

	public function getPostEndTime()
	{
		return $this->postEndTime;
	}
}