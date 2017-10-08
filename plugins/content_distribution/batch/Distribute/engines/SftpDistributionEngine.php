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
		$content = kFile::getFileContent($fileLocation);
		if (!$content || $content !== $keyContent)
		{
			kFile::safeFilePutContents($fileLocation, $keyContent, 0600);
		}
		return $fileLocation;
	}

	/*
 	* Creates and return the temp directory used for this distribution profile 
	 */
	protected function getTempDirectoryForProfile($distributionProfileId)
	{
		$tempFilePath = $this->tempDirectory . '/' . $this->getTempDirectory() . '/' . $distributionProfileId . '/';
		kFile::fullMkdir($tempFilePath,0777, true);
		return $tempFilePath;
	}

}
