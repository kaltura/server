<?php
/**
 * @package plugins.interactivity
 * @subpackage model.items
 */

class kInteractivityEntryNodeValidator extends kInteractivityNodeValidator
{
	public function validate($data)
	{
		if(isset($data[self::ENTRY_ID]))
		{
			throw new kInteractivityException(kInteractivityException::ILLEGAL_ENTRY_NODE_ENTRY_ID, kInteractivityException::ILLEGAL_ENTRY_NODE_ENTRY_ID);
		}

		parent::validate($data);
	}
}