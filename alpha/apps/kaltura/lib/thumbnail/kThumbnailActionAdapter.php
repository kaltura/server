<?php
/**
 * @package core
 * @subpackage thumbnail
 */

class kThumbnailActionAdapter
{
	const UNSET_PARAMETER_ZERO_BASED = 0;
	const UNSET_PARAMETER = -1;
	const COLOR_FORMAT = '%06x';

	protected $entry;
	protected $version;
	protected $width;
	protected $height;
	protected $type;
	protected $bgColor;
	protected $cropWidth;
	protected $cropHeight;
	protected $cropX;
	protected $cropY;
	protected $vidSec;
	protected $vidSlice;
	protected $vidSlices;
	protected $startSec;
	protected $endSec;
	protected $stripProfiles;
	protected $quality;
	protected $imageFormat;
	protected $density;
	protected $srcWidth;
	protected $srcHeight;

	/**
	 * kThumbnailActionAdapter constructor.
	 * @param entry $entry
	 * @param int $width
	 * @param int $height
	 * @param int $type
	 * @param string $bgColor
	 * @param int $cropWidth
	 * @param int $cropHeight
	 * @param int $cropX
	 * @param int $cropY
	 * @param float $vid_sec
	 * @param int $vid_slice
	 * @param int $vid_slices
	 * @param float $start_sec
	 * @param float $end_sec
	 * @param bool $stripProfiles
	 * @param float $quality
	 * @param string $imageFormat
	 * @param float $density
	 */
	public function __construct($entry, $width, $height, $type, $bgColor, $cropWidth, $cropHeight, $cropX,
								$cropY, $vid_sec, $vid_slice, $vid_slices, $start_sec, $end_sec, $stripProfiles,
								$quality, $imageFormat, $density)
	{
		$this->entry = $entry;
		$this->width = $width;
		$this->height = $height;
		$this->type = $type;
		$this->bgColor = $bgColor;
		$this->cropWidth = $cropWidth;
		$this->cropHeight = $cropHeight;
		$this->cropX = $cropX;
		$this->cropY = $cropY;
		$this->vidSec = $vid_sec;
		$this->vidSlice = $vid_slice;
		$this->vidSlices = $vid_slices;
		$this->startSec = $start_sec;
		$this->endSec = $end_sec;
		$this->stripProfiles = $stripProfiles;
		$this->quality = $quality;
		$this->imageFormat = $imageFormat;
		$this->density = $density;
	}

	/**
	 * @return kImageTransformation
	 */
	public function getImageTransformation()
	{
		$step = new kImageTransformationStep();
		$this->createEntrySource($step);
		$this->handleSourceActions($step);
		$this->prepareInput();
		switch($this->type)
		{
			case kThumbnailActionType::RESIZE:
				$this->handleResize(true, $step);
				break;
			case kThumbnailActionType::RESIZE_WITH_PADDING:
				if($this->width && $this->height)
				{
					$this->handleResizeWithPadding($step);
				}
				else
				{
					$this->handleResize( false, $step);
				}

				break;
			case kThumbnailActionType::CROP:
				$this->handleCrop(Imagick::GRAVITY_CENTER, $step);
				break;
			case kThumbnailActionType::CROP_FROM_TOP:
				$this->handleCrop(Imagick::GRAVITY_NORTH, $step);
				break;
			case kThumbnailActionType::RESIZE_WITH_FORCE:
				$this->handleResize(false, $step);
				break;
			case kThumbnailActionType::CROP_AFTER_RESIZE:
				$this->handleCropAfterResize($step);
				break;
		}

		$this->handleStrip($step);
		$this->handleImageOutputAction($step);
		$transformation = new kImageTransformation();
		$transformation->addImageTransformationStep($step);
		return $transformation;
	}

	protected function handleCropAfterResize($step)
	{
		$resizeWidth = $this->width ? $this->width : $this->height;
		$resizeHeight = $this->height ? $this->height : $this->width;
		$gravityPoint = $this->getGravityByXY();
		$resizeAction = new kResizeAction();
		$resizeAction->setActionParameter(kThumbnailParameterName::WIDTH, $resizeWidth);
		$resizeAction->setActionParameter(kThumbnailParameterName::HEIGHT, $resizeHeight);
		$resizeAction->setActionParameter(kThumbnailParameterName::BEST_FIT, true);
		$step->addAction($resizeAction);
		$cropAction = new kCropAction();
		$cropAction->setActionParameter(kThumbnailParameterName::GRAVITY_POINT, $gravityPoint);
		$cropAction->setActionParameter(kThumbnailParameterName::WIDTH, $this->cropWidth);
		$cropAction->setActionParameter(kThumbnailParameterName::HEIGHT, $this->cropHeight);
		$step->addAction($cropAction);
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
	 * @return int
	 */
	public function getGravityByXY()
	{
		$gravity = ($this->cropY < 0) ? 'North' : (($this->cropY > 0) ? 'South' : ''); // Start with North/South
		$gravity .= ($this->cropX < 0) ? 'West' : (($this->cropX > 0) ? 'East' : ''); // Add/Set East/West as needed

		switch($gravity)
		{
			case 'NorthWest':
				$result = IMagick::GRAVITY_NORTHWEST;
				break;
			case 'North':
				$result = IMagick::GRAVITY_NORTH;
				break;
			case 'NorthEast':
				$result = IMagick::GRAVITY_NORTHEAST;
				break;
			case 'West':
				$result = IMagick::GRAVITY_WEST;
				break;
			case 'East':
				$result = IMagick::GRAVITY_EAST;
				break;
			case 'SouthWest':
				$result = IMagick::GRAVITY_SOUTHWEST;
				break;
			case 'South':
				$result = IMagick::GRAVITY_SOUTH;
				break;
			case 'SouthEast':
				$result = IMagick::GRAVITY_SOUTHEAST;
				break;
			default:
				$result = IMagick::GRAVITY_CENTER;
		}


		return $result;
	}

	/**
	 * @param int $gravityPoint
	 * @param kImageTransformationStep $step
	 */
	protected function handleCrop($gravityPoint, $step)
	{
		$this->calculateResizeAndCropDimensions($gravityPoint, $resizeWidth, $resizeHeight);
		$cropAction = new kCropAction();
		$cropAction->setActionParameter(kThumbnailParameterName::GRAVITY_POINT, $gravityPoint);
		$cropAction->setActionParameter(kThumbnailParameterName::WIDTH, $this->cropWidth);
		$cropAction->setActionParameter(kThumbnailParameterName::HEIGHT, $this->cropHeight);
		$step->addAction($cropAction);
		$resizeAction = new kResizeAction();
		$resizeAction->setActionParameter(kThumbnailParameterName::WIDTH, $resizeWidth);
		$resizeAction->setActionParameter(kThumbnailParameterName::HEIGHT, $resizeHeight);
		$resizeAction->setActionParameter(kThumbnailParameterName::BEST_FIT, true);
		$step->addAction($resizeAction);
	}

	protected function calculateResizeAndCropDimensions(&$gravityPoint, &$resizeWidth, &$resizeHeight)
	{
		$this->initResizeAndCropCalculationVariables($gravityPoint, $resizeWidth, $resizeHeight, $cropHeight, $cropWidth);

		if ($resizeWidth < $resizeHeight)
		{
			$ratio = round($resizeHeight / $resizeWidth, 3);
			if ($this->srcHeight / $ratio <= $cropWidth)
			{
				$cropWidth = $this->srcHeight / $ratio;
			}
			else
			{
				$cropHeight = $this->srcWidth / $ratio;
			}
		}
		elseif ($resizeHeight < $resizeWidth)
		{
			$ratio = round($resizeWidth / $resizeHeight, 3);
			//in case vertical image - height reduces by w/h ratio
			if ($this->srcHeight * $ratio <= $cropWidth)
			{
				$cropWidth = $this->srcHeight * $ratio;
			}
			else
			{
				$cropHeight = $this->srcWidth / $ratio;
			}
		}
		else
		{
			$cropHeight = $cropWidth = ($cropHeight < $cropWidth ? $cropHeight : $cropWidth);
		}

		$this->cropHeight = round($cropHeight);
		$this->cropWidth = round($cropWidth);
	}

	protected function prepareInput()
	{
		$this->bgColor = sprintf(self::COLOR_FORMAT, $this->bgColor);
		if (!$this->cropWidth)
		{
			$this->srcWidth = $this->entry->getWidth();
		}

		if (!$this->cropHeight)
		{
			$this->srcHeight = $this->entry->getHeight();
		}
	}

	/**
	 * @param kImageTransformationStep $step
	 */
	protected function handleResizeWithPadding($step)
	{
		$this->calculatePadding($borderHeight, $borderWidth);
		$reSizeAction = new kResizeAction();
		$reSizeAction->setActionParameter(kThumbnailParameterName::WIDTH, $this->width);
		$reSizeAction->setActionParameter(kThumbnailParameterName::HEIGHT, $this->height);
		$step->addAction($reSizeAction);
		$borderAction = new kBorderImageAction();
		$borderAction->setActionParameter(kThumbnailParameterName::BACKGROUND_COLOR, $this->bgColor);
		$borderAction->setActionParameter(kThumbnailParameterName::WIDTH, $borderWidth);
		$borderAction->setActionParameter(kThumbnailParameterName::HEIGHT, $borderHeight);
		$step->addAction($borderAction);
	}

	protected function calculatePadding(&$borderHeight, &$borderWidth)
	{
		$borderWidth = 0;
		$borderHeight = 0;

		if($this->width * $this->cropHeight < $this->height * $this->cropWidth)
		{
			$w = $this->width;
			$h = ceil($this->cropHeight * ($this->width / $this->cropWidth));
			$borderHeight = ceil(($this->height - $h) / 2);
			if ($borderHeight * 2 + $h > $this->height)
			{
				$h--;
			}
		}
		else
		{
			$h = $this->height;
			$w = ceil($this->cropWidth * ($this->height / $this->cropHeight));
			$borderWidth = ceil(($this->width - $w) / 2);
			if ($borderWidth * 2 + $w > $this->width)
			{
				$w--;
			}
		}

		$this->width = $w;
		$this->height = $h;
	}

	/**
	 * @param kImageTransformationStep $step
	 */
	protected function handleStrip($step)
	{
		if($this->stripProfiles)
		{
			$stripAction = new kStripImageAction();
			$step->addAction($stripAction);
		}
	}

	/**
	 * @param bool $bestFit
	 * @param kImageTransformationStep $step
	 */
	protected function handleResize($bestFit, $step)
	{
		$action = new kResizeAction();
		$action->setActionParameter(kThumbnailParameterName::WIDTH, $this->width);
		$action->setActionParameter(kThumbnailParameterName::HEIGHT, $this->height);
		$action->setActionParameter(kThumbnailParameterName::BEST_FIT, $bestFit);
		$step->addAction($action);
	}

	/**
	 * @param kImageTransformationStep $step
	 */
	protected function handleImageOutputAction($step)
	{
		$outputAction = new kImageOutputAction();
		if($this->quality != self::UNSET_PARAMETER_ZERO_BASED)
		{
			$outputAction->setActionParameter(kThumbnailParameterName::QUALITY, $this->quality);
		}

		if($this->density != self::UNSET_PARAMETER_ZERO_BASED)
		{
			$outputAction->setActionParameter(kThumbnailParameterName::DENSITY, $this->density);
		}

		if($this->imageFormat)
		{
			$outputAction->setActionParameter(kThumbnailParameterName::IMAGE_FORMAT, $this->imageFormat);
		}

		$step->addAction($outputAction);
	}

	/**
	 * @param kImageTransformationStep $step
	 */
	protected function handleSourceActions($step)
	{
		if($this->vidSec != self::UNSET_PARAMETER)
		{
			$sourceAction = new kVidSecAction();
			$sourceAction->setActionParameter(kThumbnailParameterName::SECOND, $this->vidSec);
			$step->addAction($sourceAction);
		}
		else if($this->vidSlices != self::UNSET_PARAMETER)
		{
			if($this->vidSlice != self::UNSET_PARAMETER)
			{
				$sourceAction = new kVidSliceAction();
				$sourceAction->setActionParameter(kThumbnailParameterName::SLICE_NUMBER, $this->vidSlice);
				$sourceAction->setActionParameter(kThumbnailParameterName::NUMBER_OF_SLICES, $this->vidSlices);
				$sourceAction->setActionParameter(kThumbnailParameterName::START_SEC, $this->startSec);
				$sourceAction->setActionParameter(kThumbnailParameterName::END_SEC, $this->endSec);
				$step->addAction($sourceAction);
			}
			else
			{
				$sourceAction = new kVidStripAction();
				$sourceAction->setActionParameter(kThumbnailParameterName::NUMBER_OF_SLICES, $this->vidSlices);
				$sourceAction->setActionParameter(kThumbnailParameterName::START_SEC, $this->startSec);
				$sourceAction->setActionParameter(kThumbnailParameterName::END_SEC, $this->endSec);
				$step->addAction($sourceAction);
			}
		}
	}

	/**
	 * @param kImageTransformationStep $step
	 */
	protected function createEntrySource($step)
	{
		$source = new kEntrySource();
		$source->setEntry($this->entry);
		$step->setSource($source);
	}

	/**
	 * @param $gravityPoint
	 * @param $resizeWidth
	 * @param $resizeHeight
	 * @param $cropHeight
	 * @param $cropWidth
	 */
	protected function initResizeAndCropCalculationVariables(&$gravityPoint, &$resizeWidth, &$resizeHeight, &$cropHeight, &$cropWidth)
	{
		$resizeWidth = $this->width ? $this->width : $this->height;
		$resizeHeight = $this->height ? $this->height : $this->width;
		if ($this->cropHeight)
		{
			$cropHeight = $this->cropHeight;
			$this->srcHeight = $cropHeight;
			$gravityPoint = imagick::GRAVITY_NORTH;
		}
		else
		{
			$cropHeight = $this->srcHeight;
		}

		if ($this->cropWidth)
		{
			$cropWidth = $this->cropWidth;
			$this->srcWidth = $cropWidth;
			$gravityPoint = imagick::GRAVITY_WEST;
		}
		else
		{
			$cropWidth = $this->srcWidth;
		}
	}
}