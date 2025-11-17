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
	 * @var string
	 */
	protected $language;

	/**
	 * @var int
	 */
	protected $accuracyGreaterThanOrEqual;

	/**
	 * @var int
	 */
	protected $accuracyLessThanOrEqual;

	/**
	 * @var int
	 */
	protected $accuracyGreaterThan;

	/**
	 * @var int
	 */
	protected $accuracyLessThan;

	/**
	 * @var CaptionUsage
	 */
	protected $usage;

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
	 * @return string
	 */
	public function getLanguage()
	{
		return $this->language;
	}

	/**
	 * @param string $language
	 */
	public function setLanguage($language)
	{
		$this->language = $language;
	}

	/**
	 * @return int
	 */
	public function getAccuracyGreaterThanOrEqual()
	{
		return $this->accuracyGreaterThanOrEqual;
	}

	/**
	 * @param int $accuracyGreaterThanOrEqual
	 */
	public function setAccuracyGreaterThanOrEqual($accuracyGreaterThanOrEqual)
	{
		$this->accuracyGreaterThanOrEqual = $accuracyGreaterThanOrEqual;
	}

	/**
	 * @return int
	 */
	public function getAccuracyLessThanOrEqual()
	{
		return $this->accuracyLessThanOrEqual;
	}

	/**
	 * @param int $accuracyLessThanOrEqual
	 */
	public function setAccuracyLessThanOrEqual($accuracyLessThanOrEqual)
	{
		$this->accuracyLessThanOrEqual = $accuracyLessThanOrEqual;
	}

	/**
	 * @return int
	 */
	public function getAccuracyGreaterThan()
	{
		return $this->accuracyGreaterThan;
	}

	/**
	 * @param int $accuracyGreaterThan
	 */
	public function setAccuracyGreaterThan($accuracyGreaterThan)
	{
		$this->accuracyGreaterThan = $accuracyGreaterThan;
	}

	/**
	 * @return int
	 */
	public function getAccuracyLessThan()
	{
		return $this->accuracyLessThan;
	}

	/**
	 * @param int $accuracyLessThan
	 */
	public function setAccuracyLessThan($accuracyLessThan)
	{
		$this->accuracyLessThan = $accuracyLessThan;
	}

	/**
	 * @return CaptionUsage
	 */
	public function getUsage()
	{
		return $this->usage;
	}

	/**
	 * @param CaptionUsage $usage
	 */
	public function setUsage($usage)
	{
		$this->usage = $usage;
	}

	/* (non-PHPdoc)
	 * @see AdvancedSearchFilterItem::applyCondition()
	 */
	public function applyCondition(IKalturaDbQuery $query)
	{
		throw new kESearchException(kESearchException::UNABLE_TO_EXECUTE_ENTRY_CAPTION_ADVANCED_FILTER, kESearchException::UNABLE_TO_EXECUTE_ENTRY_CAPTION_ADVANCED_FILTER);
	}

}
