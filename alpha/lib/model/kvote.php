<?php
/**
 * Subclass for representing a row from the 'kvote' table.
 *
 *
 *
 * @package Core
 * @subpackage model
 */
class kvote extends Basekvote implements IBaseObject
{
	private $statistics_results = null;
	
	public function save(PropelPDO $con = null)
	{
		if ( $this->isNew() )
		{
			$this->statistics_results = myStatisticsMgr::addKvote($this);
		}
		else if (in_array(kvotePeer::STATUS, $this->modifiedColumns))
		{
		   $this->statistics_results = myStatisticsMgr::modifyEntryVotesBykVote($this); 
		}
		else if ($this->status == kvoteStatus::VOTED && in_array(kvotePeer::RANK, $this->modifiedColumns))
		{
			$delta = $this->rank - $this->getColumnsOldValue(kvotePeer::RANK);
			$this->statistics_results = myStatisticsMgr::modifyEntryVotesByChangedVoteDelta($this, $delta);
		}
		
		return parent::save( $con );
	}
	
	public function getFormattedCreatedAt( $format = dateUtils::KALTURA_FORMAT )
	{
		return dateUtils::formatKalturaDate( $this , 'getCreatedAt' , $format );
	}

	public function getFormattedUpdatedAt( $format = dateUtils::KALTURA_FORMAT )
	{
		return dateUtils::formatKalturaDate( $this , 'getUpdatedAt' , $format );
	}
	
	
	public function getStatisticsResults ()
	{
		return $this->statistics_results;
	}
	
}
