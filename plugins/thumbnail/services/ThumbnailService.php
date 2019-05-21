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
	 * @return bool
	 */
	public function transformAction($transformString)
	{
		$transformation = $this->parseTransformString($transformString);
		$transformation->validate();
		$lastModified = $transformation->getLastModified();
		$imagick = $transformation->execute();
		$tempFilePath = self::saveTransformationResult($imagick);
		$renderer = kFileUtils::getDumpFileRenderer($tempFilePath, null, 0 , $lastModified);
		$renderer->output();
		return;
	}

	protected function saveTransformationResult($imagick)
	{
		$dc = kDataCenterMgr::getCurrentDc();
		$id = $dc["id"].'_'.kString::generateStringId();
		$fileName = "{$id}.jpg";
		$tempFilePath = sys_get_temp_dir().DIRECTORY_SEPARATOR . $fileName;
		$imagick->setImageFormat('jpg');
		file_put_contents($tempFilePath, $imagick);
		return $tempFilePath;
	}

	/**
	 * @param $transformString
	 * @return imageTransformation
	 */
	protected function parseTransformString($transformString)
	{
		$transformation = new imageTransformation();
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