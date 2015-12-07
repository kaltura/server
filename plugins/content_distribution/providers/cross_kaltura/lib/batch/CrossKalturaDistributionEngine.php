<?php
/**
 * @package plugins.crossKalturaDistribution
 * @subpackage lib.batch
 */
class CrossKalturaDistributionEngine extends DistributionEngine implements 
	IDistributionEngineSubmit,
	IDistributionEngineUpdate,
	IDistributionEngineDelete,
	IKalturaLogger
{
    
    const DISTRIBUTED_INFO_SOURCE_ID = 'sourceId';
    const DISTRIBUTED_INFO_TARGET_ID = 'targetId';
    const DISTRIBUTED_INFO_SOURCE_VERSION = 'sourceVersion';
    const DISTRIBUTED_INFO_SOURCE_UPDATED_AT = 'sourceUpdatedAt';
    
    
    /**
     * @var KalturaClient
     */
    protected $targetClient = null;
    
    /**
     * @var KalturaClient
     */
    protected $sourceClient = null;
    
    /**
     * @var KalturaCrossKalturaDistributionProfile
     */
    protected $distributionProfile = null;
    
    /**
     * Should distribute caption assets ?
     * @var bool
     */
    protected $distributeCaptions = false;
    
    /**
     * Should distribute cue points ?
     * @var bool
     */
    protected $distributeCuePoints = false;
    
    
    /**
     * Will hold the target entry ID once created
     * @var string
     */
    protected $targetEntryId;
    
    protected $fieldValues = array();
    
    
    protected $mapAccessControlIds = array();
    protected $mapConversionProfileIds = array();
    protected $mapMetadataProfileIds = array();
    protected $mapStorageProfileIds = array();
    protected $mapFlavorParamsIds = array();
    protected $mapThumbParamsIds = array();
    protected $mapCaptionParamsIds = array();
            
    /**
     * @var CrossKalturaEntryObjectsContainer
     */
    protected $sourceObjects = null;
    
    // ------------------------------
	//  initialization methods
	// ------------------------------
    
    
    public function __construct()
    {
        $this->targetClient = null;
        $this->distributeCaptions = false;
        $this->distributeCuePoints = false;
        $this->mapAccessControlIds = array();
        $this->mapConversionProfileIds = array();
        $this->mapMetadataProfileIds = array();
        $this->mapStorageProfileIds = array();
        $this->mapFlavorParamsIds = array();
        $this->mapThumbParamsIds = array();
        $this->mapCaptionParamsIds = array();
        $this->fieldValues = array();
        $this->sourceObjects = null;
    }
    
	/**
	 * Initialize
	 * @param KalturaDistributionJobData $data
	 * @throws Exception
	 */
	protected function init(KalturaDistributionJobData $data)
	{
	    // validate objects
		if(!$data->distributionProfile instanceof KalturaCrossKalturaDistributionProfile)
			throw new Exception('Distribution profile must be of type KalturaCrossKalturaDistributionProfile');
	
		if (!$data->providerData instanceof KalturaCrossKalturaDistributionJobProviderData)
			throw new Exception('Provider data must be of type KalturaCrossKalturaDistributionJobProviderData');
			
		$this->distributionProfile = $data->distributionProfile;
			
		// init target kaltura client
		$this->initClients($this->distributionProfile);
		
		// check for plugins availability
		$this->initPlugins($this->distributionProfile);
		
		// init mapping arrays
		$this->initMapArrays($this->distributionProfile);
		
		// init field values
		$this->fieldValues = unserialize($data->providerData->fieldValues);
		if (!$this->fieldValues) {
		    $this->fieldValues = array();
		}
	}
	
	/**
	 * Init a KalturaClient object for the target account
	 * @param KalturaCrossKalturaDistributionProfile $distributionProfile
	 * @throws Exception
	 */
	protected function initClients(KalturaCrossKalturaDistributionProfile $distributionProfile)
	{
	    // init source client
	    KalturaLog::debug('Initializing source kaltura client');	    
	    $sourceClientConfig = new KalturaConfiguration($distributionProfile->partnerId);
        $sourceClientConfig->serviceUrl = KBatchBase::$kClient->getConfig()->serviceUrl; // copy from static batch client
        $sourceClientConfig->setLogger($this);
        $this->sourceClient = new KalturaClient($sourceClientConfig);
        $this->sourceClient->setKs(KBatchBase::$kClient->getKs()); // copy from static batch client
	    
	    // init target client
	    KalturaLog::debug('Initializing target kaltura client');
	    $targetClientConfig = new KalturaConfiguration($distributionProfile->targetAccountId);
        $targetClientConfig->serviceUrl = $distributionProfile->targetServiceUrl;
		$targetClientConfig->setLogger($this);
        $this->targetClient = new KalturaClient($targetClientConfig);
        $ks = $this->targetClient->user->loginByLoginId($distributionProfile->targetLoginId, $distributionProfile->targetLoginPassword, "", 86400, 'disableentitlement');
        $this->targetClient->setKs($ks);
	}
	
	/**
	 * Check which server plugins should be used
	 * @param KalturaCrossKalturaDistributionProfile $distributionProfile
	 * @throws Exception
	 */
	protected function initPlugins(KalturaCrossKalturaDistributionProfile $distributionProfile)
	{
	    // check if should distribute caption assets
	    $this->distributeCaptions = false;
		if ($distributionProfile->distributeCaptions == true)
	    {
    	    if (class_exists('CaptionPlugin') && class_exists('KalturaCaptionClientPlugin') && KalturaPluginManager::getPluginInstance(CaptionPlugin::getPluginName()))
            {
                $this->distributeCaptions = true;    
            }
            else
            {
                KalturaLog::err('Missing CaptionPlugin');
                throw new Exception('Missing CaptionPlugin');   
            }
	    }	    
        
	    // check if should distribute cue points
	    $this->distributeCuePoints = false;
	    if ($distributionProfile->distributeCuePoints == true)
	    {
    	    if (class_exists('CuePointPlugin') && class_exists('KalturaCuePointClientPlugin') && KalturaPluginManager::getPluginInstance(CuePointPlugin::getPluginName()))
            {
                $this->distributeCuePoints = true;    
            }
	        else
            {
                KalturaLog::err('Missing CuePointPlugin');
                throw new Exception('Missing CuePointPlugin');      
            }
	    }
	}
	
	
	protected function initMapArrays(KalturaCrossKalturaDistributionProfile $distributionProfile)
	{
	    $this->mapAccessControlIds = $this->toKeyValueArray($distributionProfile->mapAccessControlProfileIds);
	    $this->mapConversionProfileIds = $this->toKeyValueArray($distributionProfile->mapConversionProfileIds);
	    $this->mapMetadataProfileIds = $this->toKeyValueArray($distributionProfile->mapMetadataProfileIds);
	    $this->mapStorageProfileIds = $this->toKeyValueArray($distributionProfile->mapStorageProfileIds);
	    $this->mapFlavorParamsIds = $this->toKeyValueArray($distributionProfile->mapFlavorParamsIds);
	    $this->mapThumbParamsIds = $this->toKeyValueArray($distributionProfile->mapThumbParamsIds);
	    $this->mapCaptionParamsIds = $this->toKeyValueArray($distributionProfile->mapCaptionParamsIds);
	}
	
	
	// ------------------------------
	//  get existing objects via api
	// ------------------------------
	
	
	
	/**
	 * @param KalturaDistributionJobData $data
	 * @return CrossKalturaEntryObjectsContainer
	 */
	protected function getSourceObjects(KalturaDistributionJobData $data)
	{
	    KalturaLog::debug('Getting source entry objects');
	    $sourceEntryId = $data->entryDistribution->entryId;
	    $sourceObjects = $this->getEntryObjects($this->sourceClient, $sourceEntryId, $data);	    
	    return $sourceObjects;   
	}
	
	/**
	 * Get entry objects for distribution
	 * @param KalturaClient $client
	 * @param string $entryId
	 * @param KalturaDistributionJobData $data
	 * @return CrossKalturaEntryObjectsContainer
	 */
	protected function getEntryObjects(KalturaClient $client, $entryId, KalturaDistributionJobData $data)
	{
        $remoteFlavorAssetContent = $data->distributionProfile->distributeRemoteFlavorAssetContent;
        $remoteThumbAssetContent = $data->distributionProfile->distributeRemoteThumbAssetContent;
        $remoteCaptionAssetContent = $data->distributionProfile->distributeRemoteCaptionAssetContent;
	    
	    // get entry
	    KalturaLog::debug('Getting entry id ['.$entryId.']');
	    $entry = $client->baseEntry->get($entryId);
	    
	    // get entry's flavor assets chosen for distribution
	    $flavorAssets = array();
	    if (!empty($data->entryDistribution->flavorAssetIds))
	    {
    	    $flavorAssetFilter = new KalturaFlavorAssetFilter();
    	    $flavorAssetFilter->idIn = $data->entryDistribution->flavorAssetIds;
    	    $flavorAssetFilter->entryIdEqual = $entryId;
    	    try {
                KalturaLog::debug('Getting entry\'s flavor assets');
                $flavorAssetsList = $client->flavorAsset->listAction($flavorAssetFilter);
                foreach ($flavorAssetsList->objects as $asset)
                {
                    $flavorAssets[$asset->id] = $asset;
                }
            }
            catch (Exception $e) {
                KalturaLog::err('Cannot get list of flavor assets - '.$e->getMessage());
                throw $e;
            }
	    }
	    else
	    {
	        KalturaLog::debug('No flavor assets set for distribution!');    
	    }
	    
	    // get flavor assets content
	    KalturaLog::debug('Getting flavor asset content for ids ['.implode(',', array_keys($flavorAssets)).']');
	    $flavorAssetsContent = array();
	    foreach ($flavorAssets as $flavorAsset)
	    {
	        $flavorAssetsContent[$flavorAsset->id] = $this->getAssetContentResource($flavorAsset->id, $client->flavorAsset, $remoteFlavorAssetContent);
	    }   
	    
	    
	    // get entry's thumbnail assets chosen for distribution
	    $thumbAssets = array();
	    if (!empty($data->entryDistribution->thumbAssetIds))
	    {
    	    $thumbAssetFilter = new KalturaThumbAssetFilter();
    	    $thumbAssetFilter->idIn = $data->entryDistribution->thumbAssetIds;
    	    $thumbAssetFilter->entryIdEqual = $entryId;
    	    try {
                KalturaLog::debug('Getting entry\'s thumbnail assets');
                $thumbAssetsList = $client->thumbAsset->listAction($thumbAssetFilter);
    	        foreach ($thumbAssetsList->objects as $asset)
                {
                    $thumbAssets[$asset->id] = $asset;
                }
            }
            catch (Exception $e) {
                KalturaLog::err('Cannot get list of thumbnail assets - '.$e->getMessage());
                throw $e;
            }
	    }
	    else
	    {
	        KalturaLog::debug('No thumb assets set for distribution!');    
	    }
	    
	    // get thumb assets content
	    KalturaLog::debug('Getting thumb asset content for ids ['.implode(',', array_keys($thumbAssets)).']');
	    $thumbAssetsContent = array();
	    foreach ($thumbAssets as $thumbAsset)
	    {
	        $thumbAssetsContent[$thumbAsset->id] = $this->getAssetContentResource($thumbAsset->id, $client->thumbAsset, $remoteThumbAssetContent);
	    } 
	    
	    
	    // get entry's custom metadata objects
	    $metadataObjects = array();
    	$metadataFilter = new KalturaMetadataFilter();
        $metadataFilter->metadataObjectTypeEqual = KalturaMetadataObjectType::ENTRY;
        $metadataFilter->objectIdEqual = $entryId;
        try {
            KalturaLog::debug('Getting entry\'s metadata objects');
            $metadataClient = KalturaMetadataClientPlugin::get($client);  
            $metadataObjectsList = $metadataClient->metadata->listAction($metadataFilter);
            foreach ($metadataObjectsList->objects as $metadata)
            {
                $metadataObjects[$metadata->id] = $metadata;
            }
        }
        catch (Exception $e) {
            KalturaLog::err('Cannot get list of metadata objects - '.$e->getMessage());
            throw $e;
        }
        
        // get entry's caption assets
        $captionAssetClient = KalturaCaptionClientPlugin::get($client);  
        $captionAssets = array();
        if ($this->distributeCaptions == true)
        {
            $captionAssetFilter = new KalturaCaptionAssetFilter();
            $captionAssetFilter->entryIdEqual = $entryId;
    	    try {
                KalturaLog::debug('Getting entry\'s caption assets');
                $captionAssetsList = $captionAssetClient->captionAsset->listAction($captionAssetFilter);
    	        foreach ($captionAssetsList->objects as $asset)
                {
                    $captionAssets[$asset->id] = $asset;
                }
            }
            catch (Exception $e) {
                KalturaLog::err('Cannot get list of caption assets - '.$e->getMessage());
                throw $e;
            }
        }
        else
        {
            KalturaLog::debug('Caption distribution is turned off');
        }
        
        
        // get caption assets content
	    $captionAssetsContent = array();
	    KalturaLog::debug('Getting caption asset content for ids ['.implode(',', array_keys($captionAssets)).']');
	    foreach ($captionAssets as $captionAsset)
	    {
	        $captionAssetsContent[$captionAsset->id] = $this->getAssetContentResource($captionAsset->id, $captionAssetClient->captionAsset, $remoteCaptionAssetContent);
	    } 
        
        
        // get entry's cue points
        $cuePoints = array();
        if ($this->distributeCuePoints == true)
        {
            $cuePointFilter = new KalturaCuePointFilter();
            $cuePointFilter->entryIdEqual = $entryId;        
    	    try {
                KalturaLog::debug('Getting entry\'s cue points');
                $cuePointClient = KalturaCuePointClientPlugin::get($client);            
                $cuePointsList = $cuePointClient->cuePoint->listAction($cuePointFilter);
    	        foreach ($cuePointsList->objects as $cuePoint)
                {
                    $cuePoints[$cuePoint->id] = $cuePoint;
                }
            }
            catch (Exception $e) {
                KalturaLog::err('Cannot get list of cue points - '.$e->getMessage());
                throw $e;
            }
        }
	    else
        {
            KalturaLog::debug('Cue points distribution is turned off');
        }
		
        $entryObjects = new CrossKalturaEntryObjectsContainer();
        $entryObjects->entry = $entry;
        $entryObjects->metadataObjects = $metadataObjects;
        $entryObjects->flavorAssets = $flavorAssets;
        $entryObjects->flavorAssetsContent = $flavorAssetsContent;
        $entryObjects->thumbAssets = $thumbAssets;
        $entryObjects->thumbAssetsContent = $thumbAssetsContent;
        $entryObjects->captionAssets = $captionAssets;
        $entryObjects->captionAssetsContent = $captionAssetsContent;
        $entryObjects->cuePoints = $cuePoints;
        
        return $entryObjects;
	}
	
	
	/**
	 * @return KalturaContentResource content resource for the given asset in the target account
	 * @param string $assetId
	 * @param KalturaServiceBase $assetService
	 * @param bool $remote
	 */
	protected function getAssetContentResource($assetId, KalturaServiceBase $assetService, $remote)
	{
	    KalturaLog::debug('Getting content resource for asset id ['.$assetId.'] remote ['.$remote.']');
	    $contentResource = null;
	    	    
	    if ($remote)
	    {
	        // get remote resource
	        
	        $contentResource = new KalturaRemoteStorageResources();
	        $contentResource->resources = array();
	        
	        $remotePaths = $assetService->getRemotePaths($assetId);
	        $remotePaths = $remotePaths->objects;
	        foreach ($remotePaths as $remotePath)
	        {
	            /* @var $remotePath KalturaRemotePath */
	            $res = new KalturaRemoteStorageResource();
	            if (!isset($this->mapStorageProfileIds[$remotePath->storageProfileId]))
	            {
	                throw new Exception('Cannot map storage profile ID ['.$remotePath->storageProfileId.']');
	            }
	            $res->storageProfileId = $this->mapStorageProfileIds[$remotePath->storageProfileId];
	            $res->url = $remotePath->uri;      
	            
	            $contentResource->resources[] = $res;
	        }
	    }
	    else 
	    {
	        // get local resource
	        $contentResource = new KalturaUrlResource();
	        $contentResource->url = $assetService->getUrl($assetId);
	    }
	    return $contentResource;
	}
	

	
	// -----------------------------------------------
	//  methods to transform source to target objects
	// -----------------------------------------------
	
	/**
	 * Transform source entry object to a target object ready for insert/update
	 * @param KalturaBaseEntry $sourceEntry
	 * @param bool $forUpdate
	 * @return KalturaBaseEntry
	 */
	protected function transformEntry(KalturaBaseEntry $sourceEntry, $forUpdate = false)
	{
	    // remove readonly/insertonly parameters
	    /* @var $targetEntry KalturaBaseEntry */
	    $targetEntry = $this->copyObjectForInsertUpdate($sourceEntry);
	    
	    // switch to target account's object ids
	    if ($forUpdate)
	    {
	    	$targetEntry = $this->removeInsertOnly($targetEntry);
    	    $targetEntry->conversionProfileId = null;    
	    }
	    else
	    {
	        if (!is_null($sourceEntry->conversionProfileId))
    	    {
        	    if (!isset($this->mapConversionProfileIds[$sourceEntry->conversionProfileId]))
        	    {
        	        throw new Exception('Cannot map conversion profile ID ['.$sourceEntry->conversionProfileId.']');
        	    } 
        	    $targetEntry->conversionProfileId = $this->mapConversionProfileIds[$sourceEntry->conversionProfileId];
    	    }
	    }
	    
	    if (!is_null($sourceEntry->accessControlId))
	    {
    	    if (!isset($this->mapAccessControlIds[$sourceEntry->accessControlId]))
    	    {
    	        throw new Exception('Cannot map access control ID ['.$sourceEntry->accessControlId.']');
    	    }
    	    $targetEntry->accessControlId = $this->mapAccessControlIds[$sourceEntry->accessControlId];
	    }
	    
	    // transform metadata according to fields configuration
	    $targetEntry->name = $this->getValueForField(KalturaCrossKalturaDistributionField::BASE_ENTRY_NAME);
	    $targetEntry->description = $this->getValueForField(KalturaCrossKalturaDistributionField::BASE_ENTRY_DESCRIPTION);
	    $targetEntry->userId = $this->getValueForField(KalturaCrossKalturaDistributionField::BASE_ENTRY_USER_ID);
	    $targetEntry->tags = $this->getValueForField(KalturaCrossKalturaDistributionField::BASE_ENTRY_TAGS);
	    $targetEntry->categories = $this->getValueForField(KalturaCrossKalturaDistributionField::BASE_ENTRY_CATEGORIES);
	    $targetEntry->categoriesIds = null;
	    $targetEntry->partnerData = $this->getValueForField(KalturaCrossKalturaDistributionField::BASE_ENTRY_PARTNER_DATA);
	    $targetEntry->startDate = $this->getValueForField(KalturaCrossKalturaDistributionField::BASE_ENTRY_START_DATE);
	    $targetEntry->endDate = $this->getValueForField(KalturaCrossKalturaDistributionField::BASE_ENTRY_END_DATE);
	    $targetEntry->referenceId = $this->getValueForField(KalturaCrossKalturaDistributionField::BASE_ENTRY_REFERENCE_ID);
	    $targetEntry->licenseType = $this->getValueForField(KalturaCrossKalturaDistributionField::BASE_ENTRY_LICENSE_TYPE);
	    if (isset($targetEntry->conversionQuality)) {
	        $targetEntry->conversionQuality = null;
	    }
	    
	    // turn problematic empty fields to null
	    if (!$targetEntry->startDate) { $targetEntry->startDate = null; }
	    if (!$targetEntry->endDate) { $targetEntry->endDate = null; }
	    if (!$targetEntry->referenceId) { $targetEntry->referenceId = null; }
	    
	    // return transformed entry object
	    return $targetEntry;
	}
	
	
	/**
	 * Transform source metadata objects to target objects ready for insert/update
	 * @param array<KalturaMetadata> $sourceMetadatas
	 * @return array<KalturaMetadata>
	 */
	protected function transformMetadatas(array $sourceMetadatas)
	{
	    if (!count($sourceMetadatas)) {
	        return array();
	    }
	    
	    $targetMetadatas = array();
	    foreach ($sourceMetadatas as $sourceMetadata)
	    {
	        /* @var $sourceMetadata KalturaMetadata */
	        
	        if (!isset($this->mapMetadataProfileIds[$sourceMetadata->metadataProfileId]))
	        {
	            throw new Exception('Cannot map metadata profile ID ['.$sourceMetadata->metadataProfileId.']');
	        }
	        
	        $targetMetadata = new KalturaMetadata();
	        $targetMetadata->metadataProfileId = $this->mapMetadataProfileIds[$sourceMetadata->metadataProfileId];
	        
            $xsltStr = $this->distributionProfile->metadataXslt;
            if (!is_null($xsltStr) && strlen($xsltStr) > 0)
            {
                KalturaLog::debug('Trying to transform source metadata id ['.$sourceMetadata->id.']...');
                $targetMetadata->xml = $this->transformXml($sourceMetadata->xml, $xsltStr);
            }
            else
            {
                $targetMetadata->xml = $sourceMetadata->xml;
            }
	        
	        $targetMetadatas[$sourceMetadata->id] = $targetMetadata;    
	    }
	    
	    return $targetMetadatas;
	}
	
	
	/**
	 * Transform source flavor assets to target objects ready for insert/update
	 * @param array<KalturaFlavorAsset> $sourceFlavorAssets
	 * @return array<KalturaFlavorAsset>
	 */
	protected function transformFlavorAssets(array $sourceFlavorAssets)
	{
	    return $this->transformAssets($sourceFlavorAssets, $this->mapFlavorParamsIds, 'flavorParamsId');
	}
	
	/**
	 * Transform source thumbnail assets to target objects ready for insert/update
	 * @param array<KalturaThumbAsset> $sourceThumbAssets
	 * @return array<KalturaThumbAsset>
	 */
	protected function transformThumbAssets(array $sourceThumbAssets)
	{
	    return $this->transformAssets($sourceThumbAssets, $this->mapThumbParamsIds, 'thumbParamsId');
	}
	
	/**
	 * Transform source caption assets to target objects ready for insert/update
	 * @param array<KalturaCaptionAsset> $sourceCaptionAssets
	 * @return array<KalturaCaptionAsset>
	 */
	protected function transformCaptionAssets(array $sourceCaptionAssets)
	{
	    return $this->transformAssets($sourceCaptionAssets, $this->mapCaptionParamsIds, 'captionParamsId');
	}
		
	/**
	 * 
	 * Transform source assets to target assets ready for insert/update
	 * @param array<KalturaAsset> $sourceAssets
	 * @param array $mapParams
	 * @param string $paramsFieldName
	 * @return array<KalturaAsset>
	 */
	protected function transformAssets(array $sourceAssets, array $mapParams, $paramsFieldName)
	{
	    if (!count($sourceAssets)) {
	        return array();
	    }
	    
	    $targetAssets = array();
	    foreach ($sourceAssets as $sourceAsset)
	    {
	        // remove readonly/insertonly parameters
    	    $targetAsset = $this->copyObjectForInsertUpdate($sourceAsset);
    	    
            // switch to target params id if defined, else leave same as source
    	    if (isset($mapParams[$sourceAsset->{$paramsFieldName}]))
    	    {
    	        $targetAsset->{$paramsFieldName} = $mapParams[$sourceAsset->{$paramsFieldName}];
    	    }
    	    else
    	    {
    	        $targetAsset->{$paramsFieldName} = $sourceAsset->{$paramsFieldName};
    	    }
    	    
    	    $targetAssets[$sourceAsset->id] = $targetAsset;    	    
	    }
	    
	    return $targetAssets;	    
	}

	
	/**
	 * Transform source cue points to target objects ready for insert/update
	 * @param array<KalturaCuePoint> $sourceCuePoints
	 * @return array<KalturaCuePoint>
	 */
	protected function transformCuePoints(array $sourceCuePoints)
	{
	    if (!count($sourceCuePoints)) {
	        return array();
	    }
	    
	    $targetCuePoints = array();
	    foreach ($sourceCuePoints as $sourceCuePoint)
	    {
	        // remove readonly/insertonly parameters
    	    $targetCuePoint = $this->copyObjectForInsertUpdate($sourceCuePoint);
    	    $targetCuePoints[$sourceCuePoint->id] = $targetCuePoint;    	    
	    }
	    
	    return $targetCuePoints;
	}
	
	
	protected function transformAssetContent(array $assetContent)
	{
	    if (!count($assetContent)) {
	        return array();
	    }
	    
	    $targetAssetContent = null;
	    
	}
	
	
	/**
	 * Transform source objects to target objects ready for insert/update
	 * @param CrossKalturaEntryObjectsContainer $sourceObjects
	 * @param bool $forUpdate
	 * @return CrossKalturaEntryObjectsContainer target objects
	 */
	protected function transformSourceToTarget(CrossKalturaEntryObjectsContainer $sourceObjects, $forUpdate = false)
	{
	    KalturaLog::debug('Transforming source to target objects');
	    $targetObjects = new CrossKalturaEntryObjectsContainer();
	    $targetObjects->entry = $this->transformEntry($sourceObjects->entry, $forUpdate); // basic entry object
		$targetObjects->metadataObjects = $this->transformMetadatas($sourceObjects->metadataObjects); // metadata objects
		$targetObjects->flavorAssets = $this->transformFlavorAssets($sourceObjects->flavorAssets); // flavor assets
		$targetObjects->flavorAssetsContent = $sourceObjects->flavorAssetsContent; // flavor assets content - already transformed
		$targetObjects->thumbAssets = $this->transformThumbAssets($sourceObjects->thumbAssets); // thumb assets
		$targetObjects->thumbAssetsContent = $sourceObjects->thumbAssetsContent; // thumb assets content - already transformed
		if ($this->distributeCaptions)
		{
		    $targetObjects->captionAssets = $this->transformCaptionAssets($sourceObjects->captionAssets); // caption assets
		    $targetObjects->captionAssetsContent = $sourceObjects->captionAssetsContent; // caption assets content - already transformed
		}
        if ($this->distributeCuePoints)
        {
		    $targetObjects->cuePoints = $this->transformCuePoints($sourceObjects->cuePoints); // cue points
        }
		return $targetObjects;
	}
	
	
	
	// ------------------------------------------------------------
    //  special methods to extract object arguments for add/update
    // ------------------------------------------------------------
	
    /**
     * @return array of arguments that should be passed to metadata->update api action
     * @param string $existingObjId
     * @param KalturaMetadata $newObj
     */
	protected function getMetadataUpdateArgs($existingObjId, KalturaMetadata $newObj)
	{
	    return array(
	        $existingObjId,
	        $newObj->xml
	    );
	}
	
	/**
	 * @return array of arguments that should be passed to metadata->add api action
	 * @param KalturaMetadata $newObj
	 */
    protected function getMetadataAddArgs(KalturaMetadata $newObj)
	{
	    return array(
	        $newObj->metadataProfileId,
	        KalturaMetadataObjectType::ENTRY,
	        $newObj->objectId,
	        $newObj->xml
	    );
	}
	
	/**
	 * @return array of arguments that should be passed to cuepoint->add api action
	 * @param KalturaCuePoint $newObj
	 */
	protected function getCuePointAddArgs(KalturaCuePoint $newObj)
	{
	    return array(
	        $newObj
	    );
	}
	
	
	// ----------------------
    //  distribution actions
    // ----------------------
	
	/**
	 * Fill provider data with map of distributed objects
	 * @param KalturaDistributionJobData $data
	 * @param CrossKalturaEntryObjectsContainer $syncedObjects
	 */
    protected function getDistributedMap(KalturaDistributionJobData $data, CrossKalturaEntryObjectsContainer $syncedObjects)
    {
        KalturaLog::debug('Generating map of distributed objects info');
        $data->providerData->distributedFlavorAssets = $this->getDistributedMapForObjects($this->sourceObjects->flavorAssets, $syncedObjects->flavorAssets);
		$data->providerData->distributedThumbAssets = $this->getDistributedMapForObjects($this->sourceObjects->thumbAssets, $syncedObjects->thumbAssets);
		$data->providerData->distributedMetadata = $this->getDistributedMapForObjects($this->sourceObjects->metadataObjects, $syncedObjects->metadataObjects);
		$data->providerData->distributedCaptionAssets = $this->getDistributedMapForObjects($this->sourceObjects->captionAssets, $syncedObjects->captionAssets);
		$data->providerData->distributedCuePoints = $this->getDistributedMapForObjects($this->sourceObjects->cuePoints, $syncedObjects->cuePoints);
		
		return $data;
    }
    
       
    /**
     * Get distributed map for the given objects
     * @param unknown_type $sourceObjects
     * @param unknown_type $syncedObjects
     */
    protected function getDistributedMapForObjects($sourceObjects, $syncedObjects)
    {
        $info = array();
        foreach ($syncedObjects as $sourceId => $targetObj)
        {
            $sourceObj = $sourceObjects[$sourceId];
            $objInfo = array();
            $objInfo[self::DISTRIBUTED_INFO_SOURCE_ID] = $sourceId;
            $objInfo[self::DISTRIBUTED_INFO_TARGET_ID] = $targetObj->id;
            $objInfo[self::DISTRIBUTED_INFO_SOURCE_VERSION] = $sourceObj->version;
            $objInfo[self::DISTRIBUTED_INFO_SOURCE_UPDATED_AT] = $sourceObj->updatedAt;

            $info[$sourceId] = $objInfo;
        }
        return serialize($info);
    }
	
	
	
	/**
	 * Sync objects between the source and target accounts
	 * @param KalturaServiceBase $targetClientService API service for the current object type
	 * @param array $newObjects array of target objects that should be added/updated
	 * @param array $sourceObjects array of source objects
	 * @param array $distributedMap array of information about previously distributed objects
	 * @param string $targetEntryId
	 * @param string $addArgsFunc special function to extract arguments for the ADD api action
	 * @param string $updateArgsFunc special function to extract arguments for the UPDATE api action
	 * @return array of the synced objects
	 */
	protected function syncTargetEntryObjects(KalturaServiceBase $targetClientService, $newObjects, $sourceObjects, $distributedMap, $targetEntryId, $addArgsFunc = null, $updateArgsFunc = null)
	{
	    $syncedObjects = array();
	    $distributedMap = empty($distributedMap) ? array() : unserialize($distributedMap);
	    
	    // walk through all new target objects and add/update on target as necessary
	    if (count($newObjects))
	    {
	        KalturaLog::debug('Syncing target objects for source IDs ['.implode(',', array_keys($newObjects)).']');
	        foreach ($newObjects as $sourceObjectId => $targetObject)
	        {
	            if (is_array($distributedMap) && array_key_exists($sourceObjectId, $distributedMap))
	            {
	                // this object was previously distributed
	                KalturaLog::debug('Source object id ['.$sourceObjectId.'] was previously distributed');
	                
	                $lastDistributedUpdatedAt = isset($distributedMap[$sourceObjectId][self::DISTRIBUTED_INFO_SOURCE_UPDATED_AT]) ? $distributedMap[$sourceObjectId][self::DISTRIBUTED_INFO_SOURCE_UPDATED_AT] : null;
                    $currentSourceUpdatedAt = isset($sourceObjects[$sourceObjectId]->updatedAt)	? $sourceObjects[$sourceObjectId]->updatedAt : null;

                    $targetObjectId = isset($distributedMap[$sourceObjectId][self::DISTRIBUTED_INFO_TARGET_ID]) ? $distributedMap[$sourceObjectId][self::DISTRIBUTED_INFO_TARGET_ID] : null;
                    if (is_null($targetObjectId))
                    {
                        throw new Exception('Missing previously distributed target object id for source id ['.$sourceObjectId.']');
                    }
                    
                    if (!is_null($lastDistributedUpdatedAt) && !is_null($currentSourceUpdatedAt) && $currentSourceUpdatedAt <= $lastDistributedUpdatedAt)
                    {
                        // object wasn't updated since last distributed - just return existing info
                        KalturaLog::debug('No need to re-distributed object since it was not updated since last distribution - returning dummy object with target id ['.$targetObjectId.']');
                        $targetObject->id = $targetObjectId;
                        $syncedObjects[$sourceObjectId] = $targetObject;
                    }
                    else
                    {
    	                // should update existing target object
    	                KalturaLog::debug('Updating object...');
    	                $targetObjectForUpdate = $this->removeInsertOnly($targetObject);
    	                $updateArgs = null;
    	                if (is_null($updateArgsFunc)) {
    	                    $updateArgs = array($targetObjectId, $targetObjectForUpdate);
    	                }
    	                else {
    	                    $updateArgs = call_user_func_array(array($this, $updateArgsFunc), array($targetObjectId, $targetObjectForUpdate));
    	                }
    	                $syncedObjects[$sourceObjectId] = call_user_func_array(array($targetClientService, 'update'), $updateArgs);
                    }
         
	                unset($distributedMap[$sourceObjectId]);
	            }
	            else
	            {
	                // this object was not previously distributed - should add new target object
	                KalturaLog::debug('Adding object...');
	                $addArgs = null;
	                if (is_null($addArgsFunc)) {
	                    $addArgs = array($targetEntryId, $targetObject);
	                }
	                else {
	                    $addArgs = call_user_func_array(array($this, $addArgsFunc), array($targetObject));
	                }
	                
	                $syncedObjects[$sourceObjectId] = call_user_func_array(array($targetClientService, 'add'), $addArgs);
	            }
	        }
	    }
	    
	    // check if previously distributed objects should be deleted from the target account
	    if (count($distributedMap))
	    {
	        KalturaLog::debug('Deleting target objects that were deleted in source with IDs ['.implode(',', array_keys($distributedMap)).']');
	        foreach ($distributedMap as $sourceId => $objInfo)
	        {
	            // delete from target account
	            $targetId = isset($objInfo[self::DISTRIBUTED_INFO_TARGET_ID]) ? $objInfo[self::DISTRIBUTED_INFO_TARGET_ID] : null;
	            KalturaLog::debug('Deleting previously distributed source object id ['.$sourceId.'] target object id ['.$targetId.']');
	            if (is_null($targetId))
	            {
	                throw new Exception('Missing previously distributed target object id for source id ['.$sourceId.']');
	            }
	            try {
	            	$targetClientService->delete($targetId);
	            }
	            catch (Exception $e)
	            {
	            	$acceptableErrorCodes = array(
	            		'FLAVOR_ASSET_ID_NOT_FOUND',
	            		'THUMB_ASSET_ID_NOT_FOUND',
	            		'INVALID_OBJECT_ID',
	            		'CAPTION_ASSET_ID_NOT_FOUND',
	            		'INVALID_CUE_POINT_ID',
	            	);
	            	if (in_array($e->getCode(), $acceptableErrorCodes))
	            	{
	            		KalturaLog::warning('Object with id ['.$targetId.'] is already deleted - ignoring exception');
	            	}
	            	else
	            	{
	            		throw $e;
	            	}
	            }	            
	            
	        }
	    }

	    return $syncedObjects;	    
	}
	
	
	protected function syncAssetsContent(KalturaServiceBase $targetClientService, $targetAssetsContent, $targetAssets, $distributedMap, $sourceAssets)
	{
	    $distributedMap = empty($distributedMap) ? array() : unserialize($distributedMap);
	    
        foreach ($targetAssetsContent as $sourceAssetId => $targetAssetContent)
        {
            $targetAssetId = isset($targetAssets[$sourceAssetId]->id) ? $targetAssets[$sourceAssetId]->id : null;
            if (is_null($targetAssetId))
            {
                throw new Exception('Missing target id of source asset id ['.$sourceAssetId.']');
            }

            $currentSourceVersion = isset($sourceAssets[$sourceAssetId]->version) ? $sourceAssets[$sourceAssetId]->version : null;
            $lastDistributedSourceVersion = isset($distributedMap[$sourceAssetId][self::DISTRIBUTED_INFO_SOURCE_VERSION]) ? $distributedMap[$sourceAssetId][self::DISTRIBUTED_INFO_SOURCE_VERSION] : null;
            
            if (!is_null($currentSourceVersion) && !is_null($lastDistributedSourceVersion) && $currentSourceVersion <= $lastDistributedSourceVersion)
            {
                KalturaLog::debug('No need to update content of source asset id ['.$sourceAssetId.'] target id ['.$targetAssetId.'] since it was not updated since last distribution');
            }
            else
            {
                KalturaLog::debug('Updating content for source asset id ['.$sourceAssetId.'] target id ['.$targetAssetId.']');
                $targetClientService->setContent($targetAssetId, $targetAssetContent); 
            }           
        }
	}
    
	/**
	 * Sync target objects
	 * @param KalturaDistributionJobData $jobData
	 * @param CrossKalturaEntryObjectsContainer $targetObjects
	 */
	protected function sync(KalturaDistributionJobData $jobData, CrossKalturaEntryObjectsContainer $targetObjects)
	{
        $syncedObjects = new CrossKalturaEntryObjectsContainer();

        $targetEntryId = $jobData->remoteId;
        
        // add/update entry
        if ($targetEntryId)
        {
            // update entry
            KalturaLog::debug('Updating target entry id ['.$targetEntryId.']');
            $syncedObjects->entry = $this->targetClient->baseEntry->update($targetEntryId, $targetObjects->entry);
        }
        else
        {
            // add entry
            KalturaLog::debug('Adding new target entry');
    	    $syncedObjects->entry = $this->targetClient->baseEntry->add($targetObjects->entry);
    	    $targetEntryId = $syncedObjects->entry->id;
    	    KalturaLog::debug('New target entry added with id ['.$targetEntryId.']');
        }
        $this->targetEntryId = $targetEntryId;
        
        // sync metadata objects
        KalturaLog::debug('Syncing metadata objects...');
        foreach ($targetObjects->metadataObjects as $metadataObj)
        {
            /* @var $metadataObj KalturaMetadata */
            $metadataObj->objectId = $targetEntryId;
        }
        $targetMetadataClient = KalturaMetadataClientPlugin::get($this->targetClient);
	    $syncedObjects->metadataObjects = $this->syncTargetEntryObjects(
	        $targetMetadataClient->metadata,
	        $targetObjects->metadataObjects,
	        $this->sourceObjects->metadataObjects,
	        $jobData->providerData->distributedMetadata,
	        $targetEntryId,
	        'getMetadataAddArgs',
	        'getMetadataUpdateArgs'
        );
	    
	    
	    // sync flavor assets
	    KalturaLog::debug('Syncing flavor assets...');
	    $syncedObjects->flavorAssets = $this->syncTargetEntryObjects(
	        $this->targetClient->flavorAsset,
	        $targetObjects->flavorAssets,
	        $this->sourceObjects->flavorAssets,
	        $jobData->providerData->distributedFlavorAssets,
	        $targetEntryId
        );
        
        
        // sync flavor content
        KalturaLog::debug('Syncing flavor assets content...');
        $this->syncAssetsContent(
            $this->targetClient->flavorAsset,
            $targetObjects->flavorAssetsContent,
            $syncedObjects->flavorAssets,
            $jobData->providerData->distributedFlavorAssets,
            $this->sourceObjects->flavorAssets
        );
        	    
	    // sync thumbnail assets
	    KalturaLog::debug('Syncing thumbnail assets...');
	    $syncedObjects->thumbAssets = $this->syncTargetEntryObjects(
	        $this->targetClient->thumbAsset,
	        $targetObjects->thumbAssets,
	        $this->sourceObjects->thumbAssets,
	        $jobData->providerData->distributedThumbAssets,
	        $targetEntryId
        );
        
        // sync thumbnail content
        KalturaLog::debug('Syncing thumbnail assets content...');
        $this->syncAssetsContent(
            $this->targetClient->thumbAsset,
            $targetObjects->thumbAssetsContent,
            $syncedObjects->thumbAssets,
            $jobData->providerData->distributedThumbAssets,
            $this->sourceObjects->thumbAssets
        );
        
        // sync caption assets
        if ($this->distributeCaptions)
        {
            KalturaLog::debug('Syncing caption assets...');
            $targetCaptionClient = KalturaCaptionClientPlugin::get($this->targetClient);
    	    $syncedObjects->captionAssets = $this->syncTargetEntryObjects(
    	        $targetCaptionClient->captionAsset,
    	        $targetObjects->captionAssets,
    	        $this->sourceObjects->captionAssets,
    	        $jobData->providerData->distributedCaptionAssets,
    	        $targetEntryId
            );
            
            
            // sync caption content
            KalturaLog::debug('Syncing caption assets content...');
            $this->syncAssetsContent(
                $targetCaptionClient->captionAsset,
                $targetObjects->captionAssetsContent,
                $syncedObjects->captionAssets,
                $jobData->providerData->distributedCaptionAssets,
                $this->sourceObjects->captionAssets
            );
        }       
        
        
        // sync cue points
        if ($this->distributeCuePoints)
        {
	        foreach ($targetObjects->cuePoints as $cuePoint)
	        {
	            /* @var $cuePoint KalturaCuePoint */
	            $cuePoint->entryId = $targetEntryId;
	        }
            KalturaLog::debug('Syncing cue points...');
            $targetCuePointClient = KalturaCuePointClientPlugin::get($this->targetClient);
    	    $syncedObjects->cuePoints = $this->syncTargetEntryObjects(
    	        $targetCuePointClient->cuePoint,
    	        $targetObjects->cuePoints,
    	        $this->sourceObjects->cuePoints,
    	        $jobData->providerData->distributedCuePoints,
    	        $targetEntryId,
    	        'getCuePointAddArgs'
            );
        }
        
        return $syncedObjects;
	}
		
    
	/* (non-PHPdoc)
     * @see IDistributionEngineSubmit::submit()
     */
    public function submit(KalturaDistributionSubmitJobData $data)
    {
        // initialize
		$this->init($data);
		
    	try {
			// get source entry objects
			$this->sourceObjects = $this->getSourceObjects($data);
			
			// transform source objects to target objects ready for insert
			$targetObjects = $this->transformSourceToTarget($this->sourceObjects, false);
			
			// add objects to target account
			$addedTargetObjects = $this->sync($data, $targetObjects);
			
			// save target entry id
			$data->remoteId = $addedTargetObjects->entry->id;
					
			// get info about distributed objects
			$data = $this->getDistributedMap($data, $addedTargetObjects);
			
			// all done - no need for closer
			KalturaLog::debug('Submit done');
    	}
    	catch (Exception $e)
    	{
    		// if a new target entry was created - delete it before failing distribution
    		if ($this->targetEntryId)
    		{
    			KalturaLog::debug('Deleting partial new target entry ['.$this->targetEntryId.']');
    			// delete entry from target account - may throw an exception
    			try {
	    		    $deleteResult = $this->targetClient->baseEntry->delete($this->targetEntryId);
    			}
    			catch (Exception $ignoredException)
    			{
    			    KalturaLog::err('Failed deleting partial entry ['.$this->targetEntryId.'] - '.$ignoredException->getMessage());
    			}
    		}
    		
    		// delete original exception
    		throw $e;
    	}    	
        
		return true;
    }
    
    

	/* (non-PHPdoc)
     * @see IDistributionEngineUpdate::update()
     */
    public function update(KalturaDistributionUpdateJobData $data)
    {
		// initialize
		$this->init($data);
		
		// cannot update if remoteId is missing
        $targetEntryId = $data->remoteId;
		if (!$targetEntryId) {
		    throw new Exception('Cannot delete remote entry - remote entry ID is empty');
		}
						
		// get source entry objects
		$this->sourceObjects = $this->getSourceObjects($data);
		
		// transform source objects to target objects ready for update
		$targetObjects = $this->transformSourceToTarget($this->sourceObjects, true);
		
		// update objects on the target account
		$updatedTargetObjects = $this->sync($data, $targetObjects);
		
		// get info about distributed objects
		$data = $this->getDistributedMap($data, $updatedTargetObjects);
        
        // all done - no need for closer
		KalturaLog::debug('Update done');
        return true;
    }
      
    
    
	/* (non-PHPdoc)
     * @see IDistributionEngineDelete::delete()
     */
    public function delete(KalturaDistributionDeleteJobData $data)
    {
        // initialize
		$this->init($data);
		
		// cannot delete if remoteId is missing
		$targetEntryId = $data->remoteId;
		if (!$targetEntryId) {
		    throw new Exception('Cannot delete remote entry - remote entry ID is empty');
		}
		
		// delete entry from target account - may throw an exception
	    $deleteResult = $this->targetClient->baseEntry->delete($targetEntryId);
        
        // all done - no need for closer
		KalturaLog::debug('Submit done');
		return true;
    }
    
    
    
    // ----------------
    //  helper methods
    // ----------------
    
    /**
     * Copy an object for later inserting/updating through the API
     * @param unknown_type $sourceObject
     */
    protected function copyObjectForInsertUpdate ($sourceObject)
    {
        $reflect = new ReflectionClass($sourceObject);
        $props = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
        $newObjectClass = get_class($sourceObject);
        $newObject = new $newObjectClass;
        foreach ($props as $prop)
        {
            $docComment = $prop->getDocComment();
            $propReadOnly = preg_match("/\\@readonly/i", $docComment);
            $deprecated = preg_match("/\\DEPRECATED/i", $docComment);
            
            $copyProperty = !$deprecated && !$propReadOnly;
            
            if ($copyProperty) {
                $propertyName = $prop->name;
                $newObject->{$propertyName} = $sourceObject->{$propertyName};
            }
        }
        return $newObject;
    }
    
    
    /**
     * Set to 'null' parameters marked as @insertonly
     * @param $object
     */
    protected function removeInsertOnly($object)
    {
    	$reflect = new ReflectionClass($object);
        $props = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($props as $prop)
        {
            $docComment = $prop->getDocComment();
            $propInsertOnly = preg_match("/\\@insertonly/i", $docComment);
            
            if ($propInsertOnly) {
                $propertyName = $prop->name;
                $object->{$propertyName} = null;
            }
        }
        return $object;
    }
    
    /**
     * @param string $fieldName
     * @return value for field from $this->fieldValues, or null if no value defined
     */
    protected function getValueForField($fieldName)
	{
	    if (isset($this->fieldValues[$fieldName])) {
	        return $this->fieldValues[$fieldName];
	    }
	    return null;
	}
	
	
    protected function toKeyValueArray($apiKeyValueArray)
	{
	    $keyValueArray = array();
        if (count($apiKeyValueArray))
        {
            foreach($apiKeyValueArray as $keyValueObj)
            {
                /* @var $keyValueObj KalturaKeyValue */
			    $keyValueArray[$keyValueObj->key] = $keyValueObj->value;
            }
        }		
	    return $keyValueArray;
	}
	
	
	/**
	 * Transform XML using XSLT
	 * @param string $xmlStr
	 * @param string $xslStr
	 * @return string the result XML
	 */
	protected function transformXml($xmlStr, $xslStr)
	{
	    $xmlObj = new DOMDocument();
	    if (!$xmlObj->loadXML($xmlStr))
	    {
	        throw new Exception('Error loading source XML');
	    }
	    
	    $xslObj = new DOMDocument();
	    if(!$xslObj->loadXML($xslStr))
		{
	        throw new Exception('Error loading XSLT');
		}
	    		
		$proc = new XSLTProcessor;
		$proc->registerPHPFunctions(kXml::getXslEnabledPhpFunctions());
		$proc->importStyleSheet($xslObj);
		
		$resultXmlObj = $proc->transformToDoc($xmlObj);
		if (!$resultXmlObj)
		{
		    throw new Exception('Error transforming XML');
		    return null;
		}
		
		$resultXmlStr = $resultXmlObj->saveXML();
		
		// DEBUG logs
		// KalturaLog::debug('source xml = '.$xmlStr);
		// KalturaLog::debug('xslt = '.$xslStr);
		// KalturaLog::debug('result xml = '.$$resultXmlStr);
		
		return $resultXmlStr;
	}
	
	function log($message)
	{
		KalturaLog::log($message);
	}
		    
}