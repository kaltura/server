<?php
/**
 * @service captureSpace
 * @package plugins.captureSpace
 * @subpackage api.services
 */
class CaptureSpaceService extends KalturaBaseService
{
	/**
	 * Returns latest version and URL
	 *
	 * @action clientUpdates
	 * @param string $os
	 * @param string $version
	 * @param KalturaCaptureSpaceHashAlgorithm $hashAlgorithm
	 * @return KalturaCaptureSpaceUpdateResponse
	 * @ksIgnored
	 * 
	 * @throws CaptureSpaceErrors::ALREADY_LATEST_VERSION
	 * @throws CaptureSpaceErrors::NO_UPDATE_IS_AVAILABLE
	 */
	function clientUpdatesAction ($os, $version, $hashAlgorithm = KalturaCaptureSpaceHashAlgorithm::MD5)
	{
		$hashValue = kCaptureSpaceVersionManager::getUpdateHash($os, $version, $hashAlgorithm);
		if (!$hashValue) {
			throw new KalturaAPIException(CaptureSpaceErrors::NO_UPDATE_IS_AVAILABLE, $version, $os);
		}
			
		$path = "/api_v3/service/captureSpace_captureSpace/action/serveUpdate/os/$os/version/$version";
		$downloadUrl = myPartnerUtils::getCdnHost(null) . $path;
		
		$info = new KalturaCaptureSpaceUpdateResponseInfo();
		$info->url = $downloadUrl;
		$info->hash = new KalturaCaptureSpaceUpdateResponseInfoHash();
		$info->hash->algorithm = $hashAlgorithm;
		$info->hash->value = $hashValue;
		
		$response = new KalturaCaptureSpaceUpdateResponse();
		$response->info = $info;
		
		return $response;
	}

	/**
	 * Serve installation file
	 *
	 * @action serveInstall
	 * @param string $os
	 * @return file
	 * @ksIgnored
	 * 
	 * @throws CaptureSpaceErrors::NO_INSTALL_IS_AVAILABLE
	 */
	public function serveInstallAction($os)
	{
		$filename = kCaptureSpaceVersionManager::getInstallFile($os);
		if (!$filename) {
			throw new KalturaAPIException(CaptureSpaceErrors::NO_INSTALL_IS_AVAILABLE, $os);
		}
		$actualFilePath = kCaptureSpaceVersionManager::getActualFilePath($filename);
		if (!$actualFilePath)
			throw new KalturaAPIException(CaptureSpaceErrors::NO_INSTALL_IS_AVAILABLE, $os);

		$mimeType = kFile::mimeType($actualFilePath);
		header("Content-Disposition: attachment; filename=\"$filename\"");
		return $this->dumpFile($actualFilePath, $mimeType);
	}


	/**
	 * Serve update file
	 *
	 * @action serveUpdate
	 * @param string $os
	 * @param string $version
	 * @return file
	 * @ksIgnored
	 * 
	 * @throws CaptureSpaceErrors::NO_UPDATE_IS_AVAILABLE
	 */
	public function serveUpdateAction($os, $version)
	{
		$filename = kCaptureSpaceVersionManager::getUpdateFile($os, $version);
		if (!$filename) {
			throw new KalturaAPIException(CaptureSpaceErrors::NO_UPDATE_IS_AVAILABLE, $version, $os);
		}
		
		$actualFilePath = myContentStorage::getFSContentRootPath() . "/content/third_party/capturespace/$filename";
		if (!file_exists($actualFilePath)) {
			throw new KalturaAPIException(CaptureSpaceErrors::NO_UPDATE_IS_AVAILABLE, $version, $os);
		}
		
		$mimeType = kFile::mimeType($actualFilePath);
		header("Content-Disposition: attachment; filename=\"$filename\"");
		return $this->dumpFile($actualFilePath, $mimeType);
	}
}


