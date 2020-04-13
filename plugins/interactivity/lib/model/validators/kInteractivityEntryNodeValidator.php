<?php
/**
 * @package plugins.interactivity
 * @subpackage model.items
 */

class kInteractivityEntryNodeValidator extends kInteractivityNodeValidator
{
	public function validate($data)
	{
		parent::validate($data);

		if($data[self::ENTRY_ID] != $this->entry->getEntryId())
		{
			throw new kInteractivityException(kInteractivityException::ILLEGAL_ENTRY_NODE_ENTRY_ID, kInteractivityException::ILLEGAL_ENTRY_NODE_ENTRY_ID);
		}
	}
}