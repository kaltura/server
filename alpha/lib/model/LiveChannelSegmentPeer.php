<?php

/**
 * Skeleton subclass for performing query and update operations on the 'live_channel_segment' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package Core
 * @subpackage model
 */
class LiveChannelSegmentPeer extends BaseLiveChannelSegmentPeer
{
	public static function setDefaultCriteriaFilter()
	{
		parent::setDefaultCriteriaFilter();
		if(self::$s_criteria_filter == null)
		{
			self::$s_criteria_filter = new criteriaFilter();
		}
		
		$c = new myCriteria();
		$c->addAnd(LiveChannelSegmentPeer::STATUS, LiveChannelSegmentStatus::DELETED, Criteria::NOT_EQUAL);
		self::$s_criteria_filter->setFilter($c);
	}
	
	/**
	 * Retrieve segements by channel id
	 *
	 * @param      string $channelId
	 * @return     array<LiveChannelSegment>
	 */
	public static function retrieveByChannelId($channelId)
	{
		$criteria = new Criteria();
		$criteria->add(LiveChannelSegmentPeer::CHANNEL_ID, $channelId);
		
		return LiveChannelSegmentPeer::doSelect($criteria);
	}
	
	/**
	 * Counts segements by channel id
	 *
	 * @param      string $channelId
	 * @return     int
	 */
	public static function countByChannelId($channelId)
	{
		$criteria = new Criteria();
		$criteria->add(LiveChannelSegmentPeer::CHANNEL_ID, $channelId);
		
		return LiveChannelSegmentPeer::doCount($criteria);
	}

} // LiveChannelSegmentPeer
