<?php
require_once( 'dateUtils.class.php');
require_once ( "myStatisticsMgr.class.php");
/**
 * Subclass for representing a row from the 'kvote' table.
 *
 *
 *
 * @package Core
 * @subpackage model
 */
class kvote extends Basekvote
{
	private $statistics_results = null;
	
	public function __construct()
	{
	    $this->status = KVoteStatus::KVOTE_STATUS_VOTED;
	}
	
	public function save(PropelPDO $con = null)
	{
		if ( $this->isNew() )
		{
			$this->statistics_results = myStatisticsMgr::addKvote( $this , $this->getRank() );
		}
		if (in_array(kvotePeer::STATUS, $this->modifiedColumns))
		{
		   $this->statistics_results = myStatisticsMgr::modifyEntryVotesBykVote($this, $this->getRank()); 
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
