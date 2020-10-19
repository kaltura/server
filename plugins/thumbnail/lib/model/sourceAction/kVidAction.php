<?php
/**
 * @package plugins.thumbnail
 * @subpackage model.sourceAction
 */

abstract class kVidAction extends kSourceAction
{
	protected static $action_name = "abstract action";
	protected $second;
	protected $newWidth;
	protected $newHeight;

	protected function initParameterAlias()
	{
		$kVidAlias = array(
			'w' => kThumbnailParameterName::WIDTH,
			'h' => kThumbnailParameterName::HEIGHT,
		);
		$this->parameterAlias = array_merge($this->parameterAlias, $kVidAlias);
	}

	protected function extractActionParameters()
	{
		$this->newWidth = $this->getIntActionParameter(kThumbnailParameterName::WIDTH);
		$this->newHeight = $this->getIntActionParameter(kThumbnailParameterName::HEIGHT);
	}

	protected function validateInput()
	{
		if(!$this->source instanceof kEntrySource)
		{
			$data = array(kThumbnailErrorMessages::ERROR_STRING => self::$action_name . kThumbnailErrorMessages::ENTRY_SOURCE_ONLY);
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}

		if($this->source->getEntryMediaType() != entry::ENTRY_MEDIA_TYPE_VIDEO && $this->source->getEntryType() != entryType::PLAYLIST)
		{
			throw new kThumbnailException(kThumbnailException::MUST_HAVE_VIDEO_SOURCE, kThumbnailException::MUST_HAVE_VIDEO_SOURCE);
		}

		$this->validateDimensions();
		$this->validatePermissions();
	}

	protected function validateDimensions()
	{
		if($this->newWidth && (!is_numeric($this->newWidth) || $this->newWidth < self::MIN_DIMENSION || $this->newWidth > self::MAX_DIMENSION))
		{
			$data = array(kThumbnailErrorMessages::ERROR_STRING => kThumbnailErrorMessages::WIDTH_DIMENSIONS);
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}

		if($this->newHeight && (!is_numeric($this->newHeight) || $this->newHeight < self::MIN_DIMENSION || $this->newHeight > self::MAX_DIMENSION))
		{
			$data = array(kThumbnailErrorMessages::ERROR_STRING => kThumbnailErrorMessages::HEIGHT_DIMENSIONS);
			throw new kThumbnailException(kThumbnailException::BAD_QUERY, kThumbnailException::BAD_QUERY, $data);
		}
	}

	protected function validatePermissions()
	{
		kThumbnailSecurityHelper::verifyEntryAccess($this->source->getEntry());
	}

	protected function getTempThumbnailPath()
	{
		$dc = kDataCenterMgr::getCurrentDc();
		$filePath = $dc['id'].'_'.kString::generateStringId();
		return sys_get_temp_dir().DIRECTORY_SEPARATOR . $filePath;
	}

	/**
	 * @param entry $entry
	 * @param $destPath
	 * @param $second
	 * @throws kThumbnailException
	 */
	protected function captureThumb($entry, $destPath, $second)
	{
		$success = myPackagerUtils::captureThumb($entry, $destPath, $second, $flavorAssetId, $this->newWidth, $this->newHeight);
		if(!$success)
		{
			if ($entry->getType() == entryType::PLAYLIST)
			{
				$entry = myPlaylistUtils::getFirstEntryFromPlaylist($entry);
				if (!$entry)
				{
					throw new kTumbnailException(kThumbnailException::PLAYLIST_ENTRY_NOT_FOUND, kThumbnailException::PLAYLIST_ENTRY_NOT_FOUND);
				}
			}

			if (!$this->newWidth)
			{
				$this->newWidth = kThumbAdapterParameters::UNSET_PARAMETER;
			}

			if (!$this->newHeight)
			{
				$this->newHeight = kThumbAdapterParameters::UNSET_PARAMETER;
			}

			$success = myEntryUtils::captureLocalThumb($entry, $destPath, $second, null, null, null, $flavorAssetId, $this->newWidth, $this->newHeight);
		}

		if(!$success)
		{
			$data = array(kThumbnailErrorMessages::ERROR_STRING => static::$action_name . kThumbnailErrorMessages::FAILED);
			throw new kThumbnailException(kThumbnailException::ACTION_FAILED, kThumbnailException::ACTION_FAILED, $data);
		}
	}

}