<?php
/**
 * @package server-infra
 * @subpackage Media
 */
class KImageMagickCropper extends KBaseCropper
{
	const RESIZE = 1;
	const RESIZE_WITH_PADDING = 2;
	const CROP = 3;
	const CROP_FROM_TOP = 4;
	const RESIZE_WITH_FORCE = 5;

	/**
	 * Crop the image after resizing (as opposed to the other types, which crop first and resize afterwards)
	 * This crop type makes use of the following vars from getCommand():
	 * @param $width|$height The target resize dimensions
	 * @param $cropWidth|$cropHeight The crop dimensions after resize was applied
	 * @param $cropX|$cropY Gravity indicators (see getGravityByXY() for usage)
	 * 
	 */
	const CROP_AFTER_RESIZE = 6;

	const DEFAULT_BGCOLOR = '0xffffff';


	
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
	public function __construct($srcPath, $targetPath, $cmdPath = null, $forceJpeg = false)
	{
		$this->cmdPath = $cmdPath;
		
		list($this->srcWidth, $this->srcHeight, $type, $attr) = getimagesize($srcPath);
		
		parent::__construct($srcPath, $targetPath);
	}
	
	protected function getCommand($quality, $cropType, $width = 0, $height = 0, $cropX = 0, $cropY = 0, $cropWidth = 0, $cropHeight = 0, $scaleWidth = 1, $scaleHeight = 1, $bgcolor = self::DEFAULT_BGCOLOR , $density = 0, $forceRotation = null, $strip = false)
	{

		$attributes = array();

		$exifData = @exif_read_data($this->srcPath);
		$orientation = isset($exifData["Orientation"]) ? $exifData["Orientation"] : 1;
		
		if(isset($forceRotation)) {
			switch($forceRotation){
			case 0:  // do noting
				break;
			case 90:
				$orientation = 6;
				break;
			case 180:
				$orientation = 4;
				break;
			case 270:
				$orientation = 8;
				break;
			}
		}
		
		if($strip)
			$attributes[] = "-strip";
		
		if($density != 0) {
			$attributes[] = "-density ".$density;
			$attributes[] = "-units PixelsPerInch ";
		}
				
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
				$attributes[] = "-flop";
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
		
		//reseting orientation on the image EXIF.
		$attributes[] = "-orient undefined";

		if($quality)
			$attributes[] = "-quality $quality";

		if($scaleWidth || $scaleHeight)
		{
			if(!$scaleWidth)
				$scaleWidth = 1;
			if(!$scaleHeight)
				$scaleHeight = 1;
				
			$cropX *= $scaleWidth;
			$cropY *= $scaleHeight;	
			$cropWidth *= $scaleWidth;
			$cropHeight *= $scaleHeight;
			
			$scaleWidth *= 100;
			$scaleHeight *= 100;
				
			$scale = "{$scaleWidth}%x{$scaleHeight}%";
			
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
			$attributes[] = "+repage"; 
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
												
						if($width * $this->srcHeight < $height * $this->srcWidth)
						{
							$w = $width;
							$h = ceil($this->srcHeight * ($width / $this->srcWidth));
							$borderHeight = ceil(($height - $h) / 2);
							if ($borderHeight * 2 + $h > $height)
							{
								$h--;
							}
						}
						else 
						{
							$h = $height;
							$w = ceil($this->srcWidth * ($height / $this->srcHeight));
							$borderWidth = ceil(($width - $w) / 2);
							if ($borderWidth * 2 + $w > $width)
							{
								$w--;
							}
						}
						
						$bgcolor = sprintf('%06x', $bgcolor);
						$attributes[] = "-bordercolor \"#{$bgcolor}\"";
						$attributes[] = "-resize {$w}x{$h}";
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
						//in case vertical image - height reduces by w/h ratio
						if ($orientation == 6 || $orientation == 8)
						{
							$resizeHeight = $this->srcHeight / $ratio;
						}
						else
						{
							if ($this->srcHeight * $ratio <= $resizeWidth)
								$resizeWidth = $this->srcHeight * $ratio;
							else
								$resizeHeight = $this->srcWidth / $ratio;
						}
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

				case self::CROP_AFTER_RESIZE:
					$w = $width ? $width : $height;
					$h = $height ? $height : $width;
					$gravity = self::getGravityByXY( $cropX, $cropY );

					$attributes[] = "-gravity $gravity";
					$attributes[] = "-resize {$w}x{$h}!";	// Resize first
															// Note the "!" addition, which will force resizing to required dimensions.
															// This will solve a case where, for example, resizing 331x197 to 350x208 will result as 349x208.
					$attributes[] = "-crop {$cropWidth}x{$cropHeight}+0+0"; // Then crop
					break;

				case self::RESIZE_WITH_FORCE:
				    $w = $width ? $width : '';
					$h = $height ? $height : '';
					
					$resize = "-resize {$w}x{$h}";
					if(strlen($w) && strlen($h))
						$resize .= '!';
						
					$attributes[] = $resize;
				    break;
			}
		}

		if ($bgcolor != self::DEFAULT_BGCOLOR)
		{
			$bgcolor = sprintf('%06x', $bgcolor);
			KalturaLog::debug('bgcolor is ' + $bgcolor);
			$attributes[] = "-fill \"#{$bgcolor}\" -opaque none";
		}
		if(!count($attributes))
			return null;



		$options = implode(' ', $attributes);

		$targetFileExtension = pathinfo($this->targetPath, PATHINFO_EXTENSION);
		if ($targetFileExtension === 'gif')
		{
			$tmpTarget = $this->targetPath.'.tmp.gif';
			$coalesceCmd = "\"$this->cmdPath\" \"$this->srcPath\" -coalesce \"$tmpTarget\"";
			$mainCmd = "\"$this->cmdPath\" \"$tmpTarget\" $options \"$this->targetPath\"";
			$rmCmd = "rm \"$tmpTarget\"";

			return "$coalesceCmd && $mainCmd && $rmCmd";
		}
		else
		{
			return "\"$this->cmdPath\" \"$this->srcPath\" $options \"$this->targetPath\"  2>&1";
		}
	}

	protected function parseAttribute($attribStr)
	{
		$out = array();
		if (preg_match('/\-([\w]*)\s?(.*)/', $attribStr, $out))
			return array($out[1], $out[2]);
		else
			return array(null, null);
	}

	/**
	 * Get a gravity value based on X/Y values
	 * <pre>
	 * >              (x, y)                               Result Gravity
	 * +----------+-----------+-----------+       +-----------+--------+-----------+
	 * | (-1, -1) |  (0, -1)  |  (1, -1)  |       | NorthWest | North  | NorthEast |
	 * +----------+-----------+-----------+       +-----------+--------+-----------+
	 * | (-1, 0)  |  (0, 0)   |  (1, 0)   |  ==>  |    West   | Center |   East    |
	 * +----------+-----------+-----------+       +-----------+--------+-----------+
	 * | (-1, 1)  |  (0, 1)   |  (1, 1)   |       | SouthWest | South  | SouthEast |
	 * +----------+-----------+-----------+       +-----------+--------+-----------+
	 * </pre>
	 * @param number $x < 0 = West, 0 = Center, > 0 = East
	 * @param number $y < 0 = North, 0 = Center, > 0 = South
	 * @return Gravity string (e.g. center)
	 */
	public static function getGravityByXY( $x, $y )
	{
		$gravity = ($y < 0) ? "North" : (($y > 0) ? "South" : ""); // Start with North/South
		$gravity .= ($x < 0) ? "West" : (($x > 0) ? "East" : ""); // Add/Set East/West as needed
		
		if ( ! $gravity ) // None of the above apply?
		{
			$gravity = "Center";
		}
			
		return $gravity;
	}
}
