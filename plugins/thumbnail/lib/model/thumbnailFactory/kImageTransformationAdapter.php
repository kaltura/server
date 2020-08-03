<?php
/**
 * @package plugins.thumbnail
 * @subpackage model
 */

class kImageTransformationAdapter
{
	const COLOR_FORMAT = '%06x';
	const HEX_PREFIX = '0x';
	const MILLISECONDS_IN_SECOND = 1000;

	/**
	 * @var kThumbAdapterParameters
	 */
	protected $parameters;
	protected $fileSource = false;

	/**
	 * @param kThumbAdapterParameters $parameters
	 * @return kImageTransformation
	 * @throws ImagickException
	 */
	public function getImageTransformation($parameters)
	{
		$this->parameters = $parameters;
		$this->prepareInput();
		if($this->parameters->get(kThumbFactoryFieldName::VID_SLICES) !== kThumbAdapterParameters::UNSET_PARAMETER && $this->parameters->get(kThumbFactoryFieldName::VID_SLICE) === kThumbAdapterParameters::UNSET_PARAMETER)
		{
			return $this->getStripTransformation();
		}

		$step = new kImageTransformationStep();
		$this->addSource($step);
		$this->handleSourceActions($step);
		$this->handleActionType($step);
		$this->handleStripProfile($step);
		$this->handleImageOutputAction($step);
		$transformation = new kImageTransformation();
		$transformation->addImageTransformationStep($step);
		return $transformation;
	}

	protected function getStripTransformation()
	{
		$transformation = new kImageTransformation();
		$vidSlices = $this->parameters->get(kThumbFactoryFieldName::VID_SLICES);
		$startSec = $this->parameters->get(kThumbFactoryFieldName::START_SEC);
		$interval = $this->getStripInterval();
		$startSec = max($startSec, 0);
		$step = $this->getFirstStripTransformationStep($startSec, $vidSlices);
		$transformation->addImageTransformationStep($step);
		for($i = 1; $i <= $vidSlices; $i++)
		{
			$step = new kImageTransformationStep();
			$this->addEntrySource($step);
			$this->addVidSecAction($step, $startSec + ($i * $interval));
			$this->handleActionType($step);
			$this->addStripConcat($step, $i);
			$transformation->addImageTransformationStep($step);
		}

		$this->handleStripProfile($step);
		$this->handleImageOutputAction($step);
		return $transformation;
	}

	protected function getStripInterval()
	{
		$entry = $this->parameters->get(kThumbFactoryFieldName::ENTRY);
		$videoLength = $entry->getLengthInMsecs();
		$startSec = $this->parameters->get(kThumbFactoryFieldName::START_SEC);
		$endSec =  $this->parameters->get(kThumbFactoryFieldName::END_SEC);
		$vidSlices = $this->parameters->get(kThumbFactoryFieldName::VID_SLICES);
		if($startSec !==  kThumbAdapterParameters::UNSET_PARAMETER && $endSec !==  kThumbAdapterParameters::UNSET_PARAMETER)
		{
			$interVal = ($endSec - $startSec) / $vidSlices;
		}
		else
		{
			$interVal = $videoLength / self::MILLISECONDS_IN_SECOND / $vidSlices;
		}

		return $interVal;
	}

	/**
	 * @param kImageTransformationStep $step
	 * @param int $slideNum
	 */
	protected function addStripConcat($step, $slideNum)
	{
		$concatAction = new kCompositeAction();
		$concatAction->setActionParameter(kThumbnailParameterName::X, $slideNum * $this->parameters->get(kThumbFactoryFieldName::WIDTH));
		$step->addAction($concatAction);
	}

	protected function handleActionType($step)
	{
		switch($this->parameters->get(kThumbFactoryFieldName::TYPE))
		{
			case kExtwidgetThumbnailActionType::RESIZE:
				$this->AddResizeAction($step, $this->parameters->get(kThumbFactoryFieldName::WIDTH), $this->parameters->get(kThumbFactoryFieldName::HEIGHT), true);
				break;
			case kExtwidgetThumbnailActionType::RESIZE_WITH_FORCE:
				$this->AddResizeAction($step, $this->parameters->get(kThumbFactoryFieldName::WIDTH), $this->parameters->get(kThumbFactoryFieldName::HEIGHT));
				break;
			case kExtwidgetThumbnailActionType::RESIZE_WITH_PADDING:
				if($this->parameters->get(kThumbFactoryFieldName::WIDTH) && $this->parameters->get(kThumbFactoryFieldName::HEIGHT))
				{
					$this->handleResizeWithPadding($step);
				}
				else
				{
					$this->AddResizeAction($step, $this->parameters->get(kThumbFactoryFieldName::WIDTH), $this->parameters->get(kThumbFactoryFieldName::HEIGHT));
				}

				break;
			case kExtwidgetThumbnailActionType::CROP:
				$this->handleCrop(Imagick::GRAVITY_CENTER, $step);
				break;
			case kExtwidgetThumbnailActionType::CROP_FROM_TOP:
				$this->handleCrop(Imagick::GRAVITY_NORTH, $step);
				break;
			case kExtwidgetThumbnailActionType::CROP_AFTER_RESIZE:
				$this->handleCropAfterResize($step);
				break;
		}
	}

	protected function handleCropAfterResize($step)
	{
		$resizeWidth = $this->parameters->get(kThumbFactoryFieldName::WIDTH) ? $this->parameters->get(kThumbFactoryFieldName::WIDTH) : $this->parameters->get(kThumbFactoryFieldName::HEIGHT);
		$resizeHeight = $this->parameters->get(kThumbFactoryFieldName::HEIGHT) ? $this->parameters->get(kThumbFactoryFieldName::HEIGHT) : $this->parameters->get(kThumbFactoryFieldName::WIDTH);
		$gravityPoint = $this->getGravityByXY();
		$this->AddResizeAction($step, $resizeWidth, $resizeHeight, true);
		$cropAction = new kCropAction();
		$cropAction->setActionParameter(kThumbnailParameterName::GRAVITY_POINT, $gravityPoint);
		$cropAction->setActionParameter(kThumbnailParameterName::WIDTH, $this->parameters->get(kThumbFactoryFieldName::CROP_WIDTH));
		$cropAction->setActionParameter(kThumbnailParameterName::HEIGHT, $this->parameters->get(kThumbFactoryFieldName::CROP_HEIGHT));
		$step->addAction($cropAction);
	}

	/**
	 * Get a gravity value based on X/Y values
	 *                (x, y)                               Result Gravity
	 * +----------+-----------+-----------+       +-----------+--------+-----------+
	 * | (-1, -1) |  (0, -1)  |  (1, -1)  |       | NorthWest | North  | NorthEast |
	 * +----------+-----------+-----------+       +-----------+--------+-----------+
	 * | (-1, 0)  |  (0, 0)   |  (1, 0)   |  ==>  |    West   | Center |   East    |
	 * +----------+-----------+-----------+       +-----------+--------+-----------+
	 * | (-1, 1)  |  (0, 1)   |  (1, 1)   |       | SouthWest | South  | SouthEast |
	 * +----------+-----------+-----------+       +-----------+--------+-----------+
	 * @return int
	 */
	protected function getGravityByXY()
	{
		$gravity = ($this->parameters->get(kThumbFactoryFieldName::CROP_Y) < 0) ? 'North' : (($this->parameters->get(kThumbFactoryFieldName::CROP_Y) > 0) ? 'South' : ''); // Start with North/South
		$gravity .= ($this->parameters->get(kThumbFactoryFieldName::CROP_X) < 0) ? 'West' : (($this->parameters->get(kThumbFactoryFieldName::CROP_X) > 0) ? 'East' : ''); // Add/Set East/West as needed

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
	 * @throws kThumbnailException
	 */
	protected function handleCrop($gravityPoint, $step)
	{
		$this->calculateResizeAndCropDimensions($gravityPoint, $resizeWidth, $resizeHeight);
		$cropAction = new kCropAction();
		$cropAction->setActionParameter(kThumbnailParameterName::GRAVITY_POINT, $gravityPoint);
		$cropAction->setActionParameter(kThumbnailParameterName::WIDTH, $this->parameters->get(kThumbFactoryFieldName::CROP_WIDTH));
		$cropAction->setActionParameter(kThumbnailParameterName::HEIGHT, $this->parameters->get(kThumbFactoryFieldName::CROP_HEIGHT));
		$step->addAction($cropAction);
		$this->AddResizeAction($step, $resizeWidth, $resizeHeight, true);
	}

	/**
	 * @param $gravityPoint
	 * @param $resizeWidth
	 * @param $resizeHeight
	 * @throws kThumbnailException
	 */
	protected function calculateResizeAndCropDimensions(&$gravityPoint, &$resizeWidth, &$resizeHeight)
	{
		$cropHeight = null;
		$cropWidth = null;
		$this->initResizeAndCropCalculationVariables($gravityPoint, $resizeWidth, $resizeHeight, $cropHeight, $cropWidth);
		if ($resizeWidth < $resizeHeight)
		{
			$ratio = round($resizeHeight / $resizeWidth, 3);
			if ($this->parameters->get(kThumbFactoryFieldName::SRC_HEIGHT) / $ratio <= $cropWidth)
			{
				$cropWidth = $this->parameters->get(kThumbFactoryFieldName::SRC_HEIGHT) / $ratio;
			}
			else
			{
				$cropHeight = $this->parameters->get(kThumbFactoryFieldName::SRC_WIDTH) / $ratio;
			}
		}
		elseif ($resizeHeight < $resizeWidth)
		{
			$ratio = round($resizeWidth / $resizeHeight, 3);
			//in case vertical image - height reduces by w/h ratio
			if ($this->parameters->get(kThumbFactoryFieldName::SRC_HEIGHT) * $ratio <= $cropWidth)
			{
				$cropWidth = $this->parameters->get(kThumbFactoryFieldName::SRC_HEIGHT) * $ratio;
			}
			else
			{
				$cropHeight = $this->parameters->get(kThumbFactoryFieldName::SRC_WIDTH) / $ratio;
			}
		}
		else
		{
			$cropHeight = $cropWidth = ($cropHeight < $cropWidth ? $cropHeight : $cropWidth);
		}

		$this->parameters->set(kThumbFactoryFieldName::CROP_HEIGHT, round($cropHeight));
		$this->parameters->set(kThumbFactoryFieldName::CROP_WIDTH, round($cropWidth));
	}

	protected function prepareInput()
	{
		$bgColor = $this->parameters->get(kThumbFactoryFieldName::BG_COLOR);
		if(is_string($bgColor) && strpos($bgColor, self::HEX_PREFIX) === false)
		{
			$bgColor = hexdec(self::HEX_PREFIX . $bgColor);
		}

		$bgColor = sprintf(self::COLOR_FORMAT, $bgColor);
		$this->parameters->set(kThumbFactoryFieldName::BG_COLOR, $bgColor);
		/* @var $entry entry */
		$entry = $this->parameters->get(kThumbFactoryFieldName::ENTRY);
		if (!$this->parameters->get(kThumbFactoryFieldName::CROP_WIDTH))
		{
			$this->parameters->set(kThumbFactoryFieldName::SRC_WIDTH, $entry->getWidth());
		}

		if (!$this->parameters->get(kThumbFactoryFieldName::CROP_HEIGHT))
		{
			$this->parameters->set(kThumbFactoryFieldName::SRC_HEIGHT, $entry->getHeight());
		}
	}

	/**
	 * @param kImageTransformationStep $step
	 */
	protected function handleResizeWithPadding($step)
	{
		$this->calculatePadding($borderHeight, $borderWidth);
		$this->AddResizeAction($step, $this->parameters->get(kThumbFactoryFieldName::WIDTH), $this->parameters->get(kThumbFactoryFieldName::HEIGHT));
		$borderAction = new kBorderImageAction();
		$borderAction->setActionParameter(kThumbnailParameterName::BACKGROUND_COLOR, $this->parameters->get(kThumbFactoryFieldName::BG_COLOR));
		$borderAction->setActionParameter(kThumbnailParameterName::WIDTH, $borderWidth);
		$borderAction->setActionParameter(kThumbnailParameterName::HEIGHT, $borderHeight);
		$step->addAction($borderAction);
	}

	protected function calculatePadding(&$borderHeight, &$borderWidth)
	{
		$borderWidth = 0;
		$borderHeight = 0;

		if($this->parameters->get(kThumbFactoryFieldName::WIDTH) * $this->parameters->get(kThumbFactoryFieldName::SRC_HEIGHT) < $this->parameters->get(kThumbFactoryFieldName::HEIGHT) * $this->parameters->get(kThumbFactoryFieldName::SRC_WIDTH))
		{
			$w = $this->parameters->get(kThumbFactoryFieldName::WIDTH);
			$h = ceil($this->parameters->get(kThumbFactoryFieldName::SRC_HEIGHT) * ($this->parameters->get(kThumbFactoryFieldName::WIDTH) / $this->parameters->get(kThumbFactoryFieldName::SRC_WIDTH)));
			$borderHeight = ceil(($this->parameters->get(kThumbFactoryFieldName::HEIGHT) - $h) / 2);
			if ($borderHeight * 2 + $h > $this->parameters->get(kThumbFactoryFieldName::HEIGHT))
			{
				$h--;
			}
		}
		else
		{
			$h = $this->parameters->get(kThumbFactoryFieldName::HEIGHT);
			$w = ceil($this->parameters->get(kThumbFactoryFieldName::SRC_WIDTH) * ($this->parameters->get(kThumbFactoryFieldName::HEIGHT) / $this->parameters->get(kThumbFactoryFieldName::SRC_HEIGHT)));
			$borderWidth = ceil(($this->parameters->get(kThumbFactoryFieldName::WIDTH) - $w) / 2);
			if ($borderWidth * 2 + $w > $this->parameters->get(kThumbFactoryFieldName::WIDTH))
			{
				$w--;
			}
		}

		$this->parameters->set(kThumbFactoryFieldName::WIDTH, $w);
		$this->parameters->set(kThumbFactoryFieldName::HEIGHT, $h);
	}

	/**
	 * @param kImageTransformationStep $step
	 */
	protected function handleStripProfile($step)
	{
		if($this->parameters->get(kThumbFactoryFieldName::STRIP_PROFILES))
		{
			$stripAction = new kStripImageAction();
			$step->addAction($stripAction);
		}
	}

	/**
	 * @param kImageTransformationStep $step
	 * @param $width
	 * @param $height
	 * @param bool $bestFit
	 */
	protected function AddResizeAction($step, $width, $height, $bestFit = false)
	{
		$action = new kResizeAction();
		$bestFit = $bestFit && ($this->parameters->get(kThumbFactoryFieldName::WIDTH) > kResizeAction::BEST_FIT_MIN && $this->parameters->get(kThumbFactoryFieldName::HEIGHT) > kResizeAction::BEST_FIT_MIN);
		if($width || $height)
		{
			$action->setActionParameter(kThumbnailParameterName::WIDTH, $width);
			$action->setActionParameter(kThumbnailParameterName::HEIGHT, $height);
			$action->setActionParameter(kThumbnailParameterName::BEST_FIT, $bestFit);
			$step->addAction($action);
		}
	}

	/**
	 * @param kImageTransformationStep $step
	 */
	protected function handleImageOutputAction($step)
	{
		$outputAction = new kImageOutputAction();
		if($this->parameters->get(kThumbFactoryFieldName::QUALITY) != kThumbAdapterParameters::UNSET_PARAMETER_ZERO_BASED)
		{
			$outputAction->setActionParameter(kThumbnailParameterName::QUALITY, $this->parameters->get(kThumbFactoryFieldName::QUALITY));
		}

		if($this->parameters->get(kThumbFactoryFieldName::IMAGE_FORMAT))
		{
			$outputAction->setActionParameter(kThumbnailParameterName::IMAGE_FORMAT, $this->parameters->get(kThumbFactoryFieldName::IMAGE_FORMAT));
		}

		if($this->parameters->get(kThumbFactoryFieldName::DENSITY))
		{
			$outputAction->setActionParameter(kThumbnailParameterName::DENSITY, $this->parameters->get(kThumbFactoryFieldName::DENSITY));
		}

		$step->addAction($outputAction);
	}

	/**
	 * @param kImageTransformationStep $step
	 */
	protected function handleSourceActions($step)
	{
		if($this->fileSource)
		{
			return;
		}
		else if($this->parameters->get(kThumbFactoryFieldName::VID_SEC) !== kThumbAdapterParameters::UNSET_PARAMETER)
		{
			$this->addVidSecAction($step, $this->parameters->get(kThumbFactoryFieldName::VID_SEC));
		}
		else if($this->parameters->get(kThumbFactoryFieldName::VID_SLICE) !== kThumbAdapterParameters::UNSET_PARAMETER)
		{
			$sourceAction = new kVidSliceAction();
			$sourceAction->setActionParameter(kThumbnailParameterName::SLICE_NUMBER, $this->parameters->get(kThumbFactoryFieldName::VID_SLICE));
			$sourceAction->setActionParameter(kThumbnailParameterName::NUMBER_OF_SLICES, $this->parameters->get(kThumbFactoryFieldName::VID_SLICES));
			$sourceAction->setActionParameter(kThumbnailParameterName::START_SEC, $this->parameters->get(kThumbFactoryFieldName::START_SEC));
			$sourceAction->setActionParameter(kThumbnailParameterName::END_SEC, $this->parameters->get(kThumbFactoryFieldName::END_SEC));
			$step->addAction($sourceAction);
		}
	}

	protected function addVidSecAction($step, $sec)
	{
		$sourceAction = new kVidSecAction();
		$sourceAction->setActionParameter(kThumbnailParameterName::SECOND, $sec);
		$step->addAction($sourceAction);
	}

	/**
	 * @param kImageTransformationStep $step
	 * @throws ImagickException
	 */
	protected function addSource($step)
	{
		if(kFile::checkFileExists($this->parameters->get(kThumbFactoryFieldName::ORIG_IMAGE_PATH)) && $this->parameters->get(kThumbFactoryFieldName::VID_SEC) === kThumbAdapterParameters::UNSET_PARAMETER)
		{
			/* @var $file_sync FileSync */
			$file_sync = $this->parameters->get(kThumbFactoryFieldName::FILE_SYNC);
			$source = null;
			if($file_sync && $file_sync->isEncrypted())
			{
				$path = $file_sync->createTempClear();
				$source = new kFileSource($path);
				$file_sync->deleteTempClear();
			}
			else
			{
				$source = new kFileSource($this->parameters->get(kThumbFactoryFieldName::ORIG_IMAGE_PATH));
			}

			$step->setSource($source);
			$this->fileSource = true;
		}
		else
		{
			$this->addEntrySource($step);
		}
	}

	protected function addEntrySource($step)
	{
		$source = new kEntrySource();
		$source->setEntry($this->parameters->get(kThumbFactoryFieldName::ENTRY));
		$step->setSource($source);
	}

	/**
	 * @param $gravityPoint
	 * @param $resizeWidth
	 * @param $resizeHeight
	 * @param $cropHeight
	 * @param $cropWidth
	 * @throws kThumbnailException
	 */
	protected function initResizeAndCropCalculationVariables(&$gravityPoint, &$resizeWidth, &$resizeHeight, &$cropHeight, &$cropWidth)
	{
		if(!$this->parameters->get(kThumbFactoryFieldName::HEIGHT) && !$$this->parameters->get(kThumbFactoryFieldName::WIDTH))
		{
			$data = array(kThumbnailErrorMessages::ERROR_STRING => kThumbnailErrorMessages::WIDTH_AND_HEIGHT_ARE_ZERO);
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}

		$resizeWidth = $this->parameters->get(kThumbFactoryFieldName::WIDTH) ? $this->parameters->get(kThumbFactoryFieldName::WIDTH) : $this->parameters->get(kThumbFactoryFieldName::HEIGHT);
		$resizeHeight = $this->parameters->get(kThumbFactoryFieldName::HEIGHT) ? $this->parameters->get(kThumbFactoryFieldName::HEIGHT) : $this->parameters->get(kThumbFactoryFieldName::WIDTH);
		if ($this->parameters->get(kThumbFactoryFieldName::CROP_HEIGHT))
		{
			$cropHeight = $this->parameters->get(kThumbFactoryFieldName::CROP_HEIGHT);
			$this->parameters->set(kThumbFactoryFieldName::SRC_HEIGHT, $cropHeight);
			$gravityPoint = imagick::GRAVITY_NORTH;
		}
		else
		{
			$cropHeight = $this->parameters->get(kThumbFactoryFieldName::SRC_HEIGHT);
		}

		if ($this->parameters->get(kThumbFactoryFieldName::CROP_WIDTH))
		{
			$cropWidth = $this->parameters->get(kThumbFactoryFieldName::CROP_WIDTH);
			$this->parameters->set(kThumbFactoryFieldName::SRC_WIDTH, $cropWidth);
			$gravityPoint = imagick::GRAVITY_WEST;
		}
		else
		{
			$cropWidth = $this->parameters->get(kThumbFactoryFieldName::SRC_WIDTH);
		}
	}

	/**
	 * @param $startSec
	 * @param $vidSlices
	 * @return kImageTransformationStep
	 */
	protected function getFirstStripTransformationStep($startSec, $vidSlices)
	{
		$step = new kImageTransformationStep();
		$this->addEntrySource($step);
		$this->addVidSecAction($step, $startSec);
		$this->handleActionType($step);
		$extendAction = new kExtendImageAction();
		$extendAction->setActionParameter(kThumbnailParameterName::X, $vidSlices);
		$extendAction->setActionParameter(kThumbnailParameterName::EXTEND_VECTOR, kThumbnailParameterName::WIDTH);
		$step->addAction($extendAction);
		return $step;
	}
}