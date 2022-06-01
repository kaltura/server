<?php
/**
 * @package plugins.ZoomDropFolder
 */

class zoomTranscriptProcessor extends zoomProcessor
{
	const ZOOM_TRANSCRIPT_FILE_EXT = 'vtt';
	const ZOOM_LABEL = 'Zoom';
	const LABEL_DEL = '_';

	/**
	 * @param KalturaZoomDropFolderFile $recording
	 * @param KalturaMediaEntry $entry
	 * @throws KalturaAPIException
	 * @throws kCoreException
	 * @throws PropelException
	 */
	public function handleRecordingTranscriptComplete($recording, $entry)
	{
		$transcriptType = $this->getTranscriptType($recording->recordingFile->fileType);
		$captionPlugin = KalturaCaptionClientPlugin::get(KBatchBase::$kClient);
		if ($this->isTranscriptionAlreadyHandled($entry, $captionPlugin, $transcriptType))
		{
			KalturaLog::debug("Zoom transcription for entry {$entry->id} was already handled");
			return;
		}

		try
		{
			KBatchBase::impersonate($entry->partnerId);
			$captionAsset = $this->createAssetForTranscription($entry, $captionPlugin, $recording);
			$captionAssetResource = new KalturaUrlResource();
			$redirectUrl = $this->getZoomRedirectUrlFromFile($recording);
			$captionAssetResource->url = $redirectUrl;
			$captionPlugin->captionAsset->setContent($captionAsset->id, $captionAssetResource);
			KBatchBase::unimpersonate();
		}
		catch (Exception $e)
		{
			throw new Exception(KalturaZoomErrorMessages::ERROR_HANDLING_TRANSCRIPT);
		}
	}

	/**
	 * @param KalturaMediaEntry $entry
	 * @param $captionPlugin
	 * @return KalturaCaptionAsset
	 * @throws PropelException
	 */
	protected function createAssetForTranscription($entry, $captionPlugin, $recording)
	{
		$newCaptionAsset = new KalturaCaptionAsset();
		$newCaptionAsset->language = KalturaLanguage::EN;
		$newCaptionAsset->label = self::ZOOM_LABEL;
		$transcriptType = $this->getTranscriptType($recording->recordingFile->fileType);
		if($transcriptType != '')
		{
			$newCaptionAsset->label = self::ZOOM_LABEL . self::LABEL_DEL . $transcriptType;
		}
		$recordingFileExt = $recording->recordingFile->fileExtension;
		$transcriptFormat = CaptionPlugin::getCaptionFormatFromExtension($recordingFileExt);
		$newCaptionAsset->format = $transcriptFormat;
		$newCaptionAsset->fileExt = $recordingFileExt;
		$newCaptionAsset->source = KalturaCaptionSource::ZOOM;
		$caption = $captionPlugin->captionAsset->add($entry->id, $newCaptionAsset);
		return $caption;
	}

	/**
	 * @param KalturaMediaEntry $entry
	 * @param $captionPlugin
	 * @return bool
	 * @throws KalturaAPIException
	 */
	protected function isTranscriptionAlreadyHandled($entry, $captionPlugin, $transcriptType)
	{
		$result = false;
		$filter = new KalturaAssetFilter();
		$filter->entryIdEqual = $entry->id;
		KBatchBase::impersonate($entry->partnerId);
		$list = $captionPlugin->captionAsset->listAction($filter);
		KBatchBase::unimpersonate();
		
		foreach($list->objects as $captionAsset)
		{
			if($captionAsset->source == KalturaCaptionSource::ZOOM &&
				($captionAsset->label == self::ZOOM_LABEL . self::LABEL_DEL . $transcriptType || $captionAsset->label == self::ZOOM_LABEL))
			{
				$result = true;
				break;
			}
		}

		return $result;
	}

	protected function getTranscriptType($enumFileType)
	{
		switch($enumFileType)
		{
			case kRecordingFileType::TRANSCRIPT:
				return 'TRANSCRIPT';
			case kRecordingFileType::CC:
				return 'CC';
			default:
				return '';
		}
	}
}