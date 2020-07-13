<?php
/**
 * @package plugins.thumbnail
 * @subpackage model
 */

class kBaseResizeAdapter
{
	const DEFAULT_FILE_NAME = '0.jpg';
	const THUMB_FILE_NO_CACHE_PREFIX = '&';
	const THUMB_NAME_NO_CACHE_POSTFIX = '_NOCACHE_';
	const LOCAL_MAP_NAME = 'local';
	const CONFIGURATION_PARAM_NAME = 'thumb_path';
	const DEFAULT_THUMB_DIR = 'tempthumb';
	const ENTITY_NAME_PREFIX = 'entry/';
	const CACHED_EXISTS_HEADER = 'X-Kaltura:cached-thumb-exists,';
	const THUMB_PROCESSING_LOCK_DURATION = 300; //5 minutes
	const LOCK_KEY_PREFIX = 'thumb-processing-resize';

	/**
	 * @var kThumbAdapterParameters
	 */
	protected $parameters;

	protected $thumbName;

	protected $entryThumbFilename;

	protected $finalThumbPath;

	protected function calculateBaseThumbName()
	{
		$entry = $this->getEntry();
		$width = $this->parameters->get(kThumbFactoryFieldName::WIDTH);
		$height = $this->parameters->get(kThumbFactoryFieldName::HEIGHT);
		$type = $this->parameters->get(kThumbFactoryFieldName::TYPE);
		$bgColor= $this->parameters->get(kThumbFactoryFieldName::BG_COLOR);
		$quality= $this->parameters->get(kThumbFactoryFieldName::QUALITY);
		$src_x= $this->parameters->get(kThumbFactoryFieldName::CROP_X);
		$src_y= $this->parameters->get(kThumbFactoryFieldName::CROP_Y);
		$src_w= $this->parameters->get(kThumbFactoryFieldName::SRC_WIDTH);
		$src_h= $this->parameters->get(kThumbFactoryFieldName::SRC_HEIGHT);
		$vid_sec = $this->parameters->get(kThumbFactoryFieldName::VID_SEC);
		$vid_slice= $this->parameters->get(kThumbFactoryFieldName::VID_SLICE);
		$vid_slices= $this->parameters->get(kThumbFactoryFieldName::VID_SLICES);
		$entry_status = $entry->getStatus();

		$this->thumbName = $entry->getId() . "_{$width}_{$height}_{$type}__{$bgColor}_{$quality}_{$src_x}_{$src_y}_{$src_w}_{$src_h}_{$vid_sec}_{$vid_slice}_{$vid_slices}_{$entry_status}";
	}

	protected function calculateThumbNamePostfix()
	{
		if ($this->parameters->get(kThumbFactoryFieldName::ORIG_IMAGE_PATH))
		{
			$this->thumbName .= '_oip_' . basename($this->parameters->get(kThumbFactoryFieldName::ORIG_IMAGE_PATH));
		}

		if ($this->parameters->get(kThumbFactoryFieldName::DENSITY))
		{
			$this->thumbName .= "_dns_{$this->parameters->get(kThumbFactoryFieldName::DENSITY)}";
		}

		if ($this->parameters->get(kThumbFactoryFieldName::STRIP_PROFILES))
		{
			$this->thumbName .= "_stp_{$this->parameters->get(kThumbFactoryFieldName::STRIP_PROFILES)}";
		}

		if ($this->parameters->get(kThumbFactoryFieldName::START_SEC) != kThumbAdapterParameters::UNSET_PARAMETER)
		{
			$this->thumbName .= "_ssec_{$this->parameters->get(kThumbFactoryFieldName::START_SEC)}";
		}

		if ($this->parameters->get(kThumbFactoryFieldName::END_SEC) != kThumbAdapterParameters::UNSET_PARAMETER)
		{
			$this->thumbName .= "_esec_{$this->parameters->get(kThumbFactoryFieldName::END_SEC)}";
		}
	}

	protected function calculateThumbName()
	{
		$this->calculateBaseThumbName();
		$this->calculateThumbNamePostfix();
	}

	protected function getEntryLengthInMS()
	{
		$entry = $this->getEntry();
		return $entry->getLengthInMsecs();
	}

	protected function getEntryThumbFilename()
	{
		return self::DEFAULT_FILE_NAME;
	}

	protected function calculateThumbFileName()
	{
		$entry = $this->getEntry();
		$entryThumbFilename = $entry->getThumbnail();
		if(!$entryThumbFilename)
		{
			$entryThumbFilename = $this->getEntryThumbFilename();
		}

		if ($entry->getStatus() != entryStatus::READY || $entryThumbFilename[0] === self::THUMB_FILE_NO_CACHE_PREFIX)
		{
			$this->thumbName .= self::THUMB_NAME_NO_CACHE_POSTFIX;
		}

		// we remove the & from the template thumb otherwise getGeneralEntityPath will drop $tempThumbName from the final path
		$this->entryThumbFilename = str_replace(self::THUMB_FILE_NO_CACHE_PREFIX, '', $entryThumbFilename);
	}

	protected function calculateThumbPaths($contentPath, $thumbDirs)
	{
		$entry = $this->getEntry();
		$version = $this->parameters->get(kThumbFactoryFieldName::VERSION);
		$format = $this->parameters->get(kThumbFactoryFieldName::IMAGE_FORMAT);
		//create final path for thumbnail created
		$this->finalThumbPath = $contentPath . myContentStorage::getGeneralEntityPath(self::ENTITY_NAME_PREFIX . $thumbDirs[0], $entry->getIntId(), $this->thumbName, $this->entryThumbFilename , $version);
		if($format)
		{
			$this->finalThumbPath = kFile::replaceExt($this->finalThumbPath, $format);
		}
	}

	protected function checkIfOldApiCachedExists($contentPath, $thumbDirs)
	{
		foreach ($thumbDirs as $thumbDir)
		{
			$entry = $this->getEntry();
			$version = $this->parameters->get(kThumbFactoryFieldName::VERSION);
			$currPath = $contentPath . myContentStorage::getGeneralEntityPath(self::ENTITY_NAME_PREFIX . $thumbDir, $entry->getIntId(), $this->thumbName, $this->entryThumbFilename , $version);
			if (file_exists($currPath) && @filesize($currPath))
			{
				if($currPath != $this->finalThumbPath)
				{
					$moveFileSuccess = kFile::moveFile($currPath, $this->finalThumbPath);
					if($moveFileSuccess)
					{
						return array (true, $this->finalThumbPath);
					}
					else
					{
						KalturaLog::warning("Failed to move thumbnail from [$currPath] to [$this->finalThumbPath], will return oldPath");
					}
				}

				return array (true, $currPath);
			}
		}

		return array(false, null);
	}

	/**
	 * @return entry
	 */
	protected function getEntry()
	{
		return $this->parameters->get(kThumbFactoryFieldName::ENTRY);
	}

	/**
	 * @param $params kThumbAdapterParameters
	 * @return string
	 * @throws kThumbnailException
	 */
	public function resizeEntryImage($params)
	{
		$this->parameters = $params;
		$contentPath = myContentStorage::getFSContentRootPath();
		$this->calculateThumbName();
		$this->calculateThumbFileName();
		$thumbDirs = kConf::get(self::CONFIGURATION_PARAM_NAME, self::LOCAL_MAP_NAME, array(self::DEFAULT_THUMB_DIR));
		$this->calculateThumbPaths($contentPath, $thumbDirs);
		if(kFile::checkFileExists($this->finalThumbPath))
		{
			return $this->returnCachedVersion($this->finalThumbPath);
		}

		list($cacheExists, $filePath) = $this->checkIfOldApiCachedExists($contentPath, $thumbDirs);
		if($cacheExists)
		{
			return $this->returnCachedVersion($filePath);
		}

		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_PS2);
		$cacheLockKey = self::LOCK_KEY_PREFIX . $this->finalThumbPath;
		// creating the thumbnail is a very heavy operation prevent calling it in parallel for the same thumbnail for 5 minutes
		if ($cache && !$cache->add($cacheLockKey, true, self::THUMB_PROCESSING_LOCK_DURATION))
		{
			KExternalErrors::dieError(KExternalErrors::PROCESSING_CAPTURE_THUMBNAIL);
		}

		$this->preTransformationExtraActions();
		$adapter = new kImageTransformationAdapter();
		$imageTransformation = $adapter->getImageTransformation($this->parameters);
		try
		{
			$imagick = $imageTransformation->execute();
		}
		catch (Exception $ex)
		{
			if ($cache)
			{
				$cache->delete($cacheLockKey);
			}

			throw $ex;
		}

		kFile::filePutContents($this->finalThumbPath, $imagick);
		if ($cache)
		{
			$cache->delete($cacheLockKey);
		}

		return $this->finalThumbPath;
	}

	protected function initOrigImagePath()
	{
		$entry = $this->getEntry();
		/* @var  $fileSync FileSync*/
		$fileSync = $this->parameters->get(kThumbFactoryFieldName::FILE_SYNC);
		if ($fileSync)
		{
			$orig_image_path = $fileSync->getFullPath();
		}
		else
		{
			$orig_image_path = $this->parameters->get(kThumbFactoryFieldName::ORIG_IMAGE_PATH);
		}

		if ($orig_image_path === null || !kFile::checkFileExists($orig_image_path))
		{
			$fileSync = myEntryUtils::getEntryLocalImageFileSync($entry, $this->parameters->get(kThumbFactoryFieldName::VERSION));
			$orig_image_path = myEntryUtils::getLocalImageFilePathByEntry($entry, $this->parameters->get(kThumbFactoryFieldName::VERSION));
			$this->parameters->set(kThumbFactoryFieldName::ORIG_IMAGE_PATH, $orig_image_path);
			$this->parameters->set(kThumbFactoryFieldName::FILE_SYNC, $fileSync);
		}
		else
		{
			$this->parameters->set(kThumbFactoryFieldName::ORIG_IMAGE_PATH, $orig_image_path);
		}

	}
	protected function preTransformationExtraActions()
	{
		$this->initOrigImagePath();
		$entry = $this->getEntry();
		if(!kFile::checkFileExists($this->parameters->get(kThumbFactoryFieldName::ORIG_IMAGE_PATH)) && $this->parameters->get(kThumbFactoryFieldName::VID_SEC) !== kThumbAdapterParameters::UNSET_PARAMETER &&
			$this->parameters->get(kThumbFactoryFieldName::VID_SLICES) !== kThumbAdapterParameters::UNSET_PARAMETER)
		{
			if ($entry->getStatus() != entryStatus::READY && $entry->getLengthInMsecs() == 0) // when entry is not ready and we don't know its duration
			{
				$calc_vid_sec = ($entry->getPartner() && $entry->getPartner()->getDefThumbOffset()) ? $entry->getPartner()->getDefThumbOffset() : 3;
				$this->parameters->set(kThumbFactoryFieldName::VID_SEC, $calc_vid_sec);
			}
			else
			{
				$this->parameters->set(kThumbFactoryFieldName::VID_SEC, $entry->getBestThumbOffset());
			}
		}
	}

	protected function returnCachedVersion($filePath)
	{
		header(self::CACHED_EXISTS_HEADER . md5($filePath));
		return $filePath;
	}
}
