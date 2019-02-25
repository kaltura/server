<?php
/**
 * @package infra
 * @subpackage Media
 */
class kBifCreator
{
	protected $imagesPaths;
	protected $targetPath;
	protected $interval;

	/**
	 * @param array $imagesPaths
	 * @param string $targetPath
	 * @param int $interval
	 */
	public function __construct($imagesPaths, $targetPath, $interval)
	{
		$this->imagesPaths = $imagesPaths;
		$this->targetPath = $targetPath;
		$this->interval = $interval;
	}

	public function createBif()
	{
		$targetFile = fopen($this->targetPath, "wb");
		if(!$targetFile)
		{
			throw new Exception("Cannot open target file [$this->targetPath]", -1);
		}

		$magicNumber = pack('C*', 0x89, 0x42, 0x49, 0x46, 0x0d, 0x0a, 0x01a, 0x0a);
		$version =  pack('V', 0);
		$images = $this->imagesPaths;
		$imagesLen = pack('V', sizeof($images));
		$intervalInMs = pack('V', 1000 * $this->interval);
		KalturaLog::debug("Number of images: [". sizeof($images) . "] with interval in sec:[$this->interval]");

		fwrite($targetFile, $magicNumber);
		fwrite($targetFile, $version);
		fwrite($targetFile, $imagesLen);
		fwrite($targetFile, $intervalInMs);
		for ($i = 20; $i < 64; $i++)
		{
			fwrite($targetFile, pack('C*', 0x00));
		}

		$bifTableSize = 8 + (8 * sizeof($images));
		$imageOffset = 64 + $bifTableSize;
		$timestampIndex = 0;

		foreach ($images as $image)
		{
			KalturaLog::debug("Timestamp index: [$timestampIndex] Image path: [$image] Image offset:[$imageOffset]");
			if(!file_exists($image))
			{
				KalturaLog::debug("Failed to get file [$image]");
				throw new Exception("BIF frame [$image] is missing", -1);
			}
			$currentFileSize = filesize($image);
			fwrite($targetFile, pack('V', $timestampIndex));
			fwrite($targetFile, pack('V', $imageOffset));

			$timestampIndex+=1;
			$imageOffset+=$currentFileSize;
		}

		fwrite($targetFile, pack('V', 0xffffffff));
		fwrite($targetFile, pack('V', $imageOffset));

		foreach ($images as $image)
		{
			$content = file_get_contents($image);
			$success = fwrite($targetFile, $content);
			if(!$success)
			{
				throw new Exception("Cannot write file content [$image]", -1);
			}
		}

		fclose($targetFile);
	}

	public static function calculateBifInterval($lengthInSec, $vid_slices, $offset)
	{
		$lengthInSec = $lengthInSec - $offset;
		$interval = floor($lengthInSec / $vid_slices);
		if ($interval < 1)
		{
			$interval = 1;
		}
		return $interval;
	}
}