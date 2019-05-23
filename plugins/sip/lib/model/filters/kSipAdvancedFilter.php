<?php
/**
 * @package plugins.sip
 * @subpackage model.filters
 */
class kSipAdvancedFilter extends AdvancedSearchFilterItem
{
	/**
	 * @var string
	 */
	protected $sipToken;

	/**
	 * @return string
	 */
	public function getSipToken()
	{
		return $this->sipToken;
	}

	/**
	 * @param string $sipToken
	 */
	public function setSipToken($sipToken)
	{
		$this->sipToken = $sipToken;
	}

	/* (non-PHPdoc)
	 * @see AdvancedSearchFilterItem::applyCondition()
	 */
	public function applyCondition(IKalturaDbQuery $query)
	{
		if ($query instanceof IKalturaIndexQuery)
		{
			$searchData = SipPlugin::getSipTokenSearchData($this->sipToken);
			$query->addMatch("(@plugins_data $searchData)");
		}
	}
}
