<?php
/**
 * @package plugins.interactivity
 * @subpackage model.items
 */

class kInteractivityNodeValidator extends kInteractivityBaseValidator
{
	const OBJECT_NAME = 'node';
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
		$this->validateMandatoryStringField($data, self::OBJECT_NAME, kInteractivityDataFieldsName::INTERACTION_ID);
		$this->validateMandatoryStringField($data, self::OBJECT_NAME, kInteractivityDataFieldsName::NAME);
		$this->validateOptionalStringField($data, self::OBJECT_NAME, kInteractivityDataFieldsName::ENTRY_ID);
		$this->validateOptionalStringField($data, self::OBJECT_NAME, kInteractivityDataFieldsName::TAGS);

		if(isset($data[kInteractivityDataFieldsName::INTERACTIONS]))
		{
			$this->validateArrayField($data, self::OBJECT_NAME, kInteractivityDataFieldsName::INTERACTIONS);
			foreach ($data[kInteractivityDataFieldsName::INTERACTIONS] as $interaction)
			{
				$this->interactionValidator->validate($interaction);
				$this->interactionsIds[] = $interaction[kInteractivityDataFieldsName::INTERACTION_ID];
			}
		}
	}


	public function getInteractionIds()
	{
		return $this->interactionsIds;
	}
}