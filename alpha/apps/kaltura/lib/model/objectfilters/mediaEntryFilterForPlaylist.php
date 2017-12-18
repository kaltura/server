<?php
/**
 * @package Core
 * @subpackage model.filters
 */
class mediaEntryFilterForPlaylist extends entryFilter
{
	const NAME = "_name";

	protected function init()
	{
		parent::init();

		$extendedFields = kArray::makeAssociativeDefaultValue ( array (
			self::NAME,
		) , NULL );

		$this->fields = array_merge($this->fields , $extendedFields);
		$this->InitFieldsToIgnoreInFinalCriteria();
	}

	protected function InitFieldsToIgnoreInFinalCriteria()
	{
		parent::InitFieldsToIgnoreInFinalCriteria();
		$this->addFieldToIgnoreInFinalCriteria(self::NAME);
	}
}