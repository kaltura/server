<?php
/**
 * @package plugins.attachment
 * @subpackage admin
 */
class Kaltura_View_Helper_EntryInvestigateAttachmentAssets extends Kaltura_View_Helper_PartialViewPlugin
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
		
		$attachmentPlugin = Kaltura_Client_Attachment_Plugin::get($client);
		$fileSyncPlugin = Kaltura_Client_FileSync_Plugin::get($client);
		
		$filter = new Kaltura_Client_Attachment_Type_AttachmentAssetFilter();
		$filter->entryIdEqual = $this->entryId;
		
		$attachmentAssets = array();
		$attachmentAssetsFileSyncs = array();
		$errDescription = null;
		try
		{
			Infra_ClientHelper::impersonate($this->partnerId);
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
		return 'entry-investigate-attachment-assets.phtml';
	}
}