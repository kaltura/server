<?php
/**
 * required for VERY old widgets (wiki extension)
 *
 */
class myContentRender
{
	/**
	 * This function returns the html representation of an entry.
	 * the entity id and it's random obfuscator (which is used also for versioning)
	 * if the random obfuscator is lower then the MIN_OBFUSCATOR_VALUE the file name
	 * refers to a static template which resides in the templates directory
	 * @param int $mediaType = the media type (video, image, text, etc...)
	 * @param string $data = path to data stored on kaltura servers
	 * @param int $width = width of html object
	 * @param int $height = height of html object
	 * @return string the html reprenstation of the given data
	 */
	static public function createPlayerMedia($entry)
	{
		$status = $entry->getStatus();
		$mediaType = $entry->getMediaType();
		
		$kmediaType = entry::ENTRY_MEDIA_TYPE_TEXT;
		
		if ($status == entry::ENTRY_STATUS_IMPORT)
		{
			$kmediaData = 'The clip is currently being imported. This may take a couple of minutes. You can continue browsing this Kaltura';
		}
		else if ($status == entry::ENTRY_STATUS_PRECONVERT)
		{
			$kmediaData = 'Clip is being converted. This might take a couple of minutes. You can continue browsing the Kaltura.' ;// 'Entry is being converted';
		}
		else if ($status == entry::ENTRY_STATUS_ERROR_CONVERTING)
		{
			$kmediaData = 'Error converting entry';
		}
		else if ($mediaType == entry::ENTRY_MEDIA_TYPE_SHOW)
		{
			$kmediaType = $mediaType;
			$kmediaData = $entry->getId();
		}
		else if ($mediaType == entry::ENTRY_MEDIA_TYPE_IMAGE ||
			$mediaType == entry::ENTRY_MEDIA_TYPE_AUDIO ||
			$mediaType == entry::ENTRY_MEDIA_TYPE_VIDEO)
		{
			$kmediaType = $mediaType;
			$kmediaData = "http://".$_SERVER['SERVER_NAME'].$entry->getDataPath();
		}
		else
		{
			$kmediaData = 'Error: Cannot Show Object';
		}
		
		return array($status, $kmediaType, $kmediaData);
	}

	/**
	 * This function returns the html representation of an entry that can be embedded into other sites.
	 * It is very similar to the function above, except omits the surrounding div tags.
	 *
	 * the entity id and it's random obfuscator (which is used also for versioning)
	 * if the random obfuscator is lower then the MIN_OBFUSCATOR_VALUE the file name
	 * refers to a static template which resides in the templates directory
	 * @param int $mediaType = NOT USED!!! the media type (video, image, text, etc...)
	 * @param string $data = path to data stored on kaltura servers
	 * @param int $width = width of html object
	 * @param int $height = height of html object
	 * @return string the html reprenstation of the given data
	 */
	static public function createShareHTML($mediaType, $data, $width, $height)
	{
		$imagesExtArray = array('bmp', 'png', 'jpg', 'gif');

		$shareHTML = 'Cant Show Object';

		$ext = strtolower(pathinfo($data, PATHINFO_EXTENSION));
		if (in_array($ext, $imagesExtArray))
		{
			$shareHTML = '<img style="width:_kal_width_px;height:_kal_height_px" src="'.$data.'">';
		}

		$shareHTML = str_ireplace("_kal_width_", $width, $shareHTML);
		$shareHTML = str_ireplace("_kal_height_", $height, $shareHTML);

		return $shareHTML;
	}
}

?>
