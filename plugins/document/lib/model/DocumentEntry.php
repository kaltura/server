<?php

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
			
		$flavorAssets = flavorAssetPeer::retrieveByEntryIdAndFlavorParams($this->getId(), $flavorParams->getId());
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
}