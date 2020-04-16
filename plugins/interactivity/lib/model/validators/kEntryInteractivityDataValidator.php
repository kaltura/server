<?php
/**
 * @package plugins.interactivity
 * @subpackage model.items
 */

class kEntryInteractivityDataValidator extends kInteractivityDataValidator
{
	public function setUpNodeValidator($entry)
	{
		$this->nodeValidator = new kInteractivityEntryNodeValidator($entry);
	}

	public function validate($data)
	{
		$this->validateMandatoryField($data, self::OBJECT_NAME, self::NODES);
		if(count($data[self::NODES]) != 1)
		{
			throw new kInteractivityException(kInteractivityException::ENTRY_ILLEGAL_NODE_NUMBER, kInteractivityException::ENTRY_ILLEGAL_NODE_NUMBER);
		}

		$this->nodeValidator->validate($data[self::NODES][0]);
	}
}