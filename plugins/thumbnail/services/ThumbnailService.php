<?php
/**
 * @service thumbnail
 * @package plugins.thumbnail
 * @subpackage api.services
 */

class ThumbnailService extends KalturaBaseUserService
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

	/**
	 * Retrieves the entry thumbnail color map
	 * @action getColorMap
	 * @param string $entryId
	 * @param int $minimumColorDiff
	 * @param int $maxColors
	 * @return KalturaColorArray
	 * @throws ImagickException
	 * @throws ImagickPixelException
	 * @throws KalturaAPIException
	 */
	public function getColorMap($entryId, $minimumColorDiff = 0, $maxColors = 5)
	{
		$entrySource = new kEntrySource($entryId);
		$entry = $entrySource->getEntry();
		$subType = entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB;
		if($entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_IMAGE)
		{
			$subType = entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA;
		}

		$dataKey = $entry->getSyncKey($subType);
		$imageBlob = kFileSyncUtils::file_get_contents($dataKey);
		$image = new Imagick();
		$image->readImageBlob($imageBlob);
		$colors = $image->getImageHistogram();
		$tempResult = array();
		foreach($colors as $color)
		{
			/* @var $color ImagickPixel */
			$kalturaColor = new KalturaColor();
			$colorArray = $color->getColor();
			$kalturaColor->red = $colorArray['r'];
			$kalturaColor->blue = $colorArray['b'];
			$kalturaColor->green = $colorArray['g'];
			$kalturaColor->alpha = $colorArray['a'];
			$kalturaColor->count = $color->getColorCount();
			$tempResult[] = $kalturaColor;
		}

		usort($tempResult, function($a, $b) {return $b->count - $a->count;});
		$tempResult = array_slice($tempResult, 0, $maxColors);
		$result = KalturaColorArray::fromDbArray($tempResult);
		return $result;
	}
}