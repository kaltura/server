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

}
