<?php
/**
 * @package plugins.forensicWatermark
 * @subpackage storage
 */
class kKsUrlTokenizer extends kUrlTokenizer
{
	/**
	 * @var string
	 */
	protected $entryId;

	/**
	 * @var int
	 */
	protected $partnerId;
	
	/**
	 * @param string $entryId
	 */
	public function setEntryId($entryId)
	{
		$this->entryId = $entryId;
	}

	/**
	 * @param string $partnerId
	 */
	public function setPartnerId($partnerId)
	{
		$this->partnerId = $partnerId;
	}
	
	/**
	 * @param string $url
	 * @param string $urlPrefix
	 * @return string
	 */
	public function tokenizeSingleUrl($url, $urlPrefix = null)
	{
		if (!$this->ksObject || !$this->ksObject->user)
		{
			KExternalErrors::dieError(KExternalErrors::MISSING_PARAMETER, 'ks user');
		}
		
		$privileges = kSessionBase::PRIVILEGE_DISABLE_ENTITLEMENT_FOR_ENTRY . ':' . $this->entryId;
		$privileges .= ',' . kSessionBase::PRIVILEGE_VIEW . ':' . $this->entryId;

		$ks = kSessionBase::generateKsV2(
			$this->key, 
			$this->ksObject->user, 
			kSessionBase::SESSION_TYPE_USER, 
			$this->partnerId, 
			$this->window, 
			$privileges, 
			null, 
			null);
		
		return $url . '?ks=' . $ks;
	}
}
