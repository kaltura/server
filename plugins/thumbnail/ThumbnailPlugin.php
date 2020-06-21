<?php
/**
 * @package plugins.thumbnail
 */
class ThumbnailPlugin extends KalturaPlugin implements IKalturaServices, IKalturaPermissions, IKalturaPending, IKalturaExceptionHandler, IKalturaImageTransformationExecutor
{
	const PLUGIN_NAME = 'thumbnail';
	const THUMBNAIL_CORE_EXCEPTION = 'kThumbnailException';
	const THUMBNAIL_ADAPTER_PARTNERS = 'adapter_partners';
	const THUMBNAIL_ADAPTER_PARTNER_PACKAGES = 'adapter_partner_packages';
	const THUMBNAIL_MAP_NAME = 'thumbnail';
	const ALL_PARTNERS_WILD_CHAR = '*';

	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}

	/* (non-PHPdoc)
	 * @see IKalturaPermissions::isAllowedPartner()
	 */
	public static function isAllowedPartner($partnerId)
	{
		return true;
	}

	public static function dependsOn()
	{
		$dependency = new KalturaDependency(FileSyncPlugin::getPluginName());
		return array($dependency);
	}

	public static function getServicesMap ()
	{
		$map = array(
			'thumbnail' => 'ThumbnailService',
		);

		return $map;
	}

	/**
	 * @param kThumbnailException $exception
	 * @return KalturaAPIException
	 * @throws Exception
	 */
	public static function handleThumbnailException($exception)
	{
		$code = $exception->getCode();
		$data = $exception->getData();
		switch ($code)
		{
			case kThumbnailException::FAILED_TO_PARSE_ACTION:
				$object = new KalturaAPIException(KalturaThumbnailErrors::FAILED_TO_PARSE_ACTION, $data[kThumbnailErrorMessages::ACTION_STRING]);
				break;
			case kThumbnailException::FAILED_TO_PARSE_SOURCE:
				$object = new KalturaAPIException(KalturaThumbnailErrors::FAILED_TO_PARSE_SOURCE, $data[kThumbnailErrorMessages::SOURCE_STRING]);
				break;
			case kThumbnailException::MISSING_SOURCE_ACTIONS_FOR_TYPE:
				$object = new KalturaAPIException(KalturaThumbnailErrors::MISSING_SOURCE_ACTIONS_FOR_TYPE, $data[kThumbnailErrorMessages::ENTRY_TYPE]);
				break;
			case kThumbnailException::EMPTY_IMAGE_TRANSFORMATION:
				$object = new KalturaAPIException(KalturaThumbnailErrors::EMPTY_IMAGE_TRANSFORMATION);
				break;
			case kThumbnailException::FIRST_STEP_CANT_USE_COMP_ACTION:
				$object = new KalturaAPIException(KalturaThumbnailErrors::FIRST_STEP_CANT_USE_COMP_ACTION);
				break;
			case kThumbnailException::MISSING_COMPOSITE_ACTION:
				$object = new KalturaAPIException(KalturaThumbnailErrors::MISSING_COMPOSITE_ACTION);
				break;
			case kThumbnailException::TRANSFORMATION_RUNTIME_ERROR:
				$object = new KalturaAPIException(KalturaThumbnailErrors::TRANSFORMATION_RUNTIME_ERROR);
				break;
			case kThumbnailException::BAD_QUERY:
				$object = new KalturaAPIException(KalturaThumbnailErrors::BAD_QUERY, $data[kThumbnailErrorMessages::ERROR_STRING]);
				break;
			case kThumbnailException::ACTION_FAILED:
				$object = new KalturaAPIException(KalturaThumbnailErrors::ACTION_FAILED);
				break;
			case kThumbnailException::NOT_ALLOWED_PARAMETER:
				$object = new KalturaAPIException(KalturaThumbnailErrors::NOT_ALLOWED_PARAMETER);
				break;
			case kThumbnailException::MUST_HAVE_VIDEO_SOURCE:
				$object = new KalturaAPIException(KalturaThumbnailErrors::MUST_HAVE_VIDEO_SOURCE);
				break;
			case kThumbnailException::MISSING_S3_CONFIGURATION:
				$object = new KalturaAPIException(KalturaThumbnailErrors::MISSING_S3_CONFIGURATION);
				break;
			case kThumbnailException::CACHE_ERROR:
				$object = new KalturaAPIException(KalturaThumbnailErrors::CACHE_ERROR);
				break;
			case kThumbnailException::ENTRY_NOT_FOUND:
				$object = new KalturaAPIException(KalturaThumbnailErrors::ENTRY_ID_NOT_FOUND, $data[kThumbnailErrorMessages::ENTRY_ID]);
				break;
			default:
				$object = null;
		}

		return $object;
	}

	public function getExceptionMap()
	{
		return array(
			self::THUMBNAIL_CORE_EXCEPTION => array('ThumbnailPlugin', 'handleThumbnailException'),
		);
	}


	/**
	 * @param entry $entry
	 * @param $version
	 * @param $width
	 * @param $height
	 * @param $type
	 * @param $bgcolor
	 * @param $quality
	 * @param $src_x
	 * @param $src_y
	 * @param $src_w
	 * @param $src_h
	 * @param $vid_sec
	 * @param $vid_slice
	 * @param $vid_slices
	 * @param $density
	 * @param $orig_image_path
	 * @param $stripProfiles
	 * @param $format
	 * @param $start_sec
	 * @param $end_sec
	 * @return string
	 * @throws kThumbnailException
	 */
	public function getImageFile($entry, $version, $width, $height, $type, $bgcolor, $quality, $src_x, $src_y, $src_w, $src_h, $vid_sec, $vid_slice, $vid_slices, $orig_image_path, $density, $stripProfiles, $format, $start_sec, $end_sec)
	{
		$result = false;
		if($this->shouldUseThumbnailAdapter($entry->getPartnerId()))
		{
			$adapter = kThumbnailAdapterFactory::getAdapter($entry);
			$params = kThumbnailAdapterFactory::getThumbAdapterParameters($entry, $version, $width, $height, $type, $bgcolor, $quality, $src_x, $src_y, $src_w, $src_h,
				$vid_sec, $vid_slice, $vid_slices, $orig_image_path, $density, $stripProfiles, $format, $start_sec, $end_sec);
			$result = $adapter->resizeEntryImage($params);
		}

		return $result;
	}

	public function shouldUseThumbnailAdapter($partnerId)
	{
		$result = false;
		$partnerIds = kConf::get(self::THUMBNAIL_ADAPTER_PARTNERS, self::THUMBNAIL_MAP_NAME, array());
		if (in_array($partnerId, $partnerIds) || in_array(self::ALL_PARTNERS_WILD_CHAR, $partnerIds))
		{
			$result = true;
		}
		else
		{
			$partnerPackages = kConf::get(self::THUMBNAIL_ADAPTER_PARTNER_PACKAGES, self::THUMBNAIL_MAP_NAME, array());
			$partner = PartnerPeer::retrieveActiveByPK($partnerId);
			if ( $partner && in_array($partner->getPartnerPackage(), $partnerPackages) )
			{
				$result = true;
			}
		}

		return $result;
	}
}