<?php


/**
 * Skeleton subclass for representing a row from the 'live_channel_segment' table.
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
class LiveChannelSegment extends BaseLiveChannelSegment {

	/**
	 * @return LiveChannel
	 */
	public function getChannel()
	{
		return entryPeer::retrieveByPK($this->getChannelId());
	}
	
	/* (non-PHPdoc)
	 * @see BaseLiveChannelSegment::postSave()
	 */
	public function postSave(PropelPDO $con = null)
	{
		$liveChannel = $this->getChannel();
		if($liveChannel)
		{
			$liveChannel->updateStatus();
			$liveChannel->save();
		}
			
		parent::postSave($con);
	}
	
} // LiveChannelSegment
