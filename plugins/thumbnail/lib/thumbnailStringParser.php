<?php
/**
 * @package plugins.thumbnail
 * @subpackage lib
 */

class thumbnailStringParser
{
	const IMAGE_TRANSFORMATION_STEPS_DELIMITER = "+";
	const SOURCE_INDEX = 0;
	const SOURCE_TYPE_INDEX = 0;
	const SOURCE_VALUE_INDEX = 1;
	const ACTION_NAME_INDEX = 0;
	const IMAGE_ACTION_DELIMITER = ',';
	const PARAMETER_DELIMITER = '_';
	const VALUE_DELIMITER = '-';
	const PARAMETER_NAME_INDEX = 0;
	const PARAMETER_VALUE_INDEX = 1;

	protected static $actionsAlias = array(
		"c" => "kCropAction",
		"crop" => "kCropAction",
		"resize" => "kResizeAction",
		"re" => "kResizeAction",
		"comp" => "kCompositeAction",
		"composite" => "kCompositeAction",
		"vidSec" => "kVidSecAction",
		"vidsec" => "kVidSecAction",
		"vSec" => "kVidSecAction",
		"rotate" => "kRotateAction",
		"r" =>	"kRotateAction",
		"t" => "kTextAction",
		"txt" => "kTextAction",
		"text" => "kTextAction",
		"vidSlice" => "kVidSliceAction",
		"vidslice" => "kVidSliceAction",
		"vslice" => "kVidSliceAction",
		"vidStrip" => "kVidStripAction",
		"vidstrip" => "kVidStripAction",
		"vstrip" => "kVidStripAction"
	);

	/**
	 * @param $stepString
	 * @return kImageTransformationStep
	 */
	protected static function parseImageTransformationStep($stepString)
	{
		$imageActions =  explode(self::IMAGE_ACTION_DELIMITER, $stepString);
		$imageActionsCount = count($imageActions);
		$source = thumbnailStringParser::parseSource($imageActions[self::SOURCE_INDEX]);
		$step = new kImageTransformationStep();
		$step->setSource($source);
		for ($i = 1; $i < $imageActionsCount; $i++)
		{
			if(!empty($imageActions[$i]))
			{
				$imageAction = thumbnailStringParser::parseImageAction($imageActions[$i]);
				$step->addAction($imageAction);
			}
		}

		return $step;
	}

	public static function parseSource($sourceString)
	{
		$sourceParameters = explode(self::VALUE_DELIMITER, $sourceString);
		if(count($sourceParameters) < 2)
		{
			$data = array("sourceString" => $sourceString);
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::FAILED_TO_PARSE_SOURCE, $data);
		}

		$sourceType = $sourceParameters[self::SOURCE_TYPE_INDEX];
		switch($sourceType)
		{
			case kSourceType::ID:
				$source = new kEntrySource($sourceParameters[self::SOURCE_VALUE_INDEX]);
				break;
			default:
				$data = array("sourceString" => $sourceString);
				throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::FAILED_TO_PARSE_SOURCE, $data);
		}

		return $source;
	}

	public static function parseImageAction($imageActionString)
	{
		$parameters = explode(self::PARAMETER_DELIMITER, $imageActionString);
		$parametersCount = count($parameters);
		if(!array_key_exists($parameters[self::ACTION_NAME_INDEX], self::$actionsAlias))
		{
			$data = array('actionString' => $parameters[self::ACTION_NAME_INDEX]);
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::FAILED_TO_PARSE_ACTION, $data);
		}

		$imageAction = self::createImageAction($parameters[self::ACTION_NAME_INDEX]);
		self::setActionParameter($imageAction, $parameters, $parametersCount);
		return $imageAction;
	}

	/**
	 * @param kImagickAction $imageAction
	 * @param array $parameters
	 * @param int $parametersCount
	 */
	protected static function setActionParameter($imageAction, $parameters, $parametersCount)
	{
		for ($i = 1; $i < $parametersCount; $i++)
		{
			$parameter = explode(self::VALUE_DELIMITER, $parameters[$i], 2);
			if(count($parameter) == 1)
			{
				$imageAction->setActionParameter($parameter[self::PARAMETER_NAME_INDEX], true);
			}
			else
			{
				$imageAction->setActionParameter($parameter[self::PARAMETER_NAME_INDEX], $parameter[self::PARAMETER_VALUE_INDEX]);
			}
		}
	}

	/**
	 * @param $actionName
	 * @return kImagickAction
	 */
	protected static function createImageAction($actionName)
	{
		$className = self::$actionsAlias[$actionName];
		return new $className();
	}


	/**
	 * @param $transformString
	 * @return kImageTransformation
	 */
	public static function parseTransformString($transformString)
	{
		$transformation = new kImageTransformation();
		$steps = explode(self::IMAGE_TRANSFORMATION_STEPS_DELIMITER, $transformString);
		$stepsCount = count($steps);
		for ($i = 0; $i < $stepsCount; $i++)
		{
			if(!empty($steps[$i]))
			{
				$transformation->addImageTransformationStep(self::parseImageTransformationStep($steps[$i]));
			}
		}

		return $transformation;
	}
}
