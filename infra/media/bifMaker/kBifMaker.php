<?php
/**
 * @package infra
 * @subpackage Media
 */
class kBifMaker
{
	/**
	 * @var string
	 */
	protected $srcPath;
	protected $targetPath;

	const JPG_SUFFIX = '.jpg';

	/**
	 * @param string $srcPath
	 * @param string $targetPath
	 */
	public function __construct($srcPath, $targetPath)
	{
		if (!file_exists($srcPath))
			throw new Exception("Source path [$srcPath] not found");

		$this->srcPath = $srcPath;
		$this->targetPath = $targetPath;
	}

	public function createBif($interval)
	{
		$targetFile = @fopen($this->targetPath, "wb");

		$magicNumber = pack('c*', 0x89, 0x42, 0x49, 0x46, 0x0d, 0x0a, 0x01a, 0x0a);
		$version =  pack('V', 0);

		$files = scandir($this->srcPath);
		$images = array();
		foreach ($files as $file)
		{
			if(kString::endsWith($file, self::JPG_SUFFIX))
			{
				$images[] = $this->srcPath . $file;
			}
		}
		array_shift($images);

		$imagesLen = pack('V', sizeof($images));
		$intervalInMs = pack('V', 1000 * $interval);

		fwrite($targetFile, $magicNumber);
		fwrite($targetFile, $version);
		fwrite($targetFile, $imagesLen);
		fwrite($targetFile, $intervalInMs);
		for ($i = 20; $i < 64; $i++)
		{
			fwrite($targetFile, pack('c*', 0x00));
		}

		$bifTableSize = 8 + (8 * sizeof($images));
		$imageIndex = 64 + $bifTableSize;
		$timestamp = 0;

		foreach ($images as $image)
		{
			$currentFileSize = filesize($image);
			fwrite($targetFile, pack('V', $timestamp));
			fwrite($targetFile, pack('V', $imageIndex));

			$timestamp+=1;
			$imageIndex+=$currentFileSize;
		}

		fwrite($targetFile, pack('V', 0xffffffff));
		fwrite($targetFile, pack('V', $imageIndex));

		foreach ($images as $image)
		{
			$imageFile = fopen($image, "rb");
			$content = fread($imageFile, filesize($image));
			fwrite($targetFile, $content);
		}

		@fclose($targetFile);
	}
}