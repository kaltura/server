<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.filters
 */
class kEntryCaptionAdvancedFilter extends AdvancedSearchFilterItem
{
	/**
	 * @var boolean
	 */
	protected $hasCaption = true;

	/**
	 * @var KalturaLanguage
	 */
	protected $language;


	/**
	 * @return boolean
	 */
	public function getHasCaption()
	{
		return $this->hasCaption;
	}

	/**
	 * @param boolean $hasCaption
	 */
	public function setHasCaption($hasCaption)
	{
		$this->hasCaption = $hasCaption;
	}

	/**
	 * @return KalturaLanguage
	 */
	public function getLanguage()
	{
		return $this->language;
	}

	/**
	 * @param KalturaLanguage $language
	 */
	public function setLanguage($language)
	{
		$this->language = $language;
	}

	/* (non-PHPdoc)
	 * @see AdvancedSearchFilterItem::applyCondition()
	 */
	public function applyCondition(IKalturaDbQuery $query)
	{
		throw new kESearchException(kESearchException::UNABLE_TO_EXECUTE_ENTRY_CAPTION_ADVANCED_FILTER, kESearchException::UNABLE_TO_EXECUTE_ENTRY_CAPTION_ADVANCED_FILTER);
	}

}