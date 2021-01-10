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
		$pathToSign = $url;
		$lastSlashPosition = strrpos($url, "/");
		$file = substr($url, $lastSlashPosition + 1);
		$ending = '/' . $file;

		if(preg_match('#/fileName/([^/]+)/#', $pathToSign, $matches, PREG_OFFSET_CAPTURE))
		{
			$fileNamePart = $matches[0][0];
			$pathToSign = str_replace($fileNamePart, '/', $pathToSign);
			$ending = $fileNamePart  . ltrim($ending, '/');
		}

		if(preg_match('#/dirFileName/([^/]+)/#', $pathToSign, $matches, PREG_OFFSET_CAPTURE))
		{
			$fileNamePart = $matches[0][0];
			$pathToSign = str_replace($fileNamePart, '/', $pathToSign);
			$ending = $fileNamePart . ltrim($ending, '/');
		}

		$expiry = kApiCache::getTime() + $this->getWindow();
		$pathToSign .= '/exp/' . $expiry;

		$signature = kDeliveryUtils::urlsafeB64Encode(hash_hmac('sha256', $pathToSign, $this->key, true));
		return $pathToSign . '/sig/' . $signature . $ending;
	}

}