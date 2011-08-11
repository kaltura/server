<?php
require_once ( "myStatisticsMgr.class.php");
/**
 * Subclass for representing a row from the 'favorite' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 

    
class favorite extends Basefavorite
{
	public function save(PropelPDO $con = null)
	{
		if ( $this->isNew() )
		{
			myStatisticsMgr::addFavorite( $this );
		}
		
		parent::save( $con );
	}	

	// We're using the same table to store favorites of different types, use this integer constant to differentiate
	const SUBJECT_TYPE_KSHOW = '1';
	const SUBJECT_TYPE_ENTRY = '2';
	const SUBJECT_TYPE_USER = '3';

	// Favorites can be private, semi-private or public, use this int constant to differentiate
	const PRIVACY_TYPE_USER = '1';
	//const PRIVACY_TYPE_GROUP = '2';
	const PRIVACY_TYPE_WORLD = '3';
	
	/*
	public function setSubjectId($v)
	{

		if ($this->subject_id !== $v) {
			$this->subject_id = $v;
			$this->modifiedColumns[] = favoritePeer::SUBJECT_ID;
		}
		
		if ($this->subject_type == self::SUBJECT_TYPE_KSHOW)
		{
			$this->setSubjectKshowId($v);
			$this->setSubjectEntryId(null);
			$this->setSubjectKuserId(null);
		}
		else if ($this->subject_type == self::SUBJECT_TYPE_ENTRY)
		{
			$this->setSubjectKshowId(null);
			$this->setSubjectEntryId($v);
			$this->setSubjectKuserId(null);
		}
		else if ($this->subject_type == self::SUBJECT_TYPE_USER)
		{
			$this->setSubjectKshowId(null);
			$this->setSubjectEntryId(null);
			$this->setSubjectKuserId($v);
		}
		else
			throw new Exception("Unable to set favorite subject_id because of missing subject_type");
	} 
	*/


}
