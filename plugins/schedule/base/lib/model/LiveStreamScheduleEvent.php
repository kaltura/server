<?php
/**
 * @package plugins.schedule
 * @subpackage model
 */
class LiveStreamScheduleEvent extends BaseLiveStreamScheduleEvent implements ILiveStreamScheduleEvent
{
	const PROJECTED_AUDIENCE = 'projected_audience';
	const PRE_START_TIME = 'pre_start_time';
	const POST_END_TIME = 'post_end_time';

	/**
	 * @param int $v
	 */
	public function setProjectedAudience($v)
	{
		$this->putInCustomData(self::PROJECTED_AUDIENCE, $v);
	}

	/**
	 * @return int
	 */
	public function getProjectedAudience()
	{
		return $this->getFromCustomData(self::PROJECTED_AUDIENCE);
	}
	
	/**
	 * @param int $v
	 */
	public function setPreStartTime($v)
	{
		$this->putInCustomData(self::PRE_START_TIME, $v);
	}

	/**
	 * @return int
	 */
	public function getPreStartTime()
	{
		return $this->getFromCustomData(self::PRE_START_TIME, null, 0);
	}

	/**
	 * @param int $v
	 */
	public function setPostEndTime($v)
	{
		$this->putInCustomData(self::POST_END_TIME, $v);
	}

	/**
	 * @return int
	 */
	public function getPostEndTime()
	{
		return $this->getFromCustomData(self::POST_END_TIME, null, 0);
	}
	
	public function getCalculatedStartTime()
	{
		return parent::getCalculatedStartTime() - $this->getPreStartTime();
	}

	public function getCalculatedEndTime()
	{
		return parent::getCalculatedEndTime() + $this->getPostEndTime();
	}
	
	public function getAffectedProperty()
	{
		return array('isPlayable');
	}
	public function decoratorExecute (LiveEntry $e)
	{
		foreach ($this->getAffectedProperty() as $prop)
		{
			$e->$prop = true;
		}
	}
}