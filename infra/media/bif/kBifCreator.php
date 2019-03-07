<?php
/**
 * @package infra
 * @subpackage Media
 */
class kBifCreator
{
	/*
	FORMAT:
	Magic Number: 0x89,0x42,0x49,0x46,0x0d,0x0a,0x1a,0x0a
	Version (currently 0): byte 8 - 11
	Number of BIF images (unsigned 32-bit value): byte 12 - 15
	Framewise Separation (Timestamp Multiplier): byte 16 - 19
	Reserved (for future expansion. They shall be 0x00): byte 20 ... 63
	BIF index ( N+1 entries. Each entry contains two unsigned 32-bit values):
	----------------------------------------------------------
	byte      | 64 65 66 67	        | 68 69 70 71
	index 0   | 0 Frame 0 timestamp	| absolute offset of frame
	index 1   | 1 Frame 1 timestamp	| absolute offset of frame
	...
	index N-1 | Frame N-1 timestamp	| absolute offset of frame
	index N	  | 0xffffffff      	| last byte of data + 1
	----------------------------------------------------------
	Data section: contains the BIF images

	All multibyte integers are stored in little-endian format.
	*/

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
		$timestampIndex = 1;

		foreach ($images as $image)
		{
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
		$interval = max(1, floor($lengthInSec / $vid_slices));

		return $interval;
	}
}