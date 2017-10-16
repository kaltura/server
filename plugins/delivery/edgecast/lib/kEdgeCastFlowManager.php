<?php
/**
 * @package plugins.edgeCast
 * @subpackage lib
 */
class kEdgeCastFlowManager implements kObjectDeletedEventConsumer
{
    const EDGE_SERVICE_HTTP_LARGE_OBJECT_MEDIA_TYPE = '3';
    const EDGE_SERVICE_HTTP_SMALL_OBJECT_MEDIA_TYPE = '8';
    const EDGE_CAST_API_URL_BASE = 'https://api.edgecast.com/v2/mcc/customers/';
    const EDGE_CAST_API_URL_PURGE = '/edge/purge';
    const EDGE_CAST_API_AUTHORIZATION_TOK_HEADER = 'Authorization: TOK:';
    
    
	/**
	 * @param BaseObject $object
	 * @return bool true if the consumer should handle the event
	 */
	public function shouldConsumeDeletedEvent(BaseObject $object)
	{
	    if (($object instanceof asset) && EdgeCastPlugin::isAllowedPartner($object->getPartnerId()))
	    {
	         return true;
	    }
	    
	    if (($object instanceof entry) && EdgeCastPlugin::isAllowedPartner($object->getPartnerId()))
	    {
	         return true;
	    }
	    
	    return false;
	}
    
	
    /**
	 * @param BaseObject $object
	 * @param BatchJob $raisedJob
	 * @return bool true if should continue to the next consumer
	 */
	public function objectDeleted(BaseObject $object, BatchJob $raisedJob = null)
	{
	    if (($object instanceof asset) && EdgeCastPlugin::isAllowedPartner($object->getPartnerId()))
	    {
	        self::purgeAssetFromEdgeCast($object);
	    }

	    if (($object instanceof entry) && EdgeCastPlugin::isAllowedPartner($object->getPartnerId()))
	    {
	        self::purgeEntryFromEdgeCast($object);
	    }
	    
	    return true;
	}
	
	
	
	private static function purgeEntryFromEdgeCast(entry $entry)
	{
	    // get partner
	    $partnerId = $entry->getPartnerId();
        $partner = PartnerPeer::retrieveByPK($partnerId);
        if (!$partner) {
            KalturaLog::err('Cannot find partner with id ['.$partnerId.']');
            return false;
        }
	    
	    $mediaTypePathList = array(
	        array('MediaType' => self::EDGE_SERVICE_HTTP_LARGE_OBJECT_MEDIA_TYPE, 'MediaPath' => $entry->getDownloadUrl()),  // entry download url
	        array('MediaType' => self::EDGE_SERVICE_HTTP_SMALL_OBJECT_MEDIA_TYPE, 'MediaPath' => $entry->getThumbnailUrl()),  // entry thumbnail url	    
	    );
	    
	    return self::purgeFromEdgeCast($mediaTypePathList, $partner);
	}
	
	
    private static function purgeAssetFromEdgeCast(asset $asset)
	{
	    // get partner
	    $partnerId = $asset->getPartnerId();
        $partner = PartnerPeer::retrieveByPK($partnerId);
        if (!$partner) {
            KalturaLog::err('Cannot find partner with id ['.$partnerId.']');
            return false;
        }
	    
	    $mediaType = ($asset instanceof thumbAsset) ? self::EDGE_SERVICE_HTTP_SMALL_OBJECT_MEDIA_TYPE : self::EDGE_SERVICE_HTTP_LARGE_OBJECT_MEDIA_TYPE;
	    $mediaTypePathList = array();
	    try {
	        $mediaTypePathList[] = array('MediaType' => $mediaType, 'MediaPath' => $asset->getDownloadUrl());  // asset download url   
	    }
	    catch (Exception $e) {
	        KalturaLog::err('Cannot get asset URL for asset id ['.$asset->getId().'] - '.$e->getMessage());
	    }
	    
	    if ($asset instanceof flavorAsset)
	    {
	        // get a list of all URLs leading to the asset for purging
            $subPartnerId = $asset->getentry()->getSubpId();
            $partnerPath = myPartnerUtils::getUrlForPartner($partnerId, $subPartnerId);
            $assetId = $asset->getId();            
            
            $serveFlavorUrl = "$partnerPath/serveFlavor/entryId/".$asset->getEntryId()."/flavorId/$assetId".'*'; // * wildcard should delete all serveFlavor urls
            
            $types = array(
            		kPluginableEnumsManager::apiToCore(EdgeCastDeliveryProfileType::EDGE_CAST_HTTP),
            		kPluginableEnumsManager::apiToCore(EdgeCastDeliveryProfileType::EDGE_CAST_RTMP));
            
            $deliveryProfile = $partner->getDeliveryProfileIds();
            $deliveryProfileIds = array();
            foreach($deliveryProfile as $key=>$value) {
            	$deliveryProfileIds = array_merge($deliveryProfileIds, $value);
            }
            
            
			$c = new Criteria();
			$c->add(DeliveryProfilePeer::PARTNER_ID, $partnerId);
			$c->add(DeliveryProfilePeer::ID, $deliveryProfileIds, Criteria::IN);
            $c->addSelectColumn(DeliveryProfilePeer::HOST_NAME);
			$stmt = DeliveryProfilePeer::doSelectStmt($c);
			$hosts = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            foreach ($hosts as $host)
            {
                if (!empty($host)) {
                    $mediaTypePathList[] = array('MediaType' => $mediaType, 'MediaPath' => $host.$serveFlavorUrl);
                } 
            }
	    }
	    
        return self::purgeFromEdgeCast($mediaTypePathList, $partner);
	}
	
	
	private static function purgeFromEdgeCast($mediaPaths, $partner)
	{        
        // get EdgeCast parameters
        $edgeCastParams = EdgeCastPlugin::getEdgeCastParams($partner);
        if (!$edgeCastParams) {
            KalturaLog::err('Partner ['.$partner->getId().'] does not have any edge cast parameters configured');
            return false;
        }
        $edgeAccountNumber = $edgeCastParams->getAccountNumber();
        $edgeApiToken = $edgeCastParams->getApiToken();
        
        // set api parameters
        $edgeApiUrl = self::EDGE_CAST_API_URL_BASE.$edgeAccountNumber.self::EDGE_CAST_API_URL_PURGE;
        $edgeApiTokenHeader = self::EDGE_CAST_API_AUTHORIZATION_TOK_HEADER.$edgeApiToken;
        
        $curlResults = self::doJsonCurl($edgeApiUrl, $mediaPaths, $edgeApiTokenHeader);
        
        // just output the results to the log since there is nothing we can do if this failed
        KalturaLog::info('Curl results: '.print_r($curlResults, true));
	}
	
	
    /**
     * Curl HTTP POST Request
     *
     * @param string $url
     * @param array $params
     * @param array $headers
     * @return array of result and error
     */
    private static function doJsonCurl($apiUrl, $paramsArray, $tokenHeader)
    {        
        $headers = array(
            'Content-Type: application/json',
            'Accept: application/json',
            $tokenHeader,
        );
        
        $curlArray = array();
        $results = array();
        $mh = curl_multi_init();
        
        foreach ($paramsArray as $i => $params)
        {
            // init curl
            $curlArray[$i] = curl_init();
            $paramsJson = json_encode($params);
        	curl_setopt($curlArray[$i], CURLOPT_URL, $apiUrl);
        	curl_setopt($curlArray[$i], CURLOPT_CUSTOMREQUEST, "PUT");
        	curl_setopt($curlArray[$i], CURLOPT_POSTFIELDS, $paramsJson);
        	curl_setopt($curlArray[$i], CURLOPT_RETURNTRANSFER, true);
       	    curl_setopt($curlArray[$i], CURLOPT_HTTPHEADER, $headers);
        	curl_setopt($curlArray[$i], CURLOPT_SSL_VERIFYPEER, false);
        	curl_setopt($curlArray[$i], CURLOPT_SSL_VERIFYHOST, false);
        	// add to multi handle
        	curl_multi_add_handle($mh, $curlArray[$i]);
        }
        
        // execute multi curl handles
        $active = null;
        do {
            $mrc = curl_multi_exec($mh, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);
        
        while ($active && $mrc == CURLM_OK) {
            if (curl_multi_select($mh) != - 1) {
                do {
                    $mrc = curl_multi_exec($mh, $active);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
        }        
        
        // get content and remove handles
        foreach ($curlArray as $id => $ch)
        {
            $results[$id] = array(
                'result' => curl_multi_getcontent($ch),
                'error' => curl_error($ch),
                'http_code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
            );  
            curl_multi_remove_handle($mh, $ch);
        }
        
        // all done
        curl_multi_close($mh);
        
        return $results;
    }
	
}