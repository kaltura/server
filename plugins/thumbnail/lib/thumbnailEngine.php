<?php
/**
 * @package plugins.thumbnail
 * @subpackage lib
 */

class thumbnailEngine
{
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
		"c" => "cropAction",
		"crop" => "cropAction",
		"resize" => "resizeAction",
		"re" => "resizeAction",
		"comp" => "compositeAction",
		"composite" => "compositeAction",
		"vidSec" => "vidSecAction"
	);

	/**
	 * @param $stepString
	 * @return imageTransformationStep
	 */
	public static function parseImageTransformationStep($stepString)
	{
		$imageActions =  explode(self::IMAGE_ACTION_DELIMITER, $stepString);
		$imageActionsCount = count($imageActions);
		$source = thumbnailEngine::parseSource($imageActions[self::SOURCE_INDEX]);
		$step = new imageTransformationStep();
		$step->setSource($source);
		for ($i = 1; $i < $imageActionsCount; $i++)
		{
			if(!empty($imageActions[$i]))
			{
				$imageAction = thumbnailEngine::parseImageAction($imageActions[$i]);
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
			throw new KalturaAPIException(KalturaThumbnailErrors::FAILED_TO_PARSE_SOURCE, $sourceString);
		}

		$sourceType = $sourceParameters[self::SOURCE_TYPE_INDEX];
		switch($sourceType)
		{
			case "id":
				$source = new entrySource($sourceParameters[self::SOURCE_VALUE_INDEX]);
				break;
			default:
				throw new KalturaAPIException(KalturaThumbnailErrors::FAILED_TO_PARSE_SOURCE, $sourceType);
		}

		return $source;
	}

	public static function parseImageAction($imageActionString)
	{
		$parameters = explode(self::PARAMETER_DELIMITER, $imageActionString);
		$parametersCount = count($parameters);
		if(!array_key_exists($parameters[self::ACTION_NAME_INDEX], self::$actionsAlias))
		{
			throw new KalturaAPIException(KalturaThumbnailErrors::FAILED_TO_PARSE_ACTION, $parameters[self::ACTION_NAME_INDEX]);
		}

		$imageAction = self::createImageAction($parameters[self::ACTION_NAME_INDEX]);
		self::setActionParameter($imageAction, $parameters, $parametersCount);
		return $imageAction;
	}

	/**
	 * @param imagickAction $imageAction
	 * @param array $parameters
	 * @param int $parametersCount
	 */
	protected static function setActionParameter($imageAction, $parameters, $parametersCount)
	{
		for ($i = 1; $i < $parametersCount; $i++)
		{
			$parameter = explode(self::VALUE_DELIMITER, $parameters[$i], 2);
			$imageAction->setActionParameter($parameter[self::PARAMETER_NAME_INDEX], $parameter[self::PARAMETER_VALUE_INDEX]);
		}
	}

	/**
	 * @param $actionName
	 * @return imagickAction
	 */
	protected static function createImageAction($actionName)
	{
		$className = self::$actionsAlias[$actionName];
		return new $className();
	}
}
