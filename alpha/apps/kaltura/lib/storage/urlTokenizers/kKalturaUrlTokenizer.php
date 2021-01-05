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
		$file = substr($url, $lastSlashPosition + 1);
		$ending = '';

		if(preg_match('#/fileName/([^/]+)/#', $path, $matches, PREG_OFFSET_CAPTURE))
		{
			$fileNamePart = $matches[0][0];
			$path = str_replace($fileNamePart, '/', $path);
			$ending .= $fileNamePart;
		}

		if(preg_match('#/dirFileName/([^/]+)/#', $path, $matches, PREG_OFFSET_CAPTURE))
		{
			$fileNamePart = $matches[0][0];
			$path = str_replace($fileNamePart, '/', $path);
			$ending .= $fileNamePart;
		}

		if(!$ending)
		{
			$ending = '/';
		}
		$ending .= $file;

		$expiry = kApiCache::getTime() + $this->getWindow();
		$path .= '/exp/' . $expiry;

		$signature = kDeliveryUtils::urlsafeB64Encode(hash_hmac('sha256', $path, $this->key, true));
		return $path . '/sig/' . $signature . $ending;
	}

}