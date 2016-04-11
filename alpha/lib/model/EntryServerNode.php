<?php


/**
 * Skeleton subclass for representing a row from the 'entry_server_node' table.
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
class EntryServerNode extends BaseEntryServerNode {

	public function getCacheInvalidationKeys()
	{
		return array("entryServerNode:entryId".strtolower($this->getEntryId()));
	}
} // EntryServerNode
