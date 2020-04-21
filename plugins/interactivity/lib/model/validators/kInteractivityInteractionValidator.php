<?php
/**
 * @package plugins.interactivity
 * @subpackage model.items
 */

class kInteractivityInteractionValidator extends kInteractivityBaseValidator
{
	const ID = 'id';
	const TYPE = 'type';
	const TAGS = 'tags';
	const START_TIME = 'startTime';
	const END_TIME = 'endTime';
	const OBJECT_NAME = 'interaction';

	/**
	 * @param array $data
	 * @throws kInteractivityException
	 */
	public function validate($data)
	{
		$this->validateMandatoryStringField($data, self::OBJECT_NAME, self::ID);
		$this->validateMandatoryStringField($data, self::OBJECT_NAME, self::TYPE);
		$this->validateOptionalStringField($data, self::OBJECT_NAME, self::TAGS);
		$this->validateTimeFields($data);
	}

	/**
	 * @param array $data
	 * @throws kInteractivityException
	 */
	protected function validateTimeFields($data)
	{
		if(isset($data[self::START_TIME]) && !is_numeric($data[self::START_TIME]))
		{
			$data = array(kInteractivityErrorMessages::ERR_MSG => 'start time ' . kInteractivityErrorMessages::LEGAL_TIME_FORMAT);
			throw new kInteractivityException(kInteractivityException::ILLEGAL_FIELD_VALUE, kInteractivityException::ILLEGAL_FIELD_VALUE, $data);
		}

		if(isset($data[self::END_TIME]) && !is_numeric($data[self::END_TIME]))
		{
			$data = array(kInteractivityErrorMessages::ERR_MSG => 'end time ' . kInteractivityErrorMessages::LEGAL_TIME_FORMAT);
			throw new kInteractivityException(kInteractivityException::ILLEGAL_FIELD_VALUE, kInteractivityException::ILLEGAL_FIELD_VALUE, $data);
		}
	}
}