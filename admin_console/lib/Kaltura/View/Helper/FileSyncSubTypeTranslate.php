<?php
class Kaltura_View_Helper_FileSyncSubTypeTranslate extends Zend_View_Helper_Abstract
{
	private function entrySubTypeTranslate($objectSubType)
	{
		switch($objectSubType)
		{
			case 1: // FILE_SYNC_ENTRY_SUB_TYPE_DATA = 1;
				return $this->view->translate('KalturaFileSyncObjectType::ENTRY::DATA');
				
			case 2: // FILE_SYNC_ENTRY_SUB_TYPE_DATA_EDIT = 2;
				return $this->view->translate('KalturaFileSyncObjectType::ENTRY::DATA_EDIT');
				
			case 3: // FILE_SYNC_ENTRY_SUB_TYPE_THUMB = 3;
				return $this->view->translate('KalturaFileSyncObjectType::ENTRY::THUMB');
				
			case 4: // FILE_SYNC_ENTRY_SUB_TYPE_ARCHIVE = 4;
				return $this->view->translate('KalturaFileSyncObjectType::ENTRY::ARCHIVE');
				
			case 5: // FILE_SYNC_ENTRY_SUB_TYPE_DOWNLOAD = 5;
				return $this->view->translate('KalturaFileSyncObjectType::ENTRY::DOWNLOAD');
				
			default:
				return $this->view->translate('unknown');
		}
	}
	
	private function uiConfSubTypeTranslate($objectSubType)
	{
		switch($objectSubType)
		{
			case 1: // FILE_SYNC_UICONF_SUB_TYPE_DATA = 1;
				return $this->view->translate('KalturaFileSyncObjectType::UICONF::DATA');
				
			case 2: // FILE_SYNC_UICONF_SUB_TYPE_FEATURES = 2;
				return $this->view->translate('KalturaFileSyncObjectType::UICONF::FEATURES');
				
			default:
				return $this->view->translate('unknown');
		}
	}
	
	private function batchJobSubTypeTranslate($objectSubType)
	{
		switch($objectSubType)
		{
			case 1: // FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOADCSV = 1;
				return $this->view->translate('KalturaFileSyncObjectType::BATCHJOB::BULKUPLOADCSV');
				
			case 2: // FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOADLOG = 2;
				return $this->view->translate('KalturaFileSyncObjectType::BATCHJOB::BULKUPLOADLOG');
				
			default:
				return $this->view->translate('unknown');
		}
	}
	
	private function flavorAssetSubTypeTranslate($objectSubType)
	{
		switch($objectSubType)
		{
			case 1: // FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET = 1;
				return $this->view->translate('KalturaFileSyncObjectType::FLAVOR_ASSET::ASSET');
				
			case 2: // FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_CONVERT_LOG = 2;
				return $this->view->translate('KalturaFileSyncObjectType::FLAVOR_ASSET::CONVERT_LOG');
				
			default:
				return $this->view->translate('unknown');
		}
	}
	
	public function fileSyncSubTypeTranslate($objectType, $objectSubType)
	{
		switch($objectType)
		{
			case KalturaFileSyncObjectType::ENTRY:
				return $this->entrySubTypeTranslate($objectSubType);
				
			case KalturaFileSyncObjectType::UICONF:
				return $this->uiConfSubTypeTranslate($objectSubType);
				
			case KalturaFileSyncObjectType::BATCHJOB:
				return $this->batchJobSubTypeTranslate($objectSubType);
				
			case KalturaFileSyncObjectType::FLAVOR_ASSET:
				return $this->flavorAssetSubTypeTranslate($objectSubType);
				
			default:
				return $this->view->translate('unknown');
		}
	}
}