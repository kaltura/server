<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaScope extends KalturaObject
{
	/**
	 * @var int
	 */
	public $time;

	/**
	 * @var KalturaKeyValueArray
	 */
	public $dynamicValues;

	/**
	 * @var string
	 */
	public $entryId;

	public function toObject($objectToFill = null, $propsToSkip = array())
	{
		if (is_null($objectToFill))
			$objectToFill = new kScope();

		/** @var kScope $objectToFill */
		$objectToFill = parent::toObject($objectToFill, $propsToSkip);

		if (!is_null($this->dynamicValues))
		{
			foreach($this->dynamicValues as $keyValue)
			{
				$objectToFill->addDynamicValue($keyValue->key, new kStringValue($keyValue->value));
			}
		}

		return $objectToFill;
	}
}