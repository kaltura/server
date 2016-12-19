<?php

class kKsUrlTokenizer extends kUrlTokenizer
{
	/**
	 * @var bool
	 */
	protected $usePath;
	
	/**
	 * @return $usePath
	 */
	public function getUsePath() 
	{
		return $this->usePath;
	}
	
	/**
	 * @param bool $usePath
	 */
	public function setUsePath($usePath) 
	{
		$this->usePath = $usePath;
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
			require_once(__DIR__ . '/../../KExternalErrors.class.php');
			
			KExternalErrors::dieError(KExternalErrors::MISSING_PARAMETER, 'ks user');
		}
		
		$uriRestrict = explode(',', $url);		// cannot contain commas, since it's used as the privileges delimiter
		$uriRestrict = $uriRestrict[0];
		
		$privileges = kSessionBase::PRIVILEGE_DISABLE_ENTITLEMENT_FOR_ENTRY . ':' . $this->entryId;
		$privileges .= ',' . kSessionBase::PRIVILEGE_VIEW . ':' . $this->entryId;
		$privileges .= ',' . kSessionBase::PRIVILEGE_URI_RESTRICTION . ':' . $uriRestrict . '*';
		if ($this->limitIpAddress)
		{
			$privileges .= ',' . kSessionBase::PRIVILEGE_IP_RESTRICTION . ':' . infraRequestUtils::getRemoteAddress();
		}

		$ks = kSessionBase::generateKsV2(
			$this->key, 
			$this->ksObject->user, 
			kSessionBase::SESSION_TYPE_USER, 
			$this->partnerId, 
			$this->window, 
			$privileges, 
			null, 
			null);

		if ($this->usePath)
		{
			$insertPos = strpos($url, '/name/');
			if ($insertPos !== false)
			{
				return substr($url, 0, $insertPos) . '/ks/' . $ks . substr($url, $insertPos);
			}
		}
		return $url . '?ks=' . $ks;
	}
}
