<?php
/**
 * @package plugins.ask
 * @subpackage model.filters
 */
class kAskAdvancedFilter extends AdvancedSearchFilterItem
{
	/**
	 * @var boolean
	 */
	protected $isAsk = false;

	/**
	 * @return boolean
	 */
	public function getIsAsk()
	{
		return $this->isAsk;
	}

	/**
	 * @param boolean $isAsk
	 */
	public function setIsAsk($isAsk)
	{
		$this->isAsk = $isAsk;
	}

	/* (non-PHPdoc)
	 * @see AdvancedSearchFilterItem::applyCondition()
	 */
	public function applyCondition(IKalturaDbQuery $query)
	{
		$query->addCondition(entryIndex::DYNAMIC_ATTRIBUTES . '.' .  AskPlugin::getDynamicAttributeName() . ' = ' . ( $this->isAsk ?  '1' : '0' ) );
	}

}