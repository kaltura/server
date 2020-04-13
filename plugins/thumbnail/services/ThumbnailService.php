<?php
/**
 * @service thumbnail
 * @package plugins.thumbnail
 * @subpackage api.services
 */

class ThumbnailService extends KalturaBaseService
{
	/**
	 * Retrieves a thumbnail according to the required transformation
	 * @action transform
	 * @param string $transformString
	 * @throws kThumbnailException
	 */
	public function transformAction($transformString)
	{
		$transformation = thumbnailStringParser::parseTransformString($transformString);
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
}