<?php


class AttachmentSearchPlugin extends KalturaPlugin implements IKalturaPending, IKalturaPermissions, IKalturaServices, IKalturaElasticSearchDataContributor
{
	const PLUGIN_NAME = 'attachmentSearch';
	const MAX_ATTACHMENT_FILE_SIZE_FOR_INDEXING = 900000;
	const MARKDOWN_ASSET = 'MarkdownAsset';
	const ATTACHMENT_ASSET = 'AttachmentAsset';

	public static function getElasticSearchData(BaseObject $object)
	{
		if ($object instanceof entry && self::isAllowedPartner($object->getPartnerId()))
		{
			return self::getAttachmentElasticSearchData($object);
		}

		return null;
	}


	public static function getAttachmentElasticSearchData($entry): array
	{
		$attachmentAssets = assetPeer::retrieveByEntryId($entry->getId(),
			array(
				AttachmentPlugin::getAssetTypeCoreValue(AttachmentAssetType::ATTACHMENT),
				MarkdownPlugin::getAssetTypeCoreValue(MarkdownAssetType::MARKDOWN)
			),
			array(asset::ASSET_STATUS_READY, asset::ASSET_STATUS_EXPORTING));


		if (!$attachmentAssets || !count($attachmentAssets))
		{
			return array();
		}

		$data = array();
		$attachmentData = array();
		foreach($attachmentAssets as $attachmentAsset)
		{
			// Only index MarkdownAssets with KAI provider type during initial rollout.
			if (!($attachmentAsset instanceof MarkdownAsset) ||
				($attachmentAsset instanceof MarkdownAsset && (int)$attachmentAsset->getProviderType() !== MarkdownProviderType::KAI))
			{
				KalturaLog::err("Skipping Elastic index. Provider type [" . $attachmentAsset->getProviderType() . "] on asset id " . $attachmentAsset->getId());
				continue;
			}


			$syncKey = $attachmentAsset->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
			$content = kFileSyncUtils::file_get_contents($syncKey, true, false, self::MAX_ATTACHMENT_FILE_SIZE_FOR_INDEXING);
			if (!$content)
			{
				continue;
			}

			$attachmentContentManager = kAttachmentContentManager::getCoreContentManager($attachmentAsset->getContainerFormat());
			if (!$attachmentContentManager)
			{
				KalturaLog::err("Attachment content manager not found for format [" . $attachmentAsset->getContainerFormat() . "]");
				continue;
			}

			$items = $attachmentContentManager->parse($content);
			if (!$items)
			{
				continue;
			}

			$accuracy = self::getAssetAccuracy($attachmentAsset);
			$assetName = $attachmentAsset->getFilename();

			self::getElasticContent($attachmentData,
				$items,
				$attachmentAsset->getId(),
				$assetName,
				$attachmentAsset->getType(),
				$attachmentAsset->getContainerFormat(),
				$attachmentAsset->getTags(),
				$accuracy
			);
		}

		$data['attachment_assets'] = $attachmentData;
		return $data;
	}

	private static function getAssetAccuracy($attachmentAsset)
	{
		switch (get_class($attachmentAsset)) {
			case self::MARKDOWN_ASSET:
				return $attachmentAsset->getAccuracy();

			case self::ATTACHMENT_ASSET:
			default:
				return null;
		}
	}

	protected static function getElasticContent(&$attachmentData, $items, $assetId, $assetName, $assetType, $assetSubType, $tags = null, $accuracy = null)
	{
		$pageNumber = 1;
		foreach ($items as $item)
		{
			$page = array(
				'attachment_asset_id' => $assetId,
				'file_name' => $assetName,
				'asset_type' => $assetType,
				'asset_sub_type' => $assetSubType,
			);

			if ($tags)
			{
				$page['tags'] = $tags;
			}

			if ($accuracy)
			{
				$page['accuracy'] = $accuracy;
			}

			$content = '';
			foreach ($item['content'] as $curChunk)
			{
				$content .= $curChunk['text'];
			}

			$content = kString::stripUtf8InvalidChars($content);

			if (strlen($content) > kElasticSearchManager::MAX_LENGTH) {
				$chunks = str_split($content, kElasticSearchManager::MAX_LENGTH);
			}
			else
			{
				$chunks = [$content];
			}

			foreach ($chunks as $chunk)
			{
				$page['page_number'] = $pageNumber;
				$page['content'] = $chunk;
				$attachmentData[] = $page;
				$pageNumber++;
			}
		}
	}


	public static function dependsOn()
	{
		$attachmentDependency = new KalturaDependency(AttachmentPlugin::getPluginName());
		$markdownDependency = new KalturaDependency(MarkdownPlugin::getPluginName());
		return array($attachmentDependency, $markdownDependency);
	}

	public static function isAllowedPartner($partnerId)
	{
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if(!$partner)
		{
			return false;
		}

		return $partner->getPluginEnabled(self::PLUGIN_NAME);
	}

	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}


	public static function getServicesMap()
	{
		return array();
	}
}
