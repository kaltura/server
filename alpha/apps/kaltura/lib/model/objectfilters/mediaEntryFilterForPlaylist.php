<?php
/**
 * @package Core
 * @subpackage model.filters
 */
class mediaEntryFilterForPlaylist extends entryFilter
{
	const NAME = "_name";

	public function init()
	{
		parent::init();

		$extendedFields = kArray::makeAssociativeDefaultValue ( array (
			self::NAME,
		) , NULL );

		$this->fields = array_merge($this->fields , $extendedFields);
		$this->fieldsToIgnoreInFinalCriteria = array_merge($this->fieldsToIgnoreInFinalCriteria , $extendedFields);
	}
}