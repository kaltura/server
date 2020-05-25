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

		if($entry->getType() == entryType::PLAYLIST && $entry->getMediaType() == PlaylistType::STATIC_LIST)
		{
			return new kResizeStitchedPlaylistAdapter();
		}

		if($entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_IMAGE)
		{
			return new kResizeImageEntryAdapter();
		}

		return new kBaseResizeAdapter();
	}

	public static function getThumbAdapterParameters(entry $entry, $version, $width, $height, $type, $bgcolor, $quality, $src_x, $src_y, $src_w, $src_h,
													 $vid_sec, $vid_slice, $vid_slices, $density, $stripProfiles, $format, $start_sec, $end_sec)
	{
		$params = new kThumbAdapterParameters();
		$params->set(kThumbFactoryFieldName::ENTRY, $entry);
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
		$params->set(kThumbFactoryFieldName::VID_SEC, $vid_sec);
		$params->set(kThumbFactoryFieldName::VID_SLICE, $vid_slice);
		$params->set(kThumbFactoryFieldName::VID_SLICES, $vid_slices);
		$params->set(kThumbFactoryFieldName::DENSITY, $density);
		$params->set(kThumbFactoryFieldName::STRIP_PROFILES, $stripProfiles);
		$params->set(kThumbFactoryFieldName::IMAGE_FORMAT, $format);
		$params->set(kThumbFactoryFieldName::START_SEC, $start_sec);
		$params->set(kThumbFactoryFieldName::END_SEC, $end_sec);
		return $params;
	}
}
