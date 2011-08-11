<?php
/**
 * @package plugins.caption
 * @subpackage admin
 */
class Kaltura_View_Helper_EntryInvestigateCaptionAssets extends Kaltura_View_Helper_EntryInvestigatePlugin
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
		
		$captionPlugin = Kaltura_Client_Caption_Plugin::get($client);
		$fileSyncPlugin = Kaltura_Client_FileSync_Plugin::get($client);
		
		$filter = new Kaltura_Client_Caption_Type_CaptionAssetFilter();
		$filter->entryIdEqual = $entryId;
		
		$captionAssets = array();
		$captionAssetsFileSyncs = array();
		$errDescription = null;
		try
		{
			Infra_ClientHelper::impersonate($partnerId);
			$captionAssetsList = $captionPlugin->captionAsset->listAction($filter);
			Infra_ClientHelper::unimpersonate();
			$captionAssets = $captionAssetsList->objects;
		}
		catch (Exception $e)
		{
			$errDescription = $e->getMessage();
		}
		
		$captionAssetIds = array();
		if(is_array($captionAssets))
		{
			foreach($captionAssets as $captionAsset)
			{
				$captionAssetsFileSyncs[$captionAsset->id] = array();
				$captionAssetIds[] = $captionAsset->id;
			}
		}
	
		if(count($captionAssetIds))
		{
			try
			{
				$filter = new Kaltura_Client_FileSync_Type_FileSyncFilter();
				$filter->fileObjectTypeEqual = Kaltura_Client_Enum_FileSyncObjectType::FLAVOR_ASSET;
				$filter->objectIdIn = implode(',', $captionAssetIds);
				
				$pager = new Kaltura_Client_Type_FilterPager();
				$pager->pageSize = 100;
				
				$fileSyncList = $fileSyncPlugin->fileSync->listAction($filter, $pager);
				$fileSyncs = $fileSyncList->objects;
				foreach($fileSyncs as $fileSync)
					$captionAssetsFileSyncs[$fileSync->objectId][] = $fileSync;			
			}
			catch (Exception $e)
			{
				$errDescription = $e->getMessage();
			}
		}
		
		return array(
			'captionAssets' => $captionAssets,
			'captionAssetsFileSyncs' => $captionAssetsFileSyncs,
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
		return 'entry-investigate-caption-assets.phtml';
	}
}