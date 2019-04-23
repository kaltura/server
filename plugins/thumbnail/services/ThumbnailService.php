<?php
/**
 * @service thumbnail
 * @package plugins.thumbnail
 * @subpackage api.services
 */

class ThumbnailService extends KalturaBaseUserService
{
	const PARTNER_INDEX = 0;
	const IMAGE_TRANSFORMATION_STEPS_DELIMITER = "/";
	const PARTNER_TOKEN = "/p/";

	/**
	 * Retrieves a thumbnail according to the required transformation
	 * @action transform
	 * @return bool
	 *
	 */
	public function transformAction()
	{
		$transformation = $this->parseUrl();
		$transformation->validate();
		$imagick = $transformation->execute();
		file_put_contents("/tmp/result.png", $imagick);
		$renderer = kFileUtils::getDumpFileRenderer("/tmp/result.png", null);
		$renderer->output();
		return;
	}

	protected function parseUrl()
	{

		$transformParametersString = $this->getTransformationStringFromUri();
		$transformation = new imageTransformation();
		$steps = explode(self::IMAGE_TRANSFORMATION_STEPS_DELIMITER, $transformParametersString);
		$stepsCount = count($steps);
		for ($i = 1; $i < $stepsCount; $i++)
		{
			if(!empty($steps[$i]))
			{
				$transformation->addImageTransformationStep(thumbnailEngine::parseImageTransformationStep($steps[$i]));
			}
		}

		return $transformation;
	}

	protected function getTransformationStringFromUri()
	{
		$uri  = $_SERVER['REQUEST_URI'];
		$transformParametersStart = strpos($uri,self::PARTNER_TOKEN);
		if($transformParametersStart === false)
		{
			throw new KalturaAPIException(KalturaThumbnailErrors::MISSING_PARTNER_PARAMETER_IN_URL);
		}

		return substr($uri, $transformParametersStart + 3);
	}
}