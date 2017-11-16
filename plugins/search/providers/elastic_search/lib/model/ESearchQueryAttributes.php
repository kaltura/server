<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model
 */
class ESearchQueryAttributes
{
	const HIGHLIGHT_GLOBAL_SCOPE = "global";
	const HIGHLIGHT_INNER_SCOPE = "inner";

	/**
	 * @var array
	 */
	protected $partnerLanguages;

	/**
	 * @var array
	 */
	private $fieldsToHighlight = array(self::HIGHLIGHT_GLOBAL_SCOPE => array(), self::HIGHLIGHT_INNER_SCOPE => array());

	private $highlightScope = self::HIGHLIGHT_GLOBAL_SCOPE;

	private $useHighlight = true;

	/**
	 * @param string $scope should be one of the scopes globals
	 */
	public function setHighlightScope($scope)
	{
		if($this->highlightScope != $scope)
		{
			$this->highlightScope = $scope;
			if($scope == self::HIGHLIGHT_GLOBAL_SCOPE) //if we changed from inner to global we need to clear the inner values;
			{
				$this->fieldsToHighlight[self::HIGHLIGHT_INNER_SCOPE] = array();
			}
		}
	}

	/**
	 * @return array
	 */
	public function getFieldsToHighlight()
	{
		return $this->fieldsToHighlight[$this->highlightScope];
	}

	/**
	 * @param string $field
	 */
	public function addFieldToHighlight($field)
	{
		if(!array_key_exists($field ,$this->fieldsToHighlight[$this->highlightScope]))
		{
			$this->fieldsToHighlight[$this->highlightScope][$field] = new stdClass();
		}
	}

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

	public function setUseHighlight($useHighlight)
	{
		$this->useHighlight = $useHighlight;
	}

	public function getUseHighlight()
	{
		return $this->useHighlight;
	}
}
