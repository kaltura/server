<?php
/**
 * @package plugins.interactivity
 * @subpackage model.items
 */

class kInteractivityDataValidator extends kInteractivityBaseValidator
{
	const NODES = 'nodes';
	const OBJECT_NAME = 'InteractivityData';

	/** @var IInteractivityDataValidator*/
	protected $nodeValidator;

	/**
	 * kEntryInteractivityDataValidator constructor.
	 * @param entry $entry
	 */
	public function __construct($entry)
	{
		parent::__construct($entry);
		$this->setUpNodeValidator($entry);
	}

	public function setUpNodeValidator($entry)
	{
		$this->nodeValidator = new kInteractivityNodeValidator($entry);
	}

	public function validate($data)
	{
		$this->validateMandatoryField($data, self::OBJECT_NAME, self::NODES);

		foreach ($data[self::NODES] as $node)
		{
			$this->nodeValidator->validate($node);
		}
	}
}