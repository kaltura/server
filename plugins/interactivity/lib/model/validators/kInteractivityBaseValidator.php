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
}


