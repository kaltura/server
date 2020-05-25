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
	const MAP_NAME = 'local';
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

	protected $processingThumbPath;

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

	/**
	 * @return string
	 */
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
		$format = $this->parameters->get(kThumbFactoryFieldName::VERSION);

		//create final path for thumbnail created
		$this->finalThumbPath = $contentPath . myContentStorage::getGeneralEntityPath(self::ENTITY_NAME_PREFIX . $thumbDirs[0], $entry->getIntId(), $this->thumbName, $this->entryThumbFilename , $version );

		//Add unique id to the processing file path to avoid file being overwritten when several identical (with same parameters) calls are made before the final thumbnail is created
		$uniqueThumbName = $this->thumbName . '_' . uniqid() . '_';

		//create path for processing thumbnail request
		$this->processingThumbPath = $contentPath . myContentStorage::getGeneralEntityPath(self::ENTITY_NAME_PREFIX . $thumbDirs[0], $entry->getIntId(), $uniqueThumbName, $this->entryThumbFilename , $version );

		if($format)
		{
			$this->finalThumbPath = kFile::replaceExt($this->finalThumbPath, $format);
			$this->processingThumbPath = kFile::replaceExt($this->processingThumbPath, $format);
		}
	}

	protected function checkIfOldApiCachedExists($contentPath, $thumbDirs)
	{
		foreach ($thumbDirs as $thumbDir)
		{
			$entry = $this->getEntry();
			$currPath = $contentPath . myContentStorage::getGeneralEntityPath(self::ENTITY_NAME_PREFIX . $thumbDir, $entry->getIntId(), $this->thumbName, $this->entryThumbFilename , $this->version );
			if (file_exists($currPath) && @filesize($currPath))
			{
				if($currPath != $this->finalThumbPath)
				{
					$moveFileSuccess = kFile::moveFile($currPath, $this->finalThumbPath);
					if(!$moveFileSuccess)
					{
						KalturaLog::debug("Failed to move thumbnail from [$currPath] to [$this->finalThumbPath], will return oldPath");
						header(self::CACHED_EXISTS_HEADER . md5($currPath));
						return array(true, $currPath);
					}
				}

				header(self::CACHED_EXISTS_HEADER . md5($this->finalThumbPath));
				return array (true, $this->finalThumbPath);
			}
		}

		return array(false);
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
		$thumbDirs = kConf::get(self::CONFIGURATION_PARAM_NAME, self::MAP_NAME, array(self::DEFAULT_THUMB_DIR));
		$this->calculateThumbPaths($contentPath, $thumbDirs);
		list($cacheExists, $filePath) = $this->checkIfOldApiCachedExists($contentPath, $thumbDirs);
		if($cacheExists)
		{
			return $filePath;
		}

		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_PS2);
		$cacheLockKey = self::LOCK_KEY_PREFIX . $this->finalThumbPath;
		// creating the thumbnail is a very heavy operation prevent calling it in parallel for the same thumbnail for 5 minutes
		if ($cache && !$cache->add($cacheLockKey, true, self::THUMB_PROCESSING_LOCK_DURATION))
		{
			KExternalErrors::dieError(KExternalErrors::PROCESSING_CAPTURE_THUMBNAIL);
		}

		$flavorAssetId = null;
		$entry = $this->getEntry();
		if ($entry->getType() == entryType::PLAYLIST)
		{
			myPlaylistUtils::updatePlaylistStatistics($entry->getPartnerId(), $entry);
		}

		$adapter = new kImageTransformationAdapter();
		$imageTransformation = $adapter->getImageTransformation($this->parameters);
		$storage = kThumbStorageBase::getInstance();
		if(!$storage->loadFileIntoPath($this->thumbName, $imageTransformation->getLastModified(), $this->finalThumbPath))
		{
			$imagick = $imageTransformation->execute();
			$storage->saveFile($this->thumbName, $imagick, $imageTransformation->getLastModified());
			$storage->loadFileIntoPath($this->thumbName, $imageTransformation->getLastModified(), $this->finalThumbPath);
		}

		if ($cache)
		{
			$cache->delete($cacheLockKey);
		}

		return $this->finalThumbPath;
	}

}
