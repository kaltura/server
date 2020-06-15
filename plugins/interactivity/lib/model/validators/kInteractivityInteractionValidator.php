<?php
/**
 * @package plugins.interactivity
 * @subpackage model.items
 */

class kInteractivityInteractionValidator extends kInteractivityBaseValidator
{
	const OBJECT_NAME = 'interaction';

	/**
	 * @param array $data
	 * @throws kInteractivityException
	 */
	public function validate($data)
	{
		$this->validateMandatoryStringField($data, self::OBJECT_NAME, kInteractivityDataFieldsName::INTERACTION_ID);
		$this->validateMandatoryStringField($data, self::OBJECT_NAME, kInteractivityDataFieldsName::TYPE);
		$this->validateOptionalStringField($data, self::OBJECT_NAME, kInteractivityDataFieldsName::TAGS);
		$this->validateTimeFields($data);
	}

	/**
	 * @param array $data
	 * @throws kInteractivityException
	 */
	protected function validateTimeFields($data)
	{
		if(isset($data[kInteractivityDataFieldsName::START_TIME]) && !is_int($data[kInteractivityDataFieldsName::START_TIME]))
		{
			$data = array(kInteractivityErrorMessages::ERR_MSG => 'start time ' . kInteractivityErrorMessages::LEGAL_TIME_FORMAT);
			throw new kInteractivityException(kInteractivityException::ILLEGAL_FIELD_VALUE, kInteractivityException::ILLEGAL_FIELD_VALUE, $data);
		}

		if(isset($data[kInteractivityDataFieldsName::END_TIME]) && !is_int($data[kInteractivityDataFieldsName::END_TIME]))
		{
			$data = array(kInteractivityErrorMessages::ERR_MSG => 'end time ' . kInteractivityErrorMessages::LEGAL_TIME_FORMAT);
			throw new kInteractivityException(kInteractivityException::ILLEGAL_FIELD_VALUE, kInteractivityException::ILLEGAL_FIELD_VALUE, $data);
		}
	}
}