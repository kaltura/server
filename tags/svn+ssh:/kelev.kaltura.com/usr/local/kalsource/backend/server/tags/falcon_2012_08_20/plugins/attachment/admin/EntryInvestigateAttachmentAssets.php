<?php
/**
 * @package plugins.attachment
 * @subpackage admin
 */
class Kaltura_View_Helper_EntryInvestigateAttachmentAssets extends Kaltura_View_Helper_EntryInvestigatePlugin
{
	/* (non-PHPdoc)
	 * @see Kaltura_View_Helper_EntryInvestigatePlugin::getDataArray()
	 */
	public function getDataArray($entryId, $partnerId)
	{
		$client = Infra_ClientHelper::getClient();
		if(!$client)
		{
			$errors[] = 'init client failed';
			return;
		}
		
		$attachmentPlugin = Kaltura_Client_Attachment_Plugin::get($client);
		$fileSyncPlugin = Kaltura_Client_FileSync_Plugin::get($client);
		
		$filter = new Kaltura_Client_Attachment_Type_AttachmentAssetFilter();
		$filter->entryIdEqual = $entryId;
		
		$attachmentAssets = array();
		$attachmentAssetsFileSyncs = array();
		$errDescription = null;
		try
		{
			Infra_ClientHelper::impersonate($partnerId);
			$attachmentAssetsList = $attachmentPlugin->attachmentAsset->listAction($filter);
			Infra_ClientHelper::unimpersonate();
			$attachmentAssets = $attachmentAssetsList->objects;
		}
		catch (Exception $e)
		{
			$errDescription = $e->getMessage();
		}
		
		$attachmentAssetIds = array();
		if(is_array($attachmentAssets))
		{
			foreach($attachmentAssets as $attachmentAsset)
			{
				$attachmentAssetsFileSyncs[$attachmentAsset->id] = array();
				$attachmentAssetIds[] = $attachmentAsset->id;
			}
		}
	
		if(count($attachmentAssetIds))
		{
			try
			{
				$filter = new Kaltura_Client_FileSync_Type_FileSyncFilter();
				$filter->fileObjectTypeEqual = Kaltura_Client_Enum_FileSyncObjectType::FLAVOR_ASSET;
				$filter->objectIdIn = implode(',', $attachmentAssetIds);
				
				$pager = new Kaltura_Client_Type_FilterPager();
				$pager->pageSize = 100;
				
				$fileSyncList = $fileSyncPlugin->fileSync->listAction($filter, $pager);
				$fileSyncs = $fileSyncList->objects;
				foreach($fileSyncs as $fileSync)
					$attachmentAssetsFileSyncs[$fileSync->objectId][] = $fileSync;			
			}
			catch (Exception $e)
			{
				$errDescription = $e->getMessage();
			}
		}
		
		return array(
			'attachmentAssets' => $attachmentAssets,
			'attachmentAssetsFileSyncs' => $attachmentAssetsFileSyncs,
			'errDescription' => $errDescription,
		);
	}
	
	/* (non-PHPdoc)
	 * @see Kaltura_View_Helper_EntryInvestigatePlugin::getTemplatePath()
	 */
	public function getTemplatePath()
	{
		return realpath(dirname(__FILE__));
	}
	
	/* (non-PHPdoc)
	 * @see Kaltura_View_Helper_EntryInvestigatePlugin::getPHTML()
	 */
	public function getPHTML()
	{
		return 'entry-investigate-attachment-assets.phtml';
	}
}