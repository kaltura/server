<?php

abstract class ESearchItem extends BaseObject
{

	/**
	 * @var ESearchItemType
	 */
	protected $itemType;

	/**
	 * @return ESearchItemType
	 */
	public function getItemType()
	{
		return $this->itemType;
	}

	/**
	 * @param ESearchItemType $itemType
	 */
	public function setItemType($itemType)
	{
		$this->itemType = $itemType;
	}

	/**
	 * @return null|string
	 */
	public function getQueryVerbs()
	{
		$queryVerb = null;
		switch ($this->getItemType())
		{
			case ESearchItemType::EXACT_MATCH:
				$queryVerb = array('must','term');
				break;
			case ESearchItemType::PARTIAL:
				$queryVerb = array('must','match');
				break;
			case ESearchItemType::STARTS_WITH:
				$queryVerb = array('must','prefix');
				break;
			case ESearchItemType::DOESNT_CONTAIN:
				$queryVerb = array('must_not','term');
				break;
		}
		return $queryVerb;
	}

	abstract public function getType();

	public static function getAallowedSearchTypesForField()
	{
		return array();
	}

}