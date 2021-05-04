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
	 * @param KalturaMediaEntry $entry
	 * @throws KalturaAPIException
	 * @throws kCoreException
	 * @throws PropelException
	 */
	public function handleRecordingTranscriptComplete($recording, $entry)
	{
		$captionPlugin = KalturaCaptionClientPlugin::get(KBatchBase::$kClient);
		if ($this->isTranscriptionAlreadyHandled($entry, $captionPlugin))
		{
			KalturaLog::debug("Zoom transcription for entry {$entry->id} was already handled");
			return;
		}

		if ($recording->recordingFile->fileType == KalturaRecordingFileType::TRANSCRIPT)
		{
			try
			{
				KBatchBase::impersonate($entry->partnerId);
				$captionAsset = $this->createAssetForTranscription($entry, $captionPlugin);
				$captionAssetResource = new KalturaUrlResource();
				$redirectUrl = $this->getRedirectUrl($recording);
				$captionAssetResource->url = $redirectUrl;
				$captionPlugin->captionAsset->setContent($captionAsset->id, $captionAssetResource);
				KBatchBase::unimpersonate();
			}
			catch (Exception $e)
			{
				throw new Exception(KalturaZoomErrorMessages::ERROR_HANDLING_TRANSCRIPT);
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
		$newCaptionAsset->language = KalturaLanguage::EN;
		$newCaptionAsset->label = self::ZOOM_LABEL;
		$newCaptionAsset->format = KalturaCaptionType::WEBVTT;
		$newCaptionAsset->fileExt = self::ZOOM_TRANSCRIPT_FILE_EXT;
		$newCaptionAsset->source = KalturaCaptionSource::ZOOM;
		$newCaptionAsset->accuracy = KBatchBase::$taskConfig->params->accuracy;
		
		$caption = $captionPlugin->captionAsset->add($entry->id, $newCaptionAsset);
		return $caption;
	}

	/**
	 * @param KalturaMediaEntry $entry
	 * @param $captionPlugin
	 * @return bool
	 * @throws KalturaAPIException
	 */
	protected function isTranscriptionAlreadyHandled($entry, $captionPlugin)
	{
		$result = false;
		
		$filter = new KalturaAssetFilter();
		$filter->entryIdEqual = $entry->id;
		KBatchBase::impersonate($entry->partnerId);
		$list = $captionPlugin->captionAsset->listAction($filter);
		KBatchBase::unimpersonate();
		
		foreach($list->objects as $captionAsset)
		{
			if($captionAsset->source == KalturaCaptionSource::ZOOM)
			{
				$result = true;
				break;
			}
		}

		return $result;
	}
}