<?php
class kConfControlDb
{
	/**
	 * Insert new map to the database
	 * @param $mapName - name of the map
	 * @param $hostNameRegex - regex of related host
	 * @return the latest version of the map
	 */
	protected function getLatestVersion($mapName , $hostNameRegex)
	{
		$mapRecord = ConfMapsPeer::getLatestMap($mapName,$hostNameRegex);
		$version = $mapRecord->getVersion();
		KalturaLog::debug("Found version - {$version} for map {$mapName} hostNameRegex {$hostNameRegex}");
		return $version;
	}
}