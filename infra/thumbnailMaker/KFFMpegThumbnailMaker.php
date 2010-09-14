<?php
class KFFMpegThumbnailMaker extends KBaseThumbnailMaker
{
	protected $cmdPath;
	
	/**
	 * @param string $filePath
	 * @param string $cmdPath
	 */
	public function __construct($srcPath, $targetPath, $cmdPath = 'ffmpeg')
	{
		$this->cmdPath = $cmdPath;
		parent::__construct($srcPath, $targetPath);
	}
	
	protected function getCommand($position = null, $width = null, $height = null, $frameCount = 1, $targetType = "image2")
	{
		$dimensions = (is_null($width) || is_null($height)) ? '' : ("-s ". $width ."x" . $height);
		$position_str = $position ? " -ss $position " : '';
		return "$this->cmdPath -i $this->srcPath -an -y -r 1 $dimensions $position_str -vframes $frameCount -f $targetType $this->targetPath >> $this->targetPath.log 2>&1";
	}
	
	protected function parseOutput($output)
	{
		throw new Exception("Not implemented yet");
	}
}