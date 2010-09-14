<?php

/**
 * Subclass for representing a row from the '0' table.
 *
 * 
 *
 * @package lib.model
 */ 
class roughcutEntry extends BaseroughcutEntry
{
	const ROUGHCUT_ENTRY_OP_TYPE_ADD = 1;
	const ROUGHCUT_ENTRY_OP_TYPE_REMOVE = -1;
	
	// used to delete a single entry from a roughcut
	public static function deleteEntryFromRoughcut ( $roughcut_id , $roughcut_version , $roughcut_kshow_id , $entry_id )
	{
		$roughcut_entry_list = roughcutEntryPeer::retrievByRoughcutIdAndEntryId( $roughcut_id , $entry_id );
		$existing_entry_list = self::accumulateRoughcutEntryList ( $roughcut_entry_list );
		
		if ( in_array( $entry_id , $existing_entry_list ) )	
		{	
			self::updateRoughcutEntry( $roughcut_id , $roughcut_version , $roughcut_kshow_id , $entry_id , self::ROUGHCUT_ENTRY_OP_TYPE_REMOVE );
		}			
	}
	
	// assume the $entry_id_list is an array of entry_ids - all the entries that exist for a roughcut at a given point
	public static function updateRoughcut ( $roughcut_id , $roughcut_version , $roughcut_kshow_id , $entry_id_list )
	{
/*		
		$roughcut_id = $roughcut->getId();
		$roughcut_version = $roughcut->getVersion();
		$roughcut_kshow_id = $roughcut->getKshowId();
*/
		$roughcut_entry_list = roughcutEntryPeer::retrievByRoughcutId( $roughcut_id );
		
		$existing_entry_list = self::accumulateRoughcutEntryList ( $roughcut_entry_list );
		
//	echo "entry_id_list: " . print_r ( $entry_id_list , true ) . "\n";
//	echo "existing_entry_list: " . print_r ( $existing_entry_list , true ) . "\n";
		
		$to_add = array();
		$to_remove = array();
		// find all the new ones

		foreach ( $entry_id_list as $new_entry_id  )
		{
			if ( ! in_array ( $new_entry_id , $existing_entry_list) )
			{
				// ignore duplicates
				if ( ! in_array ($new_entry_id , $to_add ))
				{
					self::updateRoughcutEntry( $roughcut_id , $roughcut_version , $roughcut_kshow_id , $new_entry_id , self::ROUGHCUT_ENTRY_OP_TYPE_ADD );
					$to_add[] = $new_entry_id;
				}
			}
		}
		
		// find all the ones that no longer exist
		foreach ( $existing_entry_list as $existing_entry_id )
		{
			if ( ! in_array( $existing_entry_id , $entry_id_list) )
			{
				// ignore duplicates
				if ( ! in_array ($existing_entry_id , $to_remove ))
				{
					self::updateRoughcutEntry( $roughcut_id , $roughcut_version , $roughcut_kshow_id , $existing_entry_id , self::ROUGHCUT_ENTRY_OP_TYPE_REMOVE );
					$to_remove[] = $existing_entry_id;
				}
			}
		}
		
//		echo "add: " . print_r ( $to_add , true ) . "\n";
//		echo "delete: " . print_r ( $to_remove , true ) . "\n";
		
		KalturaLog::log ( 
			"roughcutEntry::updateRoughcut\nadded: [" . implode ( "," , $to_add ) . "] removed:  [" . implode ( "," , $to_add ) . "]"  );
	}
	
	private static function updateRoughcutEntry ( $roughcut_id , $roughcut_version , $roughcut_kshow_id , $entry_id , $op_type )
	{
		$roughcut_entry = new roughcutEntry();
		$roughcut_entry->setRoughcutId( $roughcut_id );
		$roughcut_entry->setRoughcutVersion ( $roughcut_version );
		$roughcut_entry->setRoughcutKshowId ( $roughcut_kshow_id );
		$roughcut_entry->setEntryId ( $entry_id );
		$roughcut_entry->setOpType( $op_type );
		$roughcut_entry->save();
	}
	
	// assume the list is sorted by date - 
	// accumulate all the entries and decide if each entry exists or not in the roughcut 
	public static function accumulateRoughcutEntryList ( $list , $desired_roughcut_id = null)
	{
		$entry_id_list = array ();
		foreach ( $list as $roughcut_entry )
		{
			$id = $roughcut_entry->getEntryId();
			$roughcut_id = $roughcut_entry->getRoughcutId();
			if ( $desired_roughcut_id != null && $desired_roughcut_id != $roughcut_id ) continue;
			if ( $roughcut_entry->getOpType() == self::ROUGHCUT_ENTRY_OP_TYPE_ADD )
			{
				$entry_id_list[]=$id;
			}
			elseif ( $roughcut_entry->getOpType() == self::ROUGHCUT_ENTRY_OP_TYPE_REMOVE )
			{
				kArray::removeFromArray ( $entry_id_list , $id );
			}
		}
		
		return $entry_id_list;
	}
	
	public static function isEntryInRoughcut ( $list , $entry_id , $roughcut_id = null)
	{
		$entry_id_list = self::accumulateRoughcutEntryList ( $list , $roughcut_id );
		return ( in_array ( $entry_id , $entry_id_list ));
	}	
	
	
	public static function getAllRoughcuts ( $entry_id )
	{
		$roughcut_id_list = array();
		$roughcut_entry_list = roughcutEntryPeer::retrievByEntryId( $entry_id /*, true */);
		
//		print_r ( $roughcut_entry_list );
		
		foreach ( $roughcut_entry_list as $roughcut_entry )		
		{
			$roughcut_id = $roughcut_entry->getRoughcutId();
				// see there will be 
			if ( in_array ( $roughcut_id , $roughcut_id_list ) ) continue;
	
			if ( self::isEntryInRoughcut ( $roughcut_entry_list , $entry_id , $roughcut_id ) )
			{
//				$roughcut = $roughcut_entry->getRoughcut();
				$roughcut_id_list[] = $roughcut_id;
			}
		}
		
		
		if ( count ( $roughcut_id_list ) > 0 )
			$roughcut_list = entryPeer::retrieveByPKs( $roughcut_id_list );
		else
			$roughcut_list = array();
		
		return $roughcut_list;
	}
	
	public function getRoughcut()
	{
		return $this->getentryRelatedByRoughcutId();
	}
	
	public function getEntry()
	{
		return $this->getentryRelatedByEntryId(); 
	}
	
	public static function getAllEntries ( $c , $roughcut_ids )
	{
		roughcutEntryPeer::retrievByRoughcutIds ( $c , $roughcut_ids );
	}
}
