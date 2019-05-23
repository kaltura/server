<?php
/**
 * @package plugins.thumbnail
 */
class ThumbnailPlugin extends KalturaPlugin implements IKalturaServices, IKalturaPermissions, IKalturaPending, IKalturaExceptionHandler
{
	const PLUGIN_NAME = 'thumbnail';
	const THUMBNAIL_CORE_EXCEPTION = 'kThumbnailException';

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

	public static function handleThumbnailException($exception)
	{
		$code = $exception->getCode();
		$data = $exception->getData();
		switch ($code)
		{
			case kThumbnailException::FAILED_TO_PARSE_ACTION:
				$object = new KalturaAPIException(KalturaThumbnailErrors::FAILED_TO_PARSE_ACTION, $data['actionString']);
				break;
			case kThumbnailException::FAILED_TO_PARSE_SOURCE:
				$object = new KalturaAPIException(KalturaThumbnailErrors::FAILED_TO_PARSE_SOURCE, $data['sourceString']);
				break;
			case kThumbnailException::MISSING_SOURCE_ACTIONS_FOR_TYPE:
				$object = new KalturaAPIException(KalturaThumbnailErrors::MISSING_SOURCE_ACTIONS_FOR_TYPE, $data['entryType']);
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
				$object = new KalturaAPIException(KalturaThumbnailErrors::BAD_QUERY, $data['errorString']);
				break;
			case kThumbnailException::VID_SEC_FAILED:
				$object = new KalturaAPIException(KalturaThumbnailErrors::VID_SEC_FAILED);
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
}