<?php
/**
 * @package plugins.thumbnail
 * @subpackage model
 */

class kImageTransformationAdapter
{
	const COLOR_FORMAT = '%06x';

	/**
	 * @var kThumbAdapterParameters
	 */
	protected $parameters;
	protected $fileSource = false;

	/**
	 * @param kThumbAdapterParameters $parameters
	 * @return kImageTransformation
	 * @throws kThumbnailException
	 */
	public function getImageTransformation($parameters)
	{
		$this->parameters = $parameters;
		$step = new kImageTransformationStep();
		$this->createEntrySource($step);
		$this->handleSourceActions($step);
		$this->prepareInput();
		switch($this->parameters->get(kThumbFactoryFieldName::TYPE))
		{
			case kExtwidgetThumbnailActionType::RESIZE:
			case kExtwidgetThumbnailActionType::RESIZE_WITH_FORCE:
				$this->handleResize(false, $step);
				break;
			case kExtwidgetThumbnailActionType::RESIZE_WITH_PADDING:
				if($this->parameters->get(kThumbFactoryFieldName::WIDTH) && $this->parameters->get(kThumbFactoryFieldName::HEIGHT))
				{
					$this->handleResizeWithPadding($step);
				}
				else
				{
					$this->handleResize( false, $step);
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
			default:
				//throw exception;
		}

		$this->handleStrip($step);
		$this->handleImageOutputAction($step);
		$transformation = new kImageTransformation();
		$transformation->addImageTransformationStep($step);
		return $transformation;
	}

	protected function handleCropAfterResize($step)
	{
		$resizeWidth = $this->parameters->get(kThumbFactoryFieldName::WIDTH) ? $this->parameters->get(kThumbFactoryFieldName::WIDTH) : $this->parameters->get(kThumbFactoryFieldName::HEIGHT);
		$resizeHeight = $this->parameters->get(kThumbFactoryFieldName::HEIGHT) ? $this->parameters->get(kThumbFactoryFieldName::HEIGHT) : $this->parameters->get(kThumbFactoryFieldName::WIDTH);
		$gravityPoint = $this->getGravityByXY();
		$resizeAction = new kResizeAction();
		$resizeAction->setActionParameter(kThumbnailParameterName::WIDTH, $resizeWidth);
		$resizeAction->setActionParameter(kThumbnailParameterName::HEIGHT, $resizeHeight);
		$resizeAction->setActionParameter(kThumbnailParameterName::BEST_FIT, true);
		$step->addAction($resizeAction);
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
		$resizeAction = new kResizeAction();
		$resizeAction->setActionParameter(kThumbnailParameterName::WIDTH, $resizeWidth);
		$resizeAction->setActionParameter(kThumbnailParameterName::HEIGHT, $resizeHeight);
		$resizeAction->setActionParameter(kThumbnailParameterName::BEST_FIT, true);
		$step->addAction($resizeAction);
	}

	/**
	 * @param $gravityPoint
	 * @param $resizeWidth
	 * @param $resizeHeight
	 * @throws kThumbnailException
	 */
	protected function calculateResizeAndCropDimensions(&$gravityPoint, &$resizeWidth, &$resizeHeight)
	{
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
		$this->parameters->set(kThumbFactoryFieldName::BG_COLOR, sprintf(self::COLOR_FORMAT, $this->parameters->get(kThumbFactoryFieldName::BG_COLOR)));
		/* @var $entry entry */
		$entry = $this->parameters->get(kThumbFactoryFieldName::ENTRY);
		if (!$this->parameters->get(kThumbFactoryFieldName::CROP_WIDTH))
		{
			$this->parameters->set(kThumbFactoryFieldName::SRC_WIDTH, $entry->getWidth());
		}

		if (!$this->parameters->get(kThumbFactoryFieldName::CROP_HEIGHT))
		{
			$this->parameters->set(kThumbFactoryFieldName::SRC_HEIGHT , $entry->getHeight());
		}
	}

	/**
	 * @param kImageTransformationStep $step
	 */
	protected function handleResizeWithPadding($step)
	{
		$this->calculatePadding($borderHeight, $borderWidth);
		$reSizeAction = new kResizeAction();
		$reSizeAction->setActionParameter(kThumbnailParameterName::WIDTH, $this->parameters->get(kThumbFactoryFieldName::WIDTH));
		$reSizeAction->setActionParameter(kThumbnailParameterName::HEIGHT, $this->parameters->get(kThumbFactoryFieldName::HEIGHT));
		$step->addAction($reSizeAction);
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

		if($this->parameters->get(kThumbFactoryFieldName::WIDTH) * $this->parameters->get(kThumbFactoryFieldName::CROP_HEIGHT) < $this->parameters->get(kThumbFactoryFieldName::HEIGHT) * $this->parameters->get(kThumbFactoryFieldName::CROP_WIDTH))
		{
			$w = $this->parameters->get(kThumbFactoryFieldName::WIDTH);
			$h = ceil($this->parameters->get(kThumbFactoryFieldName::CROP_HEIGHT) * ($this->parameters->get(kThumbFactoryFieldName::WIDTH) / $this->parameters->get(kThumbFactoryFieldName::CROP_WIDTH)));
			$borderHeight = ceil(($this->parameters->get(kThumbFactoryFieldName::HEIGHT) - $h) / 2);
			if ($borderHeight * 2 + $h > $this->parameters->get(kThumbFactoryFieldName::HEIGHT))
			{
				$h--;
			}
		}
		else
		{
			$h = $this->parameters->get(kThumbFactoryFieldName::HEIGHT);
			$w = ceil($this->parameters->get(kThumbFactoryFieldName::CROP_WIDTH) * ($this->parameters->get(kThumbFactoryFieldName::HEIGHT) / $this->parameters->get(kThumbFactoryFieldName::CROP_HEIGHT)));
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
	protected function handleStrip($step)
	{
		if($this->parameters->get(kThumbFactoryFieldName::STRIP_PROFILES))
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
		$action->setActionParameter(kThumbnailParameterName::WIDTH, $this->parameters->get(kThumbFactoryFieldName::WIDTH));
		$action->setActionParameter(kThumbnailParameterName::HEIGHT, $this->parameters->get(kThumbFactoryFieldName::HEIGHT));
		$action->setActionParameter(kThumbnailParameterName::BEST_FIT, $bestFit);
		$step->addAction($action);
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
		else if($this->parameters->get(kThumbFactoryFieldName::VID_SEC) != kThumbAdapterParameters::UNSET_PARAMETER)
		{
			$sourceAction = new kVidSecAction();
			$sourceAction->setActionParameter(kThumbnailParameterName::SECOND, $this->parameters->get(kThumbFactoryFieldName::VID_SEC));
			$step->addAction($sourceAction);
		}
		else if($this->parameters->get(kThumbFactoryFieldName::VID_SLICES) != kThumbAdapterParameters::UNSET_PARAMETER)
		{
			if($this->parameters->get(kThumbFactoryFieldName::VID_SLICE) != kThumbAdapterParameters::UNSET_PARAMETER)
			{
				$sourceAction = new kVidSliceAction();
				$sourceAction->setActionParameter(kThumbnailParameterName::SLICE_NUMBER, $this->parameters->get(kThumbFactoryFieldName::VID_SLICE));
				$sourceAction->setActionParameter(kThumbnailParameterName::NUMBER_OF_SLICES, $this->parameters->get(kThumbFactoryFieldName::VID_SLICES));
				$sourceAction->setActionParameter(kThumbnailParameterName::START_SEC, $this->parameters->get(kThumbFactoryFieldName::START_SEC));
				$sourceAction->setActionParameter(kThumbnailParameterName::END_SEC, $this->parameters->get(kThumbFactoryFieldName::END_SEC));
				$step->addAction($sourceAction);
			}
			else
			{
				$sourceAction = new kVidStripAction();
				$sourceAction->setActionParameter(kThumbnailParameterName::NUMBER_OF_SLICES, $this->parameters->get(kThumbFactoryFieldName::VID_SLICES));
				$sourceAction->setActionParameter(kThumbnailParameterName::START_SEC, $this->parameters->get(kThumbFactoryFieldName::START_SEC));
				$sourceAction->setActionParameter(kThumbnailParameterName::END_SEC, $this->parameters->get(kThumbFactoryFieldName::END_SEC));
				$step->addAction($sourceAction);
			}
		}
	}

	/**
	 * @param kImageTransformationStep $step
	 * @throws ImagickException
	 */
	protected function createEntrySource($step)
	{
		if(kFile::checkFileExists($this->parameters->get(kThumbFactoryFieldName::ORIG_IMAGE_PATH)))
		{
			$source = new kFileSource($this->parameters->get(kThumbFactoryFieldName::ORIG_IMAGE_PATH));
			$step->setSource($source);
			$this->fileSource = true;
		}
		else
		{
			$source = new kEntrySource();
			$source->setEntry($this->parameters->get(kThumbFactoryFieldName::ENTRY));
			$step->setSource($source);
		}
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
}