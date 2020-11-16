<?php
/**
 * @package plugins.thumbnail
 * @subpackage model
 */

class kThumbnailAdapterFactory
{
	/**
	 * @param entry $entry
	 * @return kBaseResizeAdapter
	 */
	public static function getAdapter($entry)
	{
		if(myEntryUtils::shouldServeVodFromLive($entry))
		{
			return new kResizeLiveEntryAdapter();
		}

		if($entry->getType() == entryType::PLAYLIST)
		{
			if($entry->getMediaType() == PlaylistType::STATIC_LIST)
			{
				return new kResizeStitchedPlaylistAdapter();
			}

			return new kResizePlaylistAdapter();
		}

		if($entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_IMAGE)
		{
			return new kResizeImageEntryAdapter();
		}

		return new kBaseResizeAdapter();
	}
	
	public static function getResizeThumbAdapterParameters(entry $entry, $version, $width, $height, $type, $bgcolor, $quality, $src_x, $src_y, $src_w, $src_h,
	                                                       $vid_sec, $vid_slice, $vid_slices, $orig_image_path, $density, $stripProfiles, $format, $fileSync, $start_sec, $end_sec)
	{
		$params = new kThumbAdapterParameters();
		$params->set(kThumbFactoryFieldName::ENTRY, $entry);
		$params->set(kThumbFactoryFieldName::SOURCE_ENTRY, $entry);
		$params->set(kThumbFactoryFieldName::VERSION, $version);
		$params->set(kThumbFactoryFieldName::WIDTH, $width);
		$params->set(kThumbFactoryFieldName::HEIGHT, $height);
		$params->set(kThumbFactoryFieldName::TYPE, $type);
		$params->set(kThumbFactoryFieldName::BG_COLOR, $bgcolor);
		$params->set(kThumbFactoryFieldName::QUALITY, $quality);
		$params->set(kThumbFactoryFieldName::CROP_X, $src_x);
		$params->set(kThumbFactoryFieldName::CROP_Y, $src_y);
		$params->set(kThumbFactoryFieldName::SRC_WIDTH, $src_w);
		$params->set(kThumbFactoryFieldName::SRC_HEIGHT, $src_h);
		$params->set(kThumbFactoryFieldName::CROP_WIDTH, $src_w);
		$params->set(kThumbFactoryFieldName::CROP_HEIGHT, $src_h);
		$params->set(kThumbFactoryFieldName::VID_SEC, $vid_sec);
		$params->set(kThumbFactoryFieldName::VID_SLICE, intval($vid_slice));
		$params->set(kThumbFactoryFieldName::VID_SLICES, intval($vid_slices));
		$params->set(kThumbFactoryFieldName::ORIG_IMAGE_PATH, $orig_image_path);
		$params->set(kThumbFactoryFieldName::DENSITY, $density);
		$params->set(kThumbFactoryFieldName::STRIP_PROFILES, $stripProfiles);
		$params->set(kThumbFactoryFieldName::IMAGE_FORMAT, $format);
		$params->set(kThumbFactoryFieldName::START_SEC, $start_sec);
		$params->set(kThumbFactoryFieldName::END_SEC, $end_sec);
		$params->set(kThumbFactoryFieldName::FILE_SYNC, $fileSync);
		self::validateResizeThumbAdapterParameters($params);
		return $params;
	}
	
	/**
	 * @param kThumbAdapterParameters $kThumbAdapterParameters
	 */
	protected static function validateResizeThumbAdapterParameters($kThumbAdapterParameters)
	{
		$vidSlices = $kThumbAdapterParameters->get(kThumbFactoryFieldName::VID_SLICES);
		if($vidSlices !== $kThumbAdapterParameters::UNSET_PARAMETER && $vidSlices <= 0)
		{
			KExternalErrors::dieError(KExternalErrors::BAD_QUERY, kThumbnailErrorMessages::NUMBER_OF_SLICE);
		}
	}
}