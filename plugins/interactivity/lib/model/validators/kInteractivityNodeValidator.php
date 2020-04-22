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
	const TAGS = 'tags';

	protected $interactionValidator;
	protected $interactionsIds;

	/**
	 * kInteractivityBaseNodeValidator constructor.
	 * @param $entry
	 */
	public function __construct($entry)
	{
		parent::__construct($entry);
		$this->interactionValidator = new kInteractivityInteractionValidator($entry);
	}

	/**
	 * @param array $data
	 * @throws kInteractivityException
	 */
	public function validate($data)
	{
		$this->interactionsIds = array();
		$this->validateMandatoryStringField($data, self::OBJECT_NAME, self::ID);
		$this->validateMandatoryStringField($data, self::OBJECT_NAME, self::NAME);
		$this->validateOptionalStringField($data, self::OBJECT_NAME, self::ENTRY_ID);
		$this->validateOptionalStringField($data, self::OBJECT_NAME, self::TAGS);

		if(isset($data[self::INTERACTIONS]))
		{
			$this->validateArrayField($data, self::OBJECT_NAME, self::INTERACTIONS);
			foreach ($data[self::INTERACTIONS] as $interaction)
			{
				$this->interactionValidator->validate($interaction);
				$this->interactionsIds[] = $interaction[$this->interactionValidator::ID];
			}
		}
	}


	public function getInteractionIds()
	{
		return $this->interactionsIds;
	}
}