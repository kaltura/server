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
		$signature = kDeliveryUtils::urlsafeB64Encode(hash_hmac('sha256', $path, $this->key));
		return $path . '/sig/' . $signature . '/' . $fileName;
	}

}