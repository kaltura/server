<?php


class kKalturaUrlTokenizer extends kUrlTokenizer
{
	/**
	 * @param string $url
	 * @param string $urlPrefix
	 * @return string
	 */
	public function tokenizeSingleUrl($url, $urlPrefix = null)
	{
		$lastSlashPosition = strrpos($url, "/");
		$path = substr($url, 0, $lastSlashPosition);
		$fileName = substr($url, $lastSlashPosition + 1);

		$expiry = $this->getExpiry();
		$path .= '/exp/' . $expiry;

		$signature = kDeliveryUtils::urlsafeB64Encode(hash_hmac('sha256', $path, $this->key, true));

		return $path . '/sig/' . $signature . '/' . $fileName;
	}

	protected function getExpiry()
	{
		$expiry = time() + 86400;
		$ksObj = kCurrentContext::$ks_object;
		if($ksObj)
		{
			$expiry = $ksObj->valid_until;
		}
		return $expiry;
	}

}