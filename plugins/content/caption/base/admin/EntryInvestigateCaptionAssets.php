<?php
/**
 * @package plugins.caption
 * @subpackage admin
 */
class Kaltura_View_Helper_EntryInvestigateCaptionAssets extends Kaltura_View_Helper_PartialViewPlugin
{
	private $entryId;
	private $partnerId;
	
	/* (non-PHPdoc)
	 * @see Kaltura_View_Helper_PartialViewPlugin::plug()
	 */
	public function plug(Zend_View_Interface $view)
	{
		$entry = $view->investigateData->entry;
		$this->entryId = $entry->id;
		$this->partnerId = $entry->partnerId;
		parent::plug($view);
	}
	
	/* (non-PHPdoc)
	 * @see Kaltura_View_Helper_PartialViewPlugin::getDataArray()
	 */
	protected function getDataArray()
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
		$filter->entryIdEqual = $this->entryId;
		
		$captionAssets = array();
		$captionAssetsFileSyncs = array();
		$errDescription = null;
		try
		{
			Infra_ClientHelper::impersonate($this->partnerId);
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
	 * @see Kaltura_View_Helper_PartialViewPlugin::getTemplatePath()
	 */
	protected function getTemplatePath()
	{
		return realpath(dirname(__FILE__));
	}
	
	/* (non-PHPdoc)
	 * @see Kaltura_View_Helper_PartialViewPlugin::getPHTML()
	 */
	protected function getPHTML()
	{
		return 'entry-investigate-caption-assets.phtml';
	}
}