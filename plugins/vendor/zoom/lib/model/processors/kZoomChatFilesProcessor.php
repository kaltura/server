<?php
/**
 * @package plugins.vendor
 * @subpackage zoom.model
 */

class kZoomChatFilesProcessor extends kZoomProcessor
{
	const ZOOM_CHAT_FILE_TYPE = 'txt';

	/**
	 * @param string $recordingId
	 * @param entry $entry
	 * @return AttachmentAsset
	 * @throws PropelException
	 */
	protected function createAttachmentAssetForChatFile($recordingId, $entry)
	{
		$attachment = new AttachmentAsset();
		$attachment->setFilename("Recording {$recordingId} chat file." . self::ZOOM_CHAT_FILE_TYPE);
		$attachment->setPartnerId($entry->getPartnerId());
		$attachment->setEntryId($entry->getId());
		$attachment->setcontainerFormat(AttachmentType::TEXT);
		$attachment->setFileExt(self::ZOOM_CHAT_FILE_TYPE);
		$attachment->save();
		return $attachment;
	}

	/**
	 * @param entry $entry
	 * @param kZoomRecording $recording
	 * @param string $chatDownloadUrl
	 * @param string $downloadToken
	 * @param kuser $dbUser
	 */
	public function handleChatRecord($entry, $recording, $chatDownloadUrl, $downloadToken, $dbUser)
	{
		if(!$entry)
		{
			ZoomHelper::exitWithError(kZoomErrorMessages::MISSING_ENTRY_FOR_CHAT);
		}
		try
		{
			$attachmentAsset = $this->createAttachmentAssetForChatFile($recording->id, $entry);
			$attachmentAssetResource = new KalturaUrlResource();
			$attachmentAssetResource->url = $chatDownloadUrl . self::URL_ACCESS_TOKEN . $downloadToken;
			$this->initUserPermissions($dbUser, true);
			$attachmentAssetService = new AttachmentAssetService();
			$attachmentAssetService->initService('attachment_attachmentasset', 'attachmentAsset', 'setContent');
			$attachmentAssetService->setContentAction($attachmentAsset->getId(), $attachmentAssetResource);
		}
		catch (Exception $e)
		{
			ZoomHelper::exitWithError(kZoomErrorMessages::ERROR_HANDLING_CHAT);
		}
	}
}