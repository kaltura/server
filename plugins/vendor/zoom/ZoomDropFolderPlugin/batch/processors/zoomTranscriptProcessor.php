<?php
/**
 * @package plugins.ZoomDropFolder
 */

class zoomTranscriptProcessor extends zoomProcessor
{
	const ZOOM_TRANSCRIPT_FILE_EXT = 'vtt';
	const ZOOM_LABEL = 'Zoom';

	/**
	 * @param KalturaZoomDropFolderFile $recording
	 * @throws KalturaAPIException
	 * @throws kCoreException
	 * @throws PropelException
	 */
	public function handleRecordingTranscriptComplete($recording)
	{
		$entry = $this->getZoomEntryByRecordingId($recording->meetingMetadata->uuid, $recording->partnerId);
		if (!$entry)
		{
			throw new KalturaAPIException(KalturaZoomErrorMessages::MISSING_ENTRY_FOR_ZOOM_RECORDING . $recording->meetingMetadata->uuid);
		}

		if ($this->isTranscriptionAlreadyHandled($entry))
		{
			KalturaLog::debug("Zoom transcription for entry {$entry->getId()} was already handled");
			return;
		}

		$resourceReservation = new kResourceReservation(self::ZOOM_LOCK_TTL, true);
		if ($recording->recordingFile->fileType == KalturaRecordingFileType::TRANSCRIPT)
		{
			if (!$resourceReservation->reserve($recording->meetingMetadata->meetingId))
			{
				return;
			}
			
			try
			{
				$captionPlugin = KalturaPluginManager::getPluginInstance(CaptionPlugin::PLUGIN_NAME);
				infra_ClientHelper::impersonate($entry->partnerId);
				$captionAsset = $this->createAssetForTranscription($entry, $captionPlugin);
				$captionAssetResource = new KalturaUrlResource();
				$captionAssetResource->url = $recording->recordingFile->downloadUrl . self::URL_ACCESS_TOKEN . $this->dropFolder->accessToken;
				$captionPlugin->captionAsset->setContent($captionAsset->id, $captionAssetResource);
				infra_ClientHelper::unimpersonate();
			}
			catch (Exception $e)
			{
				throw new KalturaAPIException(KalturaZoomErrorMessages::ERROR_HANDLING_TRANSCRIPT);
			}
		}

	}

	/**
	 * @param KalturaMediaEntry $entry
	 * @param $captionPlugin
	 * @return KalturaCaptionAsset
	 * @throws PropelException
	 */
	protected function createAssetForTranscription($entry, $captionPlugin)
	{
		$newCaptionAsset = new KalturaCaptionAsset();
		$newCaptionAsset->entryId = $entry->id;
		$newCaptionAsset->partnerId = $entry->partnerId;
		$newCaptionAsset->language = KalturaLanguage::EN;
		$newCaptionAsset->label = self::ZOOM_LABEL;
		$newCaptionAsset->format = KalturaCaptionType::WEBVTT;
		$newCaptionAsset->status = KalturaCaptionAssetStatus::QUEUED;
		$newCaptionAsset->fileExt = self::ZOOM_TRANSCRIPT_FILE_EXT;
		$newCaptionAsset->source = CaptionSource::ZOOM; //todo need to add source to KalturaCaptionAsset
		
		$caption = $captionPlugin->captionAsset->add($entry->id, $newCaptionAsset);
		return $caption;
	}

	/**
	 * @param KalturaMediaEntry $entry
	 * @return bool
	 * @throws KalturaAPIException
	 */
	protected function isTranscriptionAlreadyHandled($entry)
	{
		$result = false;
		
		$filter = new KalturaAssetFilter();
		$filter->entryIdEqual = $entry->id;
		$captionPlugin = KalturaPluginManager::getPluginInstance(CaptionPlugin::PLUGIN_NAME);
		infra_ClientHelper::impersonate($entry->partnerId);
		$list = $captionPlugin->captionAsset->listAction($filter);
		infra_ClientHelper::unimpersonate();
		
		foreach($list->objects as $captionAsset)
		{
			if($captionAsset->source == CaptionSource::ZOOM) //todo need to add source to KalturaCaptionAsset
			{
				$result = true;
				break;
			}
		}

		return $result;
	}
}