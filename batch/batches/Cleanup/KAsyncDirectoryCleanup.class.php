<?php
/**
 * @package Scheduler
 * @subpackage Cleanup
 */

/**
 * Will run periodically and cleanup directories from old files that have a specific pattern (older than x days)
 *
 * @package Scheduler
 * @subpackage Cleanup
 */
class KAsyncDirectoryCleanup extends KPeriodicWorker
{
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::CLEANUP;
	}

	/* (non-PHPdoc)
	 * @see KBatchBase::run()
	*/
	public function run($jobs = null)
	{
		$path = $this->getAdditionalParams('path');
		$pattern = $this->getAdditionalParams('pattern');
		$simulateOnly = $this->getAdditionalParams('simulateOnly');
		$minutesOld = $this->getAdditionalParams('minutesOld');
		$searchPath = $path . $pattern;
		KalturaLog::info('Searching ' . $searchPath);
		$usePHP = $this->getAdditionalParams('usePHP');
		$this->deleteFiles($searchPath, $minutesOld, $simulateOnly, $usePHP);
	}

	/**
	 * @param $searchPath
	 * @param $minutesOld
	 * @param $simulateOnly
	 * @return bool
	 */
	protected function deleteFiles($searchPath, $minutesOld, $simulateOnly, $usePHP)
	{
		$secondsOld = $minutesOld * 60;
		$files = kFile::getFilesByPattern($searchPath);
		KalturaLog::info('Found [' . count($files) . '] to scan');

		$now = time();
		KalturaLog::info('Deleting files that are ' . $secondsOld . ' seconds old (modified before ' . date('c', $now - $secondsOld) . ')');
		foreach ($files as $file)
		{
			$filemtime = kFile::getFileLastUpdatedTime($file);
			if ($filemtime > $now - $secondsOld)
			{
				continue;
			}

			if ($simulateOnly)
			{
				KalturaLog::info('Simulating: Deleting file [' . $file . ' ], it\'s last modification time was ' . date('c', $filemtime));
				continue;
			}

			if (kFile::checkIsDir($file))
			{
				if ($this->shouldDeleteDirectory($file, $now, $secondsOld))
				{
					$this->deleteDirectory($file, $usePHP);
				}
				else
				{
					continue;
				}
			}
			else
			{
				$this->deleteFile($file, $usePHP);
			}
		}
	}

	/**
	 * @param $path
	 * @param $now
	 * @param $secondsOld
	 * @return bool
	 */
	protected function shouldDeleteDirectory($path, $now, $secondsOld)
	{
		if (substr($path, -strlen(KChunkedEncode::CHUNK_ENCODE_POSTFIX)) === KChunkedEncode::CHUNK_ENCODE_POSTFIX)
		{
			foreach (kFile::dirList($path) as $file)
			{
				if (kFile::getFileLastUpdatedTime($file) > $now - $secondsOld)
				{
					return false;
				}
			}
			return true;
		}
		return false;
	}

	/**
	 * @param $dir
	 * @param $usePHP
	 */
	protected function deleteDirectory($dir, $usePHP)
	{
		if ($usePHP)
		{
			$this->deleteDirectoryHelper($dir, $usePHP);
		}
		else
		{
			$command = 'rm -rf ' . $dir;
			KalturaLog::info('Executing command: ' . $command);
			$returnedValue = null;
			passthru($command, $returnedValue);
			if ($returnedValue)
			{
				KalturaLog::err('Error: problem while deleting ' . $dir);
			}
		}
	}

	/**
	 * @param $dir
	 * @param $usePHP
	 * @return bool
	 */
	protected function deleteDirectoryHelper($dir, $usePHP)
	{
		if (!kFile::checkFileExists($dir))
		{
			return true;
		}

		if (!kFile::checkIsDir($dir))
		{
			return $this->deleteFile($dir, $usePHP);
		}

		foreach (kFile::dirList($dir) as $file)
		{
			if (!$this->deleteDirectoryHelper($file, $usePHP))
			{
				return false;
			}
		}
		return kFile::removeDir($dir);
	}

	/**
	 * @param $file
	 * @param $usePHP
	 * @return bool
	 */
	protected function deleteFile($file, $usePHP)
	{
		$res = null;
		if ($usePHP)
		{
			KalturaLog::info('Deleting file [' . $file . '], it\'s last modification time was ' . date('c', filemtime($file)));
			$res = kFile::doDeleteFile($file);
			if ($res)
			{
				return true;
			}
		}
		else
		{
			$command = 'rm -f ' . $file;
			KalturaLog::info('Executing command: ' . $command);
			passthru($command, $res);
			if (!$res)
			{
				return true;
			}
		}
		KalturaLog::err('Error: problem while deleting ' . $file);
		return false;
	}
}
