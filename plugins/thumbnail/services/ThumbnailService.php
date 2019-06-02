<?php
/**
 * @service thumbnail
 * @package plugins.thumbnail
 * @subpackage api.services
 */

//require_once ("/opt/kaltura/app/plugins/thumbnail/lib/model/thumbStorage/kThumbStorageBase.php");
class ThumbnailService extends KalturaBaseUserService
{
	/**
	 * Retrieves a thumbnail according to the required transformation
	 * @action transform
	 * @param string $transformString
	 */
	public function transformAction($transformString)
	{
<<<<<<< HEAD
		$transformationUrl = $this->getTransformationStringFromUri();
		$transformation = $this->parseUrl($transformationUrl);
		$storage = kThumbStorageBase::getInstance();
		if( !$storage->loadFile($transformationUrl) )
		{
			$transformation->validate();
			$imagick = $transformation->execute();
			$storage->saveFile($transformationUrl,$imagick);
		}
		$storage->render();
	}

	protected function parseUrl($url)
	{
		$transformation = new imageTransformation();
		$steps = explode(self::IMAGE_TRANSFORMATION_STEPS_DELIMITER, $url);
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
=======
		$transformation = thumbnailStringParser::parseTransformString($transformString);
		$transformation->validate();
		$lastModified = $transformation->getLastModified();
		$storage = kThumbStorageBase::getInstance();
		if(!$storage->loadFile($transformString, $lastModified))
>>>>>>> bc2267b517dd08ee9a78c282f90b0796fa25ad58
		{
			$imagick = $transformation->execute();
			$storage->saveFile($transformString, $imagick, $lastModified);
		}

		$storage->render($lastModified);
	}
}