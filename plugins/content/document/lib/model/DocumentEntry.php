<?php
/**
 * @package plugins.document
 * @subpackage model
 */
class DocumentEntry extends entry
{
	/**
	 * 
	 * @param $version
	 * @param $format
	 * @return FileSync
	 */
	public function getDownloadFileSyncAndLocal ( $version = NULL , $format = null , $sub_type = null )
	{
		$flavorParams = myConversionProfileUtils::getFlavorParamsFromFileFormat($this->getPartnerId(), $format);
		if(!$flavorParams)
			return null;
			
		$flavorAssets = assetPeer::retrieveByEntryIdAndParams($this->getId(), $flavorParams->getId());
		if(!$flavorAssets)
			return null;
			
		$syncKey = $flavorAssets->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		if(!$syncKey)
			return null;
			
		return kFileSyncUtils::getReadyFileSyncForKey($syncKey, true, false);
	}

	/* (non-PHPdoc)
	 * @see lib/model/entry#getDownloadUrl()
	 */
	//TODO: cannot be used yet for backward compatibility
	/*
	public function getDownloadUrl( $version = NULL )
	{
		$host = myPartnerUtils::getCdnHost($this->getPartnerId());
		$service = 'document_documents';
		$action = 'serveByFlavorParamsId';
		$entryId = $this->getId();

		return "$host/api_v3/index.php?service=$service&action=$action&entryId=$entryId";
		//	http://www.kaltura.com/api_v3/index.php?service=document_documents&action=serveByFlavorParamsId&entryId=...
	}
	*/
	
	public function getDocumentType()
	{
		return $this->getMediaType();
	}
	
	public function setDocumentType($v)
	{
		return $this->setMediaType($v);
	}
	
	/**
	 * This function returns the file system path for a requested content entity.
	 * this function is here to support PS2 getEntry and 2Tor which uses datapath
	 * @return string the content path
	 */
	public function getDataPath( $version = NULL )
	{
		$url = $this->getDownloadUrl($version);
		// this is to make sure direct embeds (formerly done by going to /content/entry/data/../file.swf)
		// will still work
		$url .= '/direct_serve/1/forceproxy/true';
		$host = myPartnerUtils::getCdnHost($this->getPartnerId());
		$url = str_replace($host, '', $url);
		
		return $url;
	}

	public function getCreateThumb (  )			{	return false;} // Documents never have a thumb
	
	public function getLocalThumbFilePath($version , $width , $height , $type , $bgcolor ="ffffff" , $crop_provider=null, $quality = 0,
		$src_x = 0, $src_y = 0, $src_w = 0, $src_h = 0, $vid_sec = -1, $vid_slice = 0, $vid_slices = -1, $density = 0, $stripProfiles = false, $flavorId = null, $fileName = null) {
		KalturaLog::log ( "flavor_id [$flavorId] file_name [$fileName]" );
		if (is_null ( $flavorId ))
			KExternalErrors::dieError ( KExternalErrors::MISSING_PARAMETER, 'flavor_id' );
		$flavor = assetPeer::retrieveById ( $flavorId );
		if (is_null ( $flavor ))
			KExternalErrors::dieError ( KExternalErrors::FLAVOR_NOT_FOUND, $flavorId );
		$flavorSyncKey = $flavor->getSyncKey ( asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET );
		$file_path = kFileSyncUtils::getReadyLocalFilePathForKey ( $flavorSyncKey );
		$orig_image_path = null;
		if (is_dir($file_path)){
			if (is_null($fileName))
				 KExternalErrors::dieError ( KExternalErrors::MISSING_PARAMETER, 'file name' );
			$orig_image_path = $file_path . DIRECTORY_SEPARATOR . $fileName;
		}
		try 
		{
			return myEntryUtils::resizeEntryImage($this, $version, $width, $height, $type, $bgcolor, $crop_provider, $quality, $src_x, $src_y, $src_w, $src_h, $vid_sec, $vid_slice, $vid_slices, $orig_image_path, $density);
		} 
		catch ( Exception $ex ) 
		{
			if ($ex->getCode () == kFileSyncException::FILE_DOES_NOT_EXIST_ON_CURRENT_DC) 
			{
				$remoteFileSync = kFileSyncUtils::getOriginFileSyncForKey ( $flavorSyncKey, false );
				if (! $remoteFileSync) 
				{
					// file does not exist on any DC - die
					KalturaLog::err ( "No FileSync for flavor [$flavorId]" );
					KExternalErrors::dieError ( KExternalErrors::FILE_NOT_FOUND );
				}
				
				if ($remoteFileSync->getDc () == kDataCenterMgr::getCurrentDcId ()) 
				{
					KalturaLog::err ( "Trying to redirect to myself - stop here." );
					KExternalErrors::dieError ( KExternalErrors::FILE_NOT_FOUND );
				}
				
				if (! in_array ( $remoteFileSync->getDc (), kDataCenterMgr::getDcIds () )) 
				{
					KalturaLog::err ( "Origin file sync is on remote storage." );
					KExternalErrors::dieError ( KExternalErrors::FILE_NOT_FOUND );
				}
				$remoteUrl = kDataCenterMgr::getRedirectExternalUrl ( $remoteFileSync );
				kFileUtils::dumpUrl ( $remoteUrl );
			}
		}			
	}
}