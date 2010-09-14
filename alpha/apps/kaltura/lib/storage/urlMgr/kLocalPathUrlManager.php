<?php
class kLocalPathUrlManager extends kUrlManager
{
	/**
	 * Returns the local path with no extension
	 * 
	 * @param FileSync $fileSync
	 * @return string
	 */
	public function getFileSyncUrl(FileSync $fileSync)
	{
		$fileSync = kFileSyncUtils::resolve($fileSync);
		
		$url = $fileSync->getFilePath();
		$url = str_replace('\\', '/', $url);
		
		if($this->protocol == StorageProfile::PLAY_FORMAT_RTMP)
			$url = preg_replace('/\.[\w]+$/', '', $url);
		
		return $url;
	}
}