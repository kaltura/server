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
	const CHUNK_ENCODEING_POSTFIX = 'chunkenc';

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
		$files = glob($searchPath);
		KalturaLog::info('Found [' . count($files) . '] to scan');

		$now = time();
		KalturaLog::info('Deleting files that are ' . $secondsOld . ' seconds old (modified before ' . date('c', $now - $secondsOld) . ')');
		foreach ($files as $file)
		{
			$filemtime = filemtime($file);
			if ($filemtime > $now - $secondsOld)
			{
				continue;
			}

			if ($simulateOnly)
			{
				KalturaLog::info('Simulating: Deleting file [' . $file . ' ], it\'s last modification time was ' . date('c', $filemtime));
				continue;
			}

			if (is_dir($file))
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
		if (substr($path, -strlen(self::CHUNK_ENCODEING_POSTFIX)) === self::CHUNK_ENCODEING_POSTFIX)
		{
			foreach (scandir($path) as $file)
			{
				if ($file == '.' || $file == '..')
				{
					continue;
				}

				$fullFilePath = $path . DIRECTORY_SEPARATOR . $file;
				if (filemtime($fullFilePath) > $now - $secondsOld)
				{
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * @param $dir
	 * @param $usePHP
	 */
	private function deleteDirectory($dir, $usePHP)
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
			KalturaLog::info('Returned value [' . $returnedValue . ']');
		}
	}

	/**
	 * @param $dir
	 * @param $usePHP
	 * @return bool
	 */
	private function deleteDirectoryHelper($dir, $usePHP)
	{
		if (!file_exists($dir))
		{
			return true;
		}

		if (!is_dir($dir))
		{
			return $this->deleteFile($dir, $usePHP);
		}

		foreach (scandir($dir) as $item)
		{
			if ($item == '.' || $item == '..')
			{
				continue;
			}
			if (!$this->deleteDirectoryHelper($dir . DIRECTORY_SEPARATOR . $item, $usePHP))
			{
				return false;
			}
		}
		return @rmdir($dir);
	}

	/**
	 * @param $file
	 * @param $usePHP
	 * @return bool
	 */
	private function deleteFile($file, $usePHP)
	{
		$res = null;
		if ($usePHP)
		{
			KalturaLog::info('Deleting file [' . $file . '], it\'s last modification time was ' . date('c', filemtime($file)));
			$res = @unlink($file);
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
			KalturaLog::info('Returned value [' . $res . ']');
			if (!$res)
			{
				return true;
			}
		}
		KalturaLog::err('Error: problem while deleting ' . $file);
		return false;
	}
}
