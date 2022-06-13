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
	protected $preStart;

	/**
	 * post end time in seconds
	 * @var int
	 */
	protected $postEnd;

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
	public function setPreStart($v)
	{
		$this->preStart = $v;
	}

	public function getPreStart()
	{
		return $this->preStart;
	}

	/**
	 * @param int $v
	 */
	public function setPostEnd($v)
	{
		$this->postEnd = $v;
	}

	public function getPostEnd()
	{
		return $this->postEnd;
	}
}