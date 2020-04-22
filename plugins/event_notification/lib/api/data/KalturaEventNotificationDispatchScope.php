<?php
/**
 * @package plugins.eventNotification
 * @subpackage api.objects
 */
class KalturaEventNotificationDispatchScope extends KalturaEventNotificationScope
{
	/**
	 * @var KalturaKeyValueArray
	 */
	public $dynamicValues;

	public function toObject($objectToFill = null, $propsToSkip = array())
	{
		$objectToFill = parent::toObject($objectToFill, $propsToSkip);
		if ($this->dynamicValues)
		{
			foreach ($this->dynamicValues as $keyValueObject)
			{
				/* @var $keyValueObject KalturaKeyValue */
				$objectToFill->addDynamicValue($keyValueObject->key, new kStringValue($keyValueObject->value));
			}
		}
		return $objectToFill;
	}

}
