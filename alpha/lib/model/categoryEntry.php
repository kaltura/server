<?php


/**
 * Skeleton subclass for representing a row from the 'category_entry' table.
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
class categoryEntry extends BasecategoryEntry {
	
	private $entryCategoriesRemovedIds = null;
	private $entryCategoriesAddedIds = null;
	
	public function setEntryCategoriesAddedIds($v)
	{
		$this->entryCategoriesAddedIds = $v;
	}
	
	public function setEntryCategoriesRemovedIds($v)
	{
		$this->entryCategoriesRemovedIds = $v;
	}
	
	/* (non-PHPdoc)
	 * @see lib/model/om/Basecategory#postInsert()
	 */
	public function postInsert(PropelPDO $con = null)
	{	
		parent::postInsert($con);
	
		$category = categoryPeer::retrieveByPK($this->category_id);
		if($category)
		{
			$category->incrementEntriesCount(1, $this->entryCategoriesAddedIds);
			$category->incrementDirectEntriesCount();
		}
	}
	
	/**
	 * Code to be run after deleting the object from database
	 * @param PropelPDO $con
	 */
	public function postDelete(PropelPDO $con = null)
	{
		parent::postDelete($con);
		
		$category = categoryPeer::retrieveByPK($this->category_id);
		if($category)
		{
			$category->decrementEntriesCount(1, $this->entryCategoriesRemovedIds);
			$category->decrementDirectEntriesCount();
		}
	}
	
} // categoryEntry
