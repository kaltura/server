<?php

require_once (__DIR__ . DIRECTORY_SEPARATOR . 'Constants.php');
require_once (__DIR__ . DIRECTORY_SEPARATOR . 'RuleEngine.php');

/**
 * @package plugins.openCalaisReachVendor
 * @subpackage batch
 */
class KReachVendorTaskOpenCalaisProcessorEngine extends KReachVendorTaskProcessorEngine
{
    const OPEN_CALAIS_URL = 'https://api-eit.refinitiv.com/permid/calais';

    const OPEN_CALAIS_API_KEY_METADATA_PROFILE_SYS_NAME = 'OpenCalais_PartnerData';
    const OPEN_CALAIS_API_KEY_METADATA_FIELD_NAME = 'OpenCalaisAPIKey';
    const OMIT_OUTPUTTING_ORIGINAL_TEXT_METADATA_FIELD_NAME = 'OmitOutputtingOriginalText';
    const ENABLE_TICKER_EXTRACTION_METADATA_FIELD_NAME = 'EnableTickerExtraction';

    const OPEN_CALAIS_MAPPING_METADATA_PROFILE_SYS_NAME = 'OpenCalais_Mapping';
    const OPEN_CALAIS_DYNAMIC_OBJECT_MAPPING_SYSTEM_NAME = 'OpenCalais_DynamicObjectMapping';
    const OPEN_CALAIS_MAPPING_POSTFIX = '_OpenCalais';

    const SHOWTAXONOMY_SYSTEM_NAME = 'ShowTaxonomy';
    // Mapping metadata constants
    const RULE_NAME = 'Rule';
    const KALTURA_FIELD_NAME_XPATH = '/metadata/Rule/Kaltura/ShowTaxonomyElement';

    const OPEN_CALAIS_TOO_MANY_REQUESTS = 429;

    /**
     * @var array
     */
    protected $targetAllMetadataFields;

    /**
     * @var array
     */
    protected $targetSettableMetadataFields;

    /**
     * @var KalturaMetadataClientPlugin
     */
    protected $metadataPlugin;

    public function __construct()
    {
        parent::__construct();
        $this->metadataPlugin = KalturaMetadataClientPlugin::get(KBatchBase::$kClient);
    }

    /**
     * @inheritDoc
     */
    function handleTask(KalturaEntryVendorTask $vendorTask)
    {
        //Impersonate the target partner
        KBatchBase::impersonate($vendorTask->partnerId);

        try {
            $mappingProfileId = $this->getMappingMetadataProfileId();
            $values = $this->getValuesUsingRuleEngine($vendorTask, $mappingProfileId);

            KalturaLog::info('Rule engine result values: ' . print_r($values, true));
            $isUnique = $this->ensureUniqueness($vendorTask);

            if ($isUnique)
            {
                $this->actionUpdate($values, $vendorTask->entryId);
            }
        }catch (Exception $e)
        {
            KalturaLog::err('An error occurred processing the task: ' . $e->getMessage());
            return $this->endTaskOnError($vendorTask);
        }

        return $this->endTaskSuccess($vendorTask);
    }

    /**
     * @param array $values
     * @param string $entryId
     */
    private function actionUpdate($values, $entryId){
        $this->cleanCuePointsForEntry($entryId);
        $showTaxonomyId = $this->initMainMetadataFields(self::SHOWTAXONOMY_SYSTEM_NAME);
        $showTaxonomyXml = $this->initMainMetadataXml($entryId, $showTaxonomyId);

        foreach ($values as $details){
            if(isset($details['entryMetadataShowTaxonomy'])
            && isset($details['dynamicMetadata']))
            {
                $isValid = $this->validateDynamicObject($details['dynamicMetadata']);
                if (!$isValid)
                {
                    KalturaLog::info ('The following dynamic metadata result was found to be incompatible with the current data on the partner account: ' . print_r($details, true));
                    continue;
                }

                $showTaxonomyXml = $this->updateEntryMetadataXml($details['entryMetadataShowTaxonomy'], $showTaxonomyXml);
            }

            if(isset($details['cuePoints_list'])){
                $this->handleCuePoints($details['cuePoints_list'], $entryId);
            }
        }

        KalturaLog::info ("Set entry metadata to " . $showTaxonomyXml);
        $this->addOrUpdateMetadata($showTaxonomyId, $entryId, KalturaMetadataObjectType::ENTRY, $showTaxonomyXml);
    }

    /**
     * @param string $entryId
     * @param int $showTaxonomyId
     */
    private function initMainMetadataXml ($entryId, $showTaxonomyId)
    {
        $initialXml = $this->retrieveMetadataObjectsByMetadataProfileAndObjectId($showTaxonomyId, KalturaMetadataObjectType::ENTRY, $entryId);
        if ($initialXml->totalCount)
        {
            return $this->clearFieldsInXml($initialXml->objects[0]->xml, $this->targetSettableMetadataFields);
        }

        return '<metadata/>';
    }

    private function initMainMetadataFields ($showTaxonomySystemName)
    {
        $metadataProfile = $this->getMetaDataProfile(self::SHOWTAXONOMY_SYSTEM_NAME);
        $this->getTargetMetadataFields($metadataProfile);

        return $metadataProfile->id;
    }


    /**
     * @param $cuePointsList
     * @param $entryId
     */
    private function handleCuePoints($cuePointsList, $entryId){

        foreach ($cuePointsList as $cuePoint){
            $cuePointObj = new KalturaAnnotation();
            $cuePointObj->text = $cuePoint['title'];
            $cuePointObj->startTime = $cuePoint['startTime'];
            $cuePointObj->entryId = $entryId;

            $this->addCuePoint($cuePointObj);
        }
    }
    /**
     * @param $entryId
     */
    protected function cleanCuePointsForEntry($entryId)
    {
        $cuePointFilter = new KalturaAnnotationFilter();
        $cuePointFilter->entryIdEqual = $entryId;
        $cuePoints = KalturaCuePointClientPlugin::get(KBatchBase::$kClient)->cuePoint->listAction($cuePointFilter);
        if($cuePoints->totalCount > 0){
            /** @var KalturaCuePoint $cuePoint */
            foreach ($cuePoints->objects as $cuePoint)
            {
                KalturaCuePointClientPlugin::get(KBatchBase::$kClient)->cuePoint->delete($cuePoint->id);
            }
        }

    }

    /**
     * @param KalturaThumbCuePoint $cuePointObj
     */
    protected function addCuePoint(KalturaAnnotation $cuePointObj)
    {
        KalturaCuePointClientPlugin::get(KBatchBase::$kClient)->cuePoint->add($cuePointObj);
    }

    /**
     * @param $metadataProfileId
     * @param $objectId
     * @param $objectType
     * @param $xmlData
     * @return Metadata
     */
    protected function addMetadata($metadataProfileId, $objectId, $objectType, $xmlData)
    {
        return $this->metadataPlugin->metadata->add($metadataProfileId, $objectType, $objectId, $xmlData);
    }



    private function getValuesUsingRuleEngine(KalturaEntryVendorTask $vendorTask, $mappingProfileId) {

        try {
            $transcript = $this->getEntryTranscript($vendorTask);
        } catch (Exception $e)
        {
            throw new Exception ('Kaltura data retrieval error: ' . $e->getMessage());
        }

        $response = $this->sendOpenCalaisGetTagsRequest($transcript, $vendorTask->partnerId);


        //Save Open Calais response as attachment asset on the entry
        $this->saveResponseAsAttachment($vendorTask, $response);

        $textCuePointsRelation = $this->getEntryJsonTranscript($vendorTask);
        $ruleEngine = new RuleEngine($this->getMappingMetadataProfileRules($mappingProfileId, $vendorTask->partnerId), $textCuePointsRelation);

        return $ruleEngine->getValuesFromApiResponse($response);
    }

    /**
     * @param KalturaEntryVendorTask $vendorTask
     * @param $response
     * @throws KalturaClientException
     */
    protected function saveResponseAsAttachment (KalturaEntryVendorTask $vendorTask, $response)
    {
        $attachmentAssetFilter = new KalturaAttachmentAssetFilter();
        $attachmentAssetFilter->tagsMultiLikeAnd = 'reach,ocm';
        $attachmentAssetFilter->formatEqual = KalturaAttachmentType::JSON;
        $attachmentAssetFilter->entryIdEqual = $vendorTask->entryId;

        $attachments = KalturaAttachmentClientPlugin::get(KBatchBase::$kClient)->attachmentAsset->listAction($attachmentAssetFilter);
        $attachmentAssetId = null;
        if($attachments->totalCount)
        {
            $attachmentAssetId = $attachments->objects[0]->id;
        }

        if(!$attachmentAssetId)
        {
            $attachmentAsset = new KalturaAttachmentAsset();
            $attachmentAsset->format = KalturaAttachmentType::JSON;
            $attachmentAsset->tags = 'reach,ocm';
            $attachmentAsset->filename = 'ocm_response.json';
            $attachmentAsset->fileExt = 'json';
            $attachmentAsset = KalturaAttachmentClientPlugin::get(KBatchBase::$kClient)->attachmentAsset->add($vendorTask->entryId, $attachmentAsset);
            $attachmentAssetId = $attachmentAsset->id;
        }

        $ocmContentResource = new KalturaStringResource();
        $ocmContentResource->content = $response;

        try {
            $attachmentAsset = KalturaAttachmentClientPlugin::get(KBatchBase::$kClient)->attachmentAsset->setContent($attachmentAssetId, $ocmContentResource);
        } catch (Exception $e)
        {
            KalturaLog::err ("OpenCalais response for entry {$vendorTask->entryId}, vendor task ID {$vendorTask->id} could not be saved as attachment asset for the entry.");
        }

    }

    /**
     * @param $entryId
     * @param $format
     *
     * @return string
     */
    protected function retrieveEntryTranscriptAssetId ($entryId, $format)
    {
        $attachmentAssetFilter = new KalturaAttachmentAssetFilter();
        $attachmentAssetFilter->entryIdEqual = $entryId;
        $attachmentAssetFilter->formatEqual = $format;
        $attachments = KalturaAttachmentClientPlugin::get(KBatchBase::$kClient)->attachmentAsset->listAction($attachmentAssetFilter);

        $transcriptId = null;
        foreach ($attachments->objects as $attachment)
        {
            if ($attachment instanceof KalturaTranscriptAsset)
            {
                $transcriptId = $attachment->id;
            }
        }

        return $transcriptId;
    }

    protected function retrieveAttachmentAssetContent ($assetId)
    {
        KBatchBase::$kClient->setReturnServedResult(true);
        $content = KalturaAttachmentClientPlugin::get(KBatchBase::$kClient)->attachmentAsset->serve($assetId);

        return (string)$content;
    }

    protected function sendOpenCalaisGetTagsRequest ($transcript, $partnerId)
    {
        $retryCounter = 0;
        do
        {
            usleep(750000);
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL, self::OPEN_CALAIS_URL);
            curl_setopt($ch,CURLOPT_POST, true);
            curl_setopt($ch,CURLOPT_POSTFIELDS, $transcript);
            curl_setopt($ch,CURLOPT_HTTPHEADER,$this->getHeaders($partnerId));
            curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

            $response =  curl_exec($ch);
            $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            $retryCounter++;
            curl_close($ch);
        } while($responseCode == self::OPEN_CALAIS_TOO_MANY_REQUESTS && $retryCounter < 2);

        return $response;
    }
    /**
     * @param string $partnerId
     * @return string[]
     * @throws Exception
     */
    private function getHeaders($partnerId) {
        $apiKey = $this->getOpenCalaisApiKey($partnerId);
        $enableTickerExtraction = $this->getEnableTickerExtraction($partnerId);
        $omitOutputtingOriginalText = $this->getOmitOutputtingOriginalText($partnerId);
        $headers = array (
            "Content-Type: text/xml",
            "charset:utf8",
            "x-ag-access-token: $apiKey",
            "outputFormat: application/json",
            "x-calais-language: English"
        );
        if($enableTickerExtraction != ''){
            $headers[] = "x-calais-EnableTickerExtraction: ". ($enableTickerExtraction == 'Yes' ? 'True' : 'False');
        }
        if($omitOutputtingOriginalText != ''){
            $headers[] = "omitOutputtingOriginalText: ". ($omitOutputtingOriginalText == 'Yes' ? 'true' : 'false');
        }
        return $headers;
    }
    /**
     * @param $partnerId
     * @return string
     * @throws Exception
     */
    protected function getOpenCalaisApiKey($partnerId) {
        $xmlData = $this->getSimpleXMLElementFromPartnerMetadata($partnerId);
        if(property_exists($xmlData, self::OPEN_CALAIS_API_KEY_METADATA_FIELD_NAME)) {
            return (string)$xmlData->OpenCalaisAPIKey;
        }
        else
        {
            throw new Exception("Required partner-level custom metadata could not be located.");
        }
    }
    /**
     * @param $partnerId
     * @return string
     * @throws Exception
     */
    protected function getEnableTickerExtraction($partnerId) {
        $xmlData = $this->getSimpleXMLElementFromPartnerMetadata($partnerId);
        if(property_exists($xmlData, self::ENABLE_TICKER_EXTRACTION_METADATA_FIELD_NAME)) {
            return (string)$xmlData->EnableTickerExtraction;
        }
        return '';
    }
    /**
     * @param $partnerId
     * @return string
     * @throws Exception
     */
    protected function getOmitOutputtingOriginalText($partnerId) {
        $xmlData = $this->getSimpleXMLElementFromPartnerMetadata($partnerId);
        if(property_exists($xmlData, self::OMIT_OUTPUTTING_ORIGINAL_TEXT_METADATA_FIELD_NAME)) {
            return (string)$xmlData->OmitOutputtingOriginalText;
        }
        return '';
    }
    /**
     * @return SimpleXMLElement|null
     * @throws Exception
     */
    private function getSimpleXMLElementFromPartnerMetadata($partnerId) {
        static $xmlData = null;
        if($xmlData === null){
            $openCalaisApiKeyProfileId = $this->getMetaDataProfileId(self::OPEN_CALAIS_API_KEY_METADATA_PROFILE_SYS_NAME);
            $openCalaisApiMetadatas = $this->retrieveMetadataObjectsByMetadataProfileAndObjectId($openCalaisApiKeyProfileId, KalturaMetadataObjectType::PARTNER, $partnerId);
            if(!$openCalaisApiMetadatas->totalCount){
                throw new Exception("Required partner-level custom metadata could not be located.");
            }

            /* @var KalturaMetadata $metadataObject */
            $metadataObject = $openCalaisApiMetadatas->objects[0];
            if(empty($metadataObject->xml)){
                throw new Exception("Required partner-level custom metadata could not be located.");
            }

            $xmlData = simplexml_load_string($metadataObject->xml, "SimpleXMLElement");
        }
        return $xmlData;
    }
    /**
     * @param KalturaEntryVendorTask $vendorTask
     * @return string
     */
    protected function getEntryTranscript(KalturaEntryVendorTask $vendorTask) {
        $mediaEntry = $this->getMediaEntry($vendorTask->entryId);
        $text = $this->getEntryTextTranscript($vendorTask);

        return '<Document><Title>' . htmlspecialchars($mediaEntry->name) . '</Title><Description>' . htmlspecialchars($mediaEntry->description) . '</Description><Body>' . htmlspecialchars($text) . '</Body></Document>';
    }


    /**
     * @param KalturaEntryVendorTask $vendorTask
     * @return string
     */
    protected function getEntryTextTranscript(KalturaEntryVendorTask $vendorTask) {

        $transcriptAssetId = $this->retrieveEntryTranscriptAssetId($vendorTask->entryId, KalturaAttachmentType::TEXT);

        if(!$transcriptAssetId)
        {
            return '';
        }

        return $this->retrieveAttachmentAssetContent($transcriptAssetId);
    }

    /**
     * @param KalturaEntryVendorTask $vendorTask
     * @return string
     */
    protected function getEntryJsonTranscript(KalturaEntryVendorTask $vendorTask) {

        if ($vendorTask->taskJobData instanceof KalturaIntelligentTaggingVendorTaskData && $vendorTask->taskJobData->assetId)
        {
            $transcriptAssetId = $vendorTask->taskJobData->assetId;
        }
        else
        {
            $transcriptAssetId = $this->retrieveEntryTranscriptAssetId($vendorTask->entryId, KalturaAttachmentType::JSON);
        }

        return $this->retrieveAttachmentAssetContent($transcriptAssetId);
    }

    /**
     * @param $entryId
     * @return KalturaMediaEntry
     */
    protected function getMediaEntry($entryId) {
        return KBatchBase::$kClient->baseEntry->get($entryId);
    }

    /**
     * @return string
     */
    protected function getMappingMetadataProfileId(){
        return $this->getMetaDataProfileId(self::OPEN_CALAIS_MAPPING_METADATA_PROFILE_SYS_NAME);
    }

    /**
     * @param $systemName
     * @return int
     */
    protected function getMetaDataProfileId($systemName)
    {
        $metadataProfile = $this->getMetadataProfile($systemName);

        return $metadataProfile->id;
    }

    /**
     * @param $systemName
     * @return KalturaMetadataProfile
     */
    protected function getMetadataProfile ($systemName)
    {
        $metadataProfileFilter = new KalturaMetadataProfileFilter();
        $metadataProfileFilter->systemNameEqual = $systemName;
        $mappingProfiles =  $this->metadataPlugin->metadataProfile->listAction($metadataProfileFilter);
        if(!$mappingProfiles->totalCount)
        {
            throw new Exception ("Kaltura data retrieval error: required custom metadata profile with system name $systemName not found.");
        }

        return $mappingProfiles->objects[0];
    }

    /**
     * @param string $mappingProfileId
     * @param int $partnerId
     * @return array
     */
    protected function getMappingMetadataProfileRules($mappingProfileId, $partnerId) {
        $metadataResponse = $this->retrieveMetadataObjectsByMetadataProfileAndObjectId($mappingProfileId, KalturaMetadataObjectType::PARTNER, $partnerId);
        $this->retrieveActiveMetadataFields($metadataResponse->objects[0]->xml);

        $result = array();
        if($metadataResponse->totalCount > 0){
            $xml = json_decode(json_encode(simplexml_load_string($metadataResponse->objects[0]->xml)), true);
            if(isset($xml[self::RULE_NAME]))
            {
                foreach ($xml[self::RULE_NAME] as $rule)
                {
                    $result[] = $rule;
                }
            }
        }

        return $result;
    }

    protected function retrieveActiveMetadataFields ($metadataMapping)
    {
        $xml = new SimpleXMLElement($metadataMapping);

        $nodeList = $xml->xpath(self::KALTURA_FIELD_NAME_XPATH);
        foreach ($nodeList as $node)
        {
            $this->targetSettableMetadataFields[] = strval($node);
        }

        KalturaLog::info ('Settable fields: ' . print_r($this->targetSettableMetadataFields, true));
    }

    /**
     * Method to set up $targetMetadataFields assoc array mapping the target metadata profile fields to their types.
     * For dynamic metadata object type, mapping includes the metadata profile ID.
     * @param KalturaMetadataProfile $metadataProfile
     *
     */
    protected function getTargetMetadataFields (KalturaMetadataProfile $metadataProfile)
    {
        $xml = new SimpleXMLElement($metadataProfile->xsd);

        $fields = $xml->xpath('/xsd:schema/xsd:element/xsd:complexType/xsd:sequence/xsd:element');

        $this->targetAllMetadataFields = array();

        foreach ($fields as $field)
        {
            $fieldName = strval($field->attributes()['name']);
            $fieldType = strval($field->attributes()['type']);

            $this->targetAllMetadataFields[$fieldName] = array();
            $this->targetAllMetadataFields[$fieldName]['type'] = $fieldType;
            if ($fieldType == 'metadataObjectType')
            {
                $metadataProfileIdXpath = "/xsd:schema/xsd:element/xsd:complexType/xsd:sequence/xsd:element[@name='$fieldName']/xsd:annotation/xsd:appinfo/metadataProfileId";

                $this->targetAllMetadataFields[$fieldName]['metadataProfileId'] = intval($xml->xpath($metadataProfileIdXpath)[0]);
            }

        }

        KalturaLog::info("Fields in the target metadata profile XSD: " . print_r($this->targetAllMetadataFields, true));

    }

    protected function placeTagAccordingToXSD ($fieldNameToAdd, DOMDocument $dom, DOMElement $newNode)
    {
        $domXpath = new DOMXPath($dom);

        $lastAutoElement= null;
        $fieldNames = array_keys($this->targetAllMetadataFields);

        for ($i = count($fieldNames)-1; $i >=0; $i--)
        {
            if ($fieldNames[$i] == $fieldNameToAdd)
            {
                $xpath = '//' . $fieldNameToAdd;
                $autoList = $domXpath->query ($xpath);
                if ($autoList->length)
                {
                    $lastAutoElement = $autoList->item ($autoList->length-1);
                    break;
                }
                $fieldNameToAdd = $i >= 1 ? $fieldNames[$i-1] : $fieldNames[0];
            }
        }

        if (!$lastAutoElement)
        {
            $dom->documentElement->insertBefore($newNode, $dom->documentElement->firstChild);
        }
        else {
            KalturaLog::info('Append after: ' . $lastAutoElement->nodeName);
            $lastAutoElement->parentNode->insertBefore($newNode, $lastAutoElement->nextSibling);
        }
    }

    /**
     * Removes specific list of fields from XML
     *
     * @param string $metadata
     * @param array $fieldNames
     */
    protected function clearFieldsInXml($metadata, array $fieldNames)
    {
        $metadataXML = new SimpleXMLElement($metadata);
        foreach ($fieldNames as $fieldName)
        {
            $tags = $metadataXML->xpath("//$fieldName");
            foreach ($tags as $tag)
            {
                unset ($tag[0]);
            }
        }

        return $metadataXML->saveXML();
    }

    /**
     * @param array $dynamicObjectDetails
     * @return bool
     */
    protected function validateDynamicObject (array $dynamicObjectDetails)
    {
        KalturaLog::info ('Validating dynamic object existence with following details: ' . print_r($dynamicObjectDetails, true));
        $metadataProfileFieldName = $dynamicObjectDetails['fieldName'];

        if ($this->targetAllMetadataFields[$metadataProfileFieldName]['type'] == 'metadataObjectType')
        {
            if (!isset ($this->targetAllMetadataFields[$metadataProfileFieldName]['metadataProfileId']))
            {
                KalturaLog::err('Invalid metadata profile ID. Cannot validate.');
                return false;
            }
            $metadataProfileId = $this->targetAllMetadataFields[$metadataProfileFieldName]['metadataProfileId'];

            $objectId = $dynamicObjectDetails['objectId'];
            if (!$objectId)
            {
                KalturaLog::err('Invalid object ID. Cannot validate.');
                return false;
            }

            $objects = $this->retrieveMetadataObjectsByMetadataProfileAndObjectId($metadataProfileId, KalturaMetadataObjectType::DYNAMIC_OBJECT, $objectId);
            if ($objects->totalCount)
            {
                return true;
            }

            if ($dynamicObjectDetails['addIfNotExist'])
            {
                //retrieve metadata profile
                $metadataProfile = $this->metadataPlugin->metadataProfile->get($metadataProfileId);

                $dynamicObjectMetadataMappingProfileId = $this->getMetadataProfileId(self::OPEN_CALAIS_DYNAMIC_OBJECT_MAPPING_SYSTEM_NAME);
                $ocmDynamicObjectMetadataMappingId = $metadataProfile->systemName . self::OPEN_CALAIS_MAPPING_POSTFIX;
                $mappingDynamicObject = $this->retrieveMetadataObjectsByMetadataProfileAndObjectId($dynamicObjectMetadataMappingProfileId, KalturaMetadataObjectType::DYNAMIC_OBJECT, $ocmDynamicObjectMetadataMappingId);

                return $this->createMissingDynamicObject($mappingDynamicObject->objects[0]->xml, $dynamicObjectDetails['fullItem'], $objectId, $metadataProfileId);

            }

            return false;
        }

        return true;
    }

    protected function createMissingDynamicObject($xml, $fullData, $objectId, $targetMetadataProfileId)
    {
        $xmlElement = new SimpleXMLElement($xml);
        $allFields = $xmlElement->xpath('./Field');

        $newObject = new SimpleXMLElement('<metadata/>');
        foreach ($allFields as $field)
        {
            $sourceField = strval($field->xpath('.//Source')[0]);
            $targetField = strval($field->xpath('.//Target')[0]);
            $targetValue = strval($field->xpath('.//Value')[0]);

            $value = null;
            if($sourceField)
            {
                $value = $this->getOcmValueRecursive($fullData, explode('/', $sourceField));
            }
            elseif ($targetValue)
            {
                $value = $targetValue;
            }

            if ($value)
            {
                $newObject->addChild($targetField, $value);
            }
        }

        KalturaLog::info('Creating dynamic metadata object: ' . $newObject->saveXML());

        try {
            $this->metadataPlugin->metadata->add($targetMetadataProfileId, KalturaMetadataObjectType::DYNAMIC_OBJECT, $objectId, $newObject->saveXML());
            return $objectId;
        }
        catch (Exception $e)
        {
            KalturaLog::err("Unable to add new dynamic object with ID $objectId");
            return null;
        }

        return null;
    }

    /**
     * @param mixed $value
     * @param array $keyArray
     */
    private function getOcmValueRecursive ($value, $keyArray)
    {
        if (is_scalar($value))
        {
            return $value;
        }

        $key = array_shift($keyArray);
        if (!isset($value[$key]))
        {
            return null;
        }

        return $this->getOcmValueRecursive($value[$key], $keyArray);
    }

    /**
     * @param array $entryMetadataAdditions
     * @param $initialMetadata
     */
    private function updateEntryMetadataXml(array $entryMetadataAdditions, $initialMetadata) {

        $dom = new KDOMDocument();
        $dom->loadXML($initialMetadata);

        foreach ($entryMetadataAdditions as $key => $value)
        {
            if (!$value)
            {
                KalturaLog::info("The field $key is associated with a blank value. Skipping.");
                continue;
            }

            //Check for duplicates - if this value was already added, skip addition.
            $xpath = new DOMXPath($dom);
            $results = $xpath->query('//'. $key .'[text()=' . $value . ']');
            if($results->length)
            {
                KalturaLog::info("The field $key already exists in the entry metadata with value $value. Skipping.");
                continue;
            }

            $domElement = $dom->createElement($key, $value);
            $this->placeTagAccordingToXSD($key, $dom, $domElement);
        }

        return $dom->saveXML();
    }

    protected function addOrUpdateMetadata ($metadataProfileId, $objectId, $objectType, $xml, $version = null)
    {
        $metadataObjects = $this->retrieveMetadataObjectsByMetadataProfileAndObjectId($metadataProfileId, $objectType, $objectId);
        if ($metadataObjects->totalCount)
        {
            $result = $this->metadataPlugin->metadata->update($metadataObjects->objects[0]->id, $xml, $version);
        }
        else
        {
            $result = $this->metadataPlugin->metadata->add($metadataProfileId, $objectType, $objectId, $xml);
        }

        return $result;
    }

    protected function retrieveMetadataObjectsByMetadataProfileAndObjectId ($metadataProfileId, $objectType, $objectId)
    {
        $metadataFilter = new KalturaMetadataFilter();
        $metadataFilter->metadataProfileIdEqual = $metadataProfileId;
        $metadataFilter->metadataObjectTypeEqual = $objectType;
        $metadataFilter->objectIdEqual = $objectId;

        return $this->metadataPlugin->metadata->listAction($metadataFilter);
    }
}
