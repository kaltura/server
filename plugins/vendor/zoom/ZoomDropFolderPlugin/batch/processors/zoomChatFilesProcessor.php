<?php
/**
 * @package plugins.ZoomDropFolder
 */

class zoomChatFilesProcessor extends zoomProcessor
{
	const ZOOM_CHAT_FILE_TYPE = 'txt';
	
	/**
	 * @param string $recordingId
	 * @param kalturaMediaEntry $entry
	 * @param $attachmentPlugin
	 * @return KalturaAttachmentAsset
	 * @throws PropelException
	 */
	protected function createAttachmentAssetForChatFile($recordingId, $entry, $attachmentPlugin)
	{
		$attachmentAsset = new KalturaAttachmentAsset();
		$attachmentAsset->filename = "Recording {$recordingId} chat file." . self::ZOOM_CHAT_FILE_TYPE;
		$attachmentAsset->format = KalturaAttachmentType::TEXT;
		$attachmentAsset->fileExt = self::ZOOM_CHAT_FILE_TYPE;
		try
		{
			$attachmentAsset = $attachmentPlugin->attachmentAsset->add($entry->id, $attachmentAsset);
		}
		catch (Exception $e)
		{
			KalturaLog::debug($e);
			throw new Exception(KalturaZoomErrorMessages::ERROR_HANDLING_CHAT);
		}
		return $attachmentAsset;
	}
	
	/**
	 * @param kalturaMediaEntry $entry
	 * @param KalturaZoomDropFolderFile $recording
	 * @param string $chatDownloadUrl
	 */
	public function handleChatRecord($entry, $recording, $chatDownloadUrl)
	{
		if(!$entry)
		{
			throw new Exception(KalturaZoomErrorMessages::MISSING_ENTRY_FOR_CHAT);
		}
		
		$attachmentPlugin = KalturaAttachmentClientPlugin::get(KBatchBase::$kClient);
		KBatchBase::impersonate($entry->partnerId);
		$attachmentAsset = $this->createAttachmentAssetForChatFile($recording->meetingMetadata->meetingId, $entry, $attachmentPlugin);
		$attachmentAssetResource = new KalturaUrlResource();
		$attachmentAssetResource->url = $this->getRedirectUrl($recording);
		try
		{
			$attachmentPlugin->attachmentAsset->setContent($attachmentAsset->id, $attachmentAssetResource);
		}
		catch (Exception $e)
		{
			KalturaLog::debug($e);
			throw new Exception(KalturaZoomErrorMessages::ERROR_HANDLING_CHAT);
		}
		KBatchBase::unimpersonate();
	}
}