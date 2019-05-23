<?php
/**
 * @service thumbnail
 * @package plugins.thumbnail
 * @subpackage api.services
 */

class ThumbnailService extends KalturaBaseUserService
{
	const PARTNER_INDEX = 0;
	const IMAGE_TRANSFORMATION_STEPS_DELIMITER = "+";
	const PARTNER_TOKEN = "/p/";

	/**
	 * Retrieves a thumbnail according to the required transformation
	 * @action transform
	 * @param string $transformString
	 */
	public function transformAction($transformString)
	{
		$transformation = $this->parseTransformString($transformString);
		$transformation->validate();
		$lastModified = $transformation->getLastModified();
		$storage = kThumbStorageBase::getInstance();
		if(!$storage->loadFile($transformString, $lastModified))
		{
			$imagick = $transformation->execute();
			$storage->saveFile($transformString, $imagick, $lastModified);
		}

		$storage->render($lastModified);
	}

	protected function saveTransformationResult($imagick)
	{
		$dc = kDataCenterMgr::getCurrentDc();
		$id = $dc["id"].'_'.kString::generateStringId();
		$fileName = "{$id}.jpg";
		$tempFilePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $fileName;
		$imagick->setImageFormat('jpg');
		file_put_contents($tempFilePath, $imagick);
		return $tempFilePath;
	}

	/**
	 * @param $transformString
	 * @return kImageTransformation
	 */
	protected function parseTransformString($transformString)
	{
		$transformation = new kImageTransformation();
		$steps = explode(self::IMAGE_TRANSFORMATION_STEPS_DELIMITER, $transformString);
		$stepsCount = count($steps);
		for ($i = 0; $i < $stepsCount; $i++)
		{
			if(!empty($steps[$i]))
			{
				$transformation->addImageTransformationStep(thumbnailEngine::parseImageTransformationStep($steps[$i]));
			}
		}

		return $transformation;
	}
}