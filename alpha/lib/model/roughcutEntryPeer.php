<?php

/**
 * Subclass for performing query and update operations on the '0' table.
 *
 * 
 *
 * @package lib.model
 */ 
class roughcutEntryPeer extends BaseroughcutEntryPeer
{
	// TODO - the join_all = true doesn't work due to a bug with propel !
	// need to find a workaround
	public static function retrievByRoughcutId( $roughcut_id , $join_all = false )
	{
		$c = new Criteria();
//		myCriteria::addComment( $c , __METHOD__ );
		$c->addAnd ( roughcutEntryPeer::ROUGHCUT_ID, $roughcut_id );
		$c->addAscendingOrderByColumn( roughcutEntryPeer::ID );
		if ( $join_all )
			return roughcutEntryPeer::doSelectJoinentryRelatedByEntryId( $c );
		else
			return roughcutEntryPeer::doSelect( $c );
	}

	// fetch the entries
	public static function retrievByRoughcutIds( $c , $roughcut_ids , $join_all = false )
	{
		if ( $c == null ) $c = new Criteria();
//		myCriteria::addComment( $c , __METHOD__ );
		$c->addAnd ( roughcutEntryPeer::ROUGHCUT_ID, $roughcut_ids , Criteria::IN );
		
		$c->addAscendingOrderByColumn( roughcutEntryPeer::ID );
		if ( $join_all )
			return roughcutEntryPeer::doSelectJoinentryRelatedByEntryId( $c );
		else
			return roughcutEntryPeer::doSelect( $c );
	}

	public static function retrievEntriesByRoughcutIds( $entry_criteria , $roughcut_ids )
	{
		// first get all the roughcuts
		$c = new Criteria();
//		myCriteria::addComment( $c , __METHOD__ );
		$c->addAnd ( roughcutEntryPeer::ROUGHCUT_ID, $roughcut_ids , Criteria::IN );
		$res = roughcutEntryPeer::doSelect( $c );
		if ( $res )
		{
			$entry_ids = array();
			foreach ( $res as $roughcut_entry )
			{
				$entry_ids[] = $roughcut_entry->getEntryId();
			}
			
			// now fetch all the relevant entries
			if ( $entry_criteria == null ) $entry_criteria = new Critaria();
			$debug = __METHOD__ . " " . print_r ( $roughcut_ids , true ) . "\n" . print_r ( $entry_ids , true );
//print ($debug);			
sfLogger::getInstance()->log ( $debug );
			$entry_criteria->add ( entryPeer::ID , $entry_ids , Criteria::IN );			
			return entryPeer::doSelect( $entry_criteria );
		}
		return array();
	}
	
	// TODO - the join_all = true doesn't work due to a bug with propel !
	// need to find a workaround
	public static function retrievByEntryId( $entry_id , $join_all = false , $op_type = null )
	{
//echo __METHOD__ . ":[$entry_id]\n";
		$c = new Criteria();
//		myCriteria::addComment( $c , __METHOD__ );
		$c->addAnd ( roughcutEntryPeer::ENTRY_ID , $entry_id );
		if ( $op_type )
			$c->addAnd ( roughcutEntryPeer::OP_TYPE , $op_type );
		$c->addAscendingOrderByColumn( roughcutEntryPeer::ID );
		if ( $join_all )
			return roughcutEntryPeer::doSelectJoinentryRelatedByRoughcutId( $c );
		else
			return roughcutEntryPeer::doSelect( $c );
	}

	// TODO - the join_all = true doesn't work due to a bug with propel !
	// need to find a workaround
	public static function retrievByRoughcutIdAndEntryId( $roughcut_id , $entry_id , $join_all = false )
	{
		$c = new Criteria();
//		myCriteria::addComment( $c , __METHOD__ );
		$c->addAnd ( roughcutEntryPeer::ROUGHCUT_ID, $roughcut_id );
		$c->addAnd ( roughcutEntryPeer::ENTRY_ID , $entry_id );
		$c->addAscendingOrderByColumn( roughcutEntryPeer::ID );
		if ( $join_all )
			return roughcutEntryPeer::doSelectJoinAll( $c );
		else
			return roughcutEntryPeer::doSelect( $c );
	}

	// TODO - the join_all = true doesn't work due to a bug with propel !
	// need to find a workaround
	public static function retrievByKshowId( $kshow_id , $join_all = false )
	{
		$c = new Criteria();
//		myCriteria::addComment( $c , __METHOD__ );
		$c->addAnd ( roughcutEntryPeer::ROUGHCUT_KSHOW_ID , $kshow_id );
		$c->addAscendingOrderByColumn( roughcutEntryPeer::ID );
		if ( $join_all )
			return roughcutEntryPeer::doSelectJoinAll( $c );
		else
			return roughcutEntryPeer::doSelect( $c );
	}	
	
}
