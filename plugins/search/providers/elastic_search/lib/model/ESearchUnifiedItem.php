<?php

class ESearchUnifiedItem extends ESearchItem
{

	/**
	 * @var string
	 */
	protected $searchTerm;

	/**
	 * @return string
	 */
	public function getSearchTerm()
	{
		return $this->searchTerm;
	}

	/**
	 * @param string $searchTerm
	 */
	public function setSearchTerm($searchTerm)
	{
		$this->searchTerm = $searchTerm;
	}

	public function getType()
	{
		return 'unified';
	}

//	public static function getAallowedSearchTypesForField()
//	{
////		$reflector = KalturaTypeReflectorCacher::get('ESearchItem');
////		$subClasses = $reflector->getSubTypesNames();
//		$subClasses = array('ESearchEntryItem','ESearchCuePointItem','ESearchMetadataItem','ESearchCaptionItem');
//		$allowedFields = array();
//		foreach ($subClasses as $subClass)
//		{
//			if ($subClass != get_class($this))
//			{
//				$allowedFields = array_merge($allowedFields, $subClass::getAallowedSearchTypesForField());
//			}
//		}
//		return $allowedFields;
//	}


}