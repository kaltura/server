<?php
/**
 * @package plugins.contentDistribution 
 * @subpackage Scheduler.Distribute
 */
abstract class SftpDistributionEngine extends DistributionEngine
{
	
	abstract function getTempDirectory();

	/*
 	* Lazy saving of the key to a temporary path, the key will exist in this location until the temp files are purged 
	 */
	protected function getFileLocationForSFTPKey($distributionProfileId, $keyContent, $fileName)
	{
		$tempDirectory = $this->getTempDirectoryForProfile($distributionProfileId);
		$fileLocation = $tempDirectory . $fileName;
		if (!file_exists($fileLocation) || (file_get_contents($fileLocation) !== $keyContent))
		{
			file_put_contents($fileLocation, $keyContent);
			chmod($fileLocation, 0600);
		}

		return $fileLocation;
	}

	/*
 	* Creates and return the temp directory used for this distribution profile 
	 */
	protected function getTempDirectoryForProfile($distributionProfileId)
	{
		$tempFilePath = $this->tempDirectory . '/' . $this->getTempDirectory() . '/' . $distributionProfileId . '/';
		if (!file_exists($tempFilePath))
			mkdir($tempFilePath, 0777, true);
		return $tempFilePath;
	}

}
