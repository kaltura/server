<?php
/**
 * @package plugins.interactivity
 * @subpackage model.items
 */

abstract class kInteractivityBaseValidator implements IInteractivityDataValidator
{
	protected $entry;

	/**
	 * kInteractivityBaseValidator constructor.
	 * @param entry $entry
	 */
	public function __construct($entry)
	{
		$this->entry = $entry;
	}


	/**
	 * @param array $data
	 * @param string $objectName
	 * @param string $fieldName
	 * @throws kInteractivityException
	 */
	protected function validateMandatoryField($data, $objectName, $fieldName)
	{
		if(empty($data[$fieldName]))
		{
			$data = array(kInteractivityErrorMessages::MISSING_PARAMETER => "{$objectName} {$fieldName}");
			throw new kInteractivityException(kInteractivityException::MISSING_MANDATORY_PARAMETERS, kInteractivityException::MISSING_MANDATORY_PARAMETERS, $data);
		}
	}

	/**
	 * @param array $data
	 * @param string $objectName
	 * @param string $fieldName
	 * @throws kInteractivityException
	 */
	protected function validateMandatoryStringField($data, $objectName, $fieldName)
	{
		$this->validateMandatoryField($data, $objectName, $fieldName);
		$this->validateStringField($data, $objectName, $fieldName);
	}

	/**
	 * @param array $data
	 * @param string $objectName
	 * @param string $fieldName
	 * @throws kInteractivityException
	 */
	protected function validateOptionalStringField($data, $objectName, $fieldName)
	{
		if(isset($data[$fieldName]))
		{
			$this->validateStringField($data, $objectName, $fieldName);
		}
	}

	/**
	 * @param array $data
	 * @param string $objectName
	 * @param string $fieldName
	 * @throws kInteractivityException
	 */
	protected function validateArrayField($data, $objectName, $fieldName)
	{
		if(!is_array($data[$fieldName]))
		{
			$data = array(kInteractivityErrorMessages::ERR_MSG => "{$objectName} {$fieldName} " . kInteractivityErrorMessages::ARRAY_VALUE);
			throw new kInteractivityException(kInteractivityException::ILLEGAL_FIELD_VALUE, kInteractivityException::ILLEGAL_FIELD_VALUE, $data);
		}
	}

	/**
	 * @param array $data
	 * @param string $objectName
	 * @param string $fieldName
	 * @throws kInteractivityException
	 */
	protected function validateStringField($data, $objectName, $fieldName)
	{
		if(!is_string($data[$fieldName]))
		{
			$data = array(kInteractivityErrorMessages::ERR_MSG => "{$objectName} {$fieldName} " . kInteractivityErrorMessages::STRING_VALUE);
			throw new kInteractivityException(kInteractivityException::ILLEGAL_FIELD_VALUE, kInteractivityException::ILLEGAL_FIELD_VALUE, $data);
		}
	}
}


