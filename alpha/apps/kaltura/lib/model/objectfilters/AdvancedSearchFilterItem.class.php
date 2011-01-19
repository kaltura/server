<?php

class AdvancedSearchFilterItem
{
	/**
	 * @var string
	 */
	protected $kalturaClass;
	
	public function apply(baseObjectFilter $filter, Criteria &$criteria, array &$matchClause, array &$whereClause)
	{
		
	}
	
	public function addToXml(SimpleXMLElement &$xmlElement)
	{
		$xmlElement->addAttribute('kalturaClass', $this->kalturaClass);
	}
	
	public function fillObjectFromXml(SimpleXMLElement $xmlElement)
	{
		$attr = $xmlElement->attributes();
		if(isset($attr['kalturaClass']))
			$this->kalturaClass = (string) $attr['kalturaClass'];
	}
	
	/**
	 * @return the $kalturaClass
	 */
	public function getKalturaClass() {
		return $this->kalturaClass;
	}

	/**
	 * @param $kalturaClass the $kalturaClass to set
	 */
	public function setKalturaClass($kalturaClass) {
		$this->kalturaClass = $kalturaClass;
	}
}
