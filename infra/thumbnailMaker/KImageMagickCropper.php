<?php
class KImageMagickCropper extends KBaseCropper
{
	const RESIZE = 1;
	const RESIZE_WITH_PADDING = 2;
	const CROP = 3;
	const CROP_FROM_TOP = 4;
	
	protected $cmdPath;
	protected $srcWidth;
	protected $srcHeight;

	protected static $imageExtByType = array(
		IMAGETYPE_GIF => 'gif',
		IMAGETYPE_PNG => 'png',
		IMAGETYPE_BMP => 'png',
		IMAGETYPE_JPEG => 'jpg',
	);
	
	/**
	 * @param string $filePath
	 * @param string $cmdPath
	 */
	public function __construct($srcPath, $targetPath, $cmdPath = 'convert', $forceJpeg = false)
	{
		$this->cmdPath = $cmdPath;
		
		list($this->srcWidth, $this->srcHeight, $type, $attr) = getimagesize($srcPath);

		// forceJpeg var is not used.
		// there is no return of the new target file (as string) after the extension change
//		if ($type == IMAGETYPE_BMP) // convert bmp to jpeg
//			$type = IMAGETYPE_JPEG;
//		
//		$ext = '';
//		if ($this->forceJpeg)
//			$ext = 'jpg';
//		elseif(isset(self::$imageExtByType[$type]))
//			$ext = self::$imageExtByType[$type];
//			
//		$targetPath = kFile::replaceExt($targetPath, $ext);
			
		parent::__construct($srcPath, $targetPath);
	}
	
	protected function getCommand($quality, $cropType, $width = 0, $height = 0, $cropX = 0, $cropY = 0, $cropWidth = 0, $cropHeight = 0, $scaleWidth = 0, $scaleHeight = 0, $bgcolor = 0xffffff)
	{
		$attributes = array();

		$exifData = @exif_read_data($this->srcPath);
		$orientation = isset($exifData["Orientation"]) ? $exifData["Orientation"] : 1;
		
		switch($orientation)
		{
			case 1: // nothing
			break;
		
			case 2: // horizontal flip
				$attributes[] = "-flop";
			break;
									
			case 3: // 180 rotate left
				$attributes[] = "-rotate 180";
			break;
						
			case 4: // vertical flip
				$attributes[] = "-flip";
			break;
					
			case 5: // vertical flip + 90 rotate right
				$attributes[] = "-transpose";
			break;
					
			case 6: // 90 rotate right
				$attributes[] = "-rotate 90";
			break;
					
			case 7: // horizontal flip + 90 rotate right
				$attributes[] = "-transverse";
			break;
					
			case 8:    // 90 rotate left
				$attributes[] = "-rotate 270";
			break;
		}

		if($quality)
			$attributes[] = "-quality $quality";

		if($scaleWidth || $scaleHeight)
		{
			$scale = ($scaleWidth ? $scaleWidth * 100 . '%' : '');
			$scale .= 'x' . ($scaleHeight ? $scaleHeight * 100 . '%' : '');
			
			$attributes[] = "-scale $scale";
		}
		
		// pre-crop (only in case no additional crop is needed)
		if ((intval($cropType) == self::RESIZE || intval($cropType) == self::RESIZE_WITH_PADDING) &&
			($cropX || $cropY || $cropWidth || $cropHeight))
		{
			if($cropType == self::CROP_FROM_TOP)
				$cropY = 0;
				
			$geometrics = "{$cropWidth}x{$cropHeight}";
			$geometrics .= ($cropX < 0 ? $cropX : "+$cropX");
			$geometrics .= ($cropY < 0 ? $cropY : "+$cropY");
			
			$attributes[] = "-crop $geometrics";
		}
				
		// crop or resize
		if($width || $height)
		{
			switch($cropType)
			{
				case self::RESIZE:
					$w = $width ? $width : '';
					$h = $height ? $height : '';
					$attributes[] = "-resize {$w}x{$h}";
					break;
					
				case self::RESIZE_WITH_PADDING:
					if($width && $height)
					{
						$borderWidth = 0;
						$borderHeight = 0;
						
						if ($cropHeight)
							$this->srcHeight = $cropHeight;
						if ($cropWidth)
							$this->srcWidth = $cropWidth;
						
						if($width < $height)
						{
							$w = $width;
							$h = ceil($this->srcHeight * ($width / $this->srcWidth));
							$borderHeight = ceil(($height - $h) / 2);
						}
						else 
						{
							$h = $height;
							$w = ceil($this->srcWidth * ($height / $this->srcHeight));
							$borderWidth = ceil(($width - $w) / 2);
						}
						
						$bgcolor = dechex($bgcolor);
						$attributes[] = "-bordercolor \"#{$bgcolor}\"";
						$attributes[] = "-resize {$w}x{$h}";
						$borderWidth = ($cropX ? $cropX : $cropWidth);
						$borderHeight = ($cropY ? $cropY : $cropHeight);
						$attributes[] = "-border {$borderWidth}x{$borderHeight} -gravity Center";
					}
					else 
					{
						$w = $width ? $width : '';
						$h = $height ? $height : '';
						$attributes[] = "-resize {$w}x{$h}";
					}
					break;
					
				case self::CROP:
				case self::CROP_FROM_TOP:
					$w = $width ? $width : $height;
					$h = $height ? $height : $width;
					$gravity = null;
					if ($cropHeight)
					{
						$this->srcHeight = $cropHeight;
						$gravity = "-gravity North";
					}
					if ($cropWidth)
					{
						$this->srcWidth = $cropWidth;
						$gravity = "-gravity West";
					}
					
					$resizeWidth = $this->srcWidth;
					$resizeHeight = $this->srcHeight;
					$ratio = 0;
					if ($w < $h) 
					{
						$ratio = round($h / $w, 3);
						if ($this->srcHeight / $ratio <= $resizeWidth)
							$resizeWidth = $this->srcHeight / $ratio;
						else
							$resizeHeight = $this->srcWidth / $ratio;
					}
					elseif ($h < $w)
					{
						$ratio = round($w / $h, 3);
						if ($this->srcHeight * $ratio <= $resizeWidth)
							$resizeWidth = $this->srcHeight * $ratio;
						else
							$resizeHeight = $this->srcWidth / $ratio;
					}
					else
					{
						$resizeHeight = $resizeWidth = ($resizeHeight < $resizeWidth ? $resizeHeight : $resizeWidth);
					}
					$resizeHeight = round($resizeHeight);
					$resizeWidth = round($resizeWidth);
					if($cropType == self::CROP && !$gravity)
						$attributes[] = "-gravity Center";
					elseif($cropType == self::CROP_FROM_TOP && !$gravity)
						$attributes[] = "-gravity North";
						
					$attributes[] = $gravity;	
					$attributes[] = "-crop {$resizeWidth}x{$resizeHeight}+0+0";
					$attributes[] = "-resize {$w}x{$h}";
					break;
			}
		}

		if(!count($attributes))
			return null;
			
		$options = implode(' ', $attributes);
		return "\"$this->cmdPath\" \"$this->srcPath\" $options \"$this->targetPath\"";
	}
}
