<?php
/**
 * @package plugins.interactivity
 * @subpackage model.items
 */

class kInteractivityNodeValidator extends kInteractivityBaseValidator
{
	const ID = 'id';
	const NAME = 'name';
	const ENTRY_ID = 'entryId';
	const OBJECT_NAME = 'node';
	const INTERACTIONS = 'interactions';

	protected $interactionValidator;

	/**
	 * kInteractivityBaseNodeValidator constructor.
	 * @param $entry
	 */
	public function __construct($entry)
	{
		parent::__construct($entry);
		$this->interactionValidator = new kInteractivityInteractionValidator($entry);
	}

	public function validate($data)
	{
		$this->validateMandatoryField($data, self::OBJECT_NAME, self::ID);
		$this->validateMandatoryField($data, self::OBJECT_NAME, self::NAME);
		$this->validateMandatoryField($data, self::OBJECT_NAME, self::ENTRY_ID);
		$this->validateMandatoryField($data, self::OBJECT_NAME, self::INTERACTIONS);

		foreach ($data[self::INTERACTIONS] as $interaction)
		{
			$this->interactionValidator->validate($interaction);
		}
	}
}