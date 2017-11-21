<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model
 */
class ESearchQueryAttributes
{
	/**
	 * @var array
	 */
	protected $partnerLanguages;

	/**
	 * @var int
	 */
	protected $overrideInnerHitsSize;

	/**
	 * @return array
	 */
	public function getPartnerLanguages()
	{
		return $this->partnerLanguages;
	}

	/**
	 * @param array $partnerLanguages
	 */
	public function setPartnerLanguages($partnerLanguages)
	{
		$this->partnerLanguages = $partnerLanguages;
	}

	/**
	 * @return int
	 */
	public function getOverrideInnerHitsSize()
	{
		return $this->overrideInnerHitsSize;
	}

	/**
	 * @param int $overrideInnerHitsSize
	 */
	public function setOverrideInnerHitsSize($overrideInnerHitsSize)
	{
		$this->overrideInnerHitsSize = $overrideInnerHitsSize;
	}

}
