<?php
/**
 * @package External
 * @subpackage Marketo
 */

if (!class_exists("LeadKeyRef")) {
/**
 * @package External
 * @subpackage Marketo
 */
class LeadKeyRef {
}}

if (!class_exists("LeadSyncStatus")) {
/**
 * @package External
 * @subpackage Marketo
 */
class LeadSyncStatus {
}}

if (!class_exists("ActivityType")) {
/**
 * @package External
 * @subpackage Marketo
 */
class ActivityType {
}}

if (!class_exists("ForeignSysType")) {
/**
 * @package External
 * @subpackage Marketo
 */
class ForeignSysType {
}}

if (!class_exists("ReqCampSourceType")) {
/**
 * @package External
 * @subpackage Marketo
 */
class ReqCampSourceType {
}}

if (!class_exists("ListKeyType")) {
/**
 * @package External
 * @subpackage Marketo
 */
class ListKeyType {
}}

if (!class_exists("ListOperationType")) {
/**
 * @package External
 * @subpackage Marketo
 */
class ListOperationType {
}}

if (!class_exists("ActivityTypeFilter")) {
/**
 * @package External
 * @subpackage Marketo
 */
class ActivityTypeFilter {
	/**
	 * @access public
	 * @var ArrayOfActivityType
	 */
	public $includeTypes;
	/**
	 * @access public
	 * @var ArrayOfActivityType
	 */
	public $excludeTypes;
}}

if (!class_exists("Attribute")) {
/**
 * @package External
 * @subpackage Marketo
 */
class Attribute {
	/**
	 * @access public
	 * @var string
	 */
	public $attrName;
	/**
	 * @access public
	 * @var string
	 */
	public $attrType;
	/**
	 * @access public
	 * @var string
	 */
	public $attrValue;
}}

if (!class_exists("LeadRecord")) {
/**
 * @package External
 * @subpackage Marketo
 */
class LeadRecord {
	/**
	 * @access public
	 * @var integer
	 */
	public $Id;
	/**
	 * @access public
	 * @var string
	 */
	public $Email;
	/**
	 * @access public
	 * @var string
	 */
	public $ForeignSysPersonId;
	/**
	 * @access public
	 * @var tnsForeignSysType
	 */
	public $ForeignSysType;
	/**
	 * @access public
	 * @var ArrayOfAttribute
	 */
	public $leadAttributeList;
}}

if (!class_exists("ActivityRecord")) {
/**
 * @package External
 * @subpackage Marketo
 */
class ActivityRecord {
	/**
	 * @access public
	 * @var integer
	 */
	public $id;
	/**
	 * @access public
	 * @var dateTime
	 */
	public $activityDateTime;
	/**
	 * @access public
	 * @var string
	 */
	public $activityType;
	/**
	 * @access public
	 * @var string
	 */
	public $mktgAssetName;
	/**
	 * @access public
	 * @var ArrayOfAttribute
	 */
	public $activityAttributes;
	/**
	 * @access public
	 * @var string
	 */
	public $campaign;
	/**
	 * @access public
	 * @var string
	 */
	public $personName;
	/**
	 * @access public
	 * @var string
	 */
	public $mktPersonId;
	/**
	 * @access public
	 * @var string
	 */
	public $foreignSysId;
	/**
	 * @access public
	 * @var string
	 */
	public $orgName;
	/**
	 * @access public
	 * @var string
	 */
	public $foreignSysOrgId;
}}

if (!class_exists("CampaignRecord")) {
/**
 * @package External
 * @subpackage Marketo
 */
class CampaignRecord {
	/**
	 * @access public
	 * @var integer
	 */
	public $id;
	/**
	 * @access public
	 * @var string
	 */
	public $name;
	/**
	 * @access public
	 * @var string
	 */
	public $description;
}}

if (!class_exists("LeadChangeRecord")) {
/**
 * @package External
 * @subpackage Marketo
 */
class LeadChangeRecord {
	/**
	 * @access public
	 * @var integer
	 */
	public $id;
	/**
	 * @access public
	 * @var dateTime
	 */
	public $activityDateTime;
	/**
	 * @access public
	 * @var string
	 */
	public $activityType;
	/**
	 * @access public
	 * @var string
	 */
	public $mktgAssetName;
	/**
	 * @access public
	 * @var ArrayOfAttribute
	 */
	public $activityAttributes;
	/**
	 * @access public
	 * @var string
	 */
	public $campaign;
	/**
	 * @access public
	 * @var string
	 */
	public $mktPersonId;
}}

if (!class_exists("LeadKey")) {
/**
 * @package External
 * @subpackage Marketo
 */
class LeadKey {
	/**
	 * @access public
	 * @var tnsLeadKeyRef
	 */
	public $keyType;
	/**
	 * @access public
	 * @var string
	 */
	public $keyValue;
}}

if (!class_exists("LeadStatus")) {
/**
 * @package External
 * @subpackage Marketo
 */
class LeadStatus {
	/**
	 * @access public
	 * @var LeadKey
	 */
	public $leadKey;
	/**
	 * @access public
	 * @var boolean
	 */
	public $status;
}}

if (!class_exists("ListKey")) {
/**
 * @package External
 * @subpackage Marketo
 */
class ListKey {
	/**
	 * @access public
	 * @var tnsListKeyType
	 */
	public $keyType;
	/**
	 * @access public
	 * @var string
	 */
	public $keyValue;
}}

if (!class_exists("MObjectMetadata")) {
/**
 * @package External
 * @subpackage Marketo
 */
class MObjectMetadata {
	/**
	 * @access public
	 * @var string
	 */
	public $name;
	/**
	 * @access public
	 * @var string
	 */
	public $description;
	/**
	 * @access public
	 * @var boolean
	 */
	public $isCustom;
	/**
	 * @access public
	 * @var boolean
	 */
	public $isVirtual;
	/**
	 * @access public
	 * @var ArrayOfMObjFieldMetadata
	 */
	public $fieldList;
	/**
	 * @access public
	 * @var dateTime
	 */
	public $updatedAt;
}}

if (!class_exists("MObjFieldMetadata")) {
/**
 * @package External
 * @subpackage Marketo
 */
class MObjFieldMetadata {
	/**
	 * @access public
	 * @var string
	 */
	public $name;
	/**
	 * @access public
	 * @var string
	 */
	public $description;
	/**
	 * @access public
	 * @var string
	 */
	public $displayName;
	/**
	 * @access public
	 * @var string
	 */
	public $sourceObject;
	/**
	 * @access public
	 * @var string
	 */
	public $dataType;
	/**
	 * @access public
	 * @var integer
	 */
	public $size;
	/**
	 * @access public
	 * @var boolean
	 */
	public $isReadonly;
	/**
	 * @access public
	 * @var boolean
	 */
	public $isUpdateBlocked;
	/**
	 * @access public
	 * @var boolean
	 */
	public $isName;
	/**
	 * @access public
	 * @var boolean
	 */
	public $isPrimaryKey;
	/**
	 * @access public
	 * @var boolean
	 */
	public $isCustom;
	/**
	 * @access public
	 * @var boolean
	 */
	public $isDynamic;
	/**
	 * @access public
	 * @var string
	 */
	public $dynamicFieldRef;
	/**
	 * @access public
	 * @var dateTime
	 */
	public $updatedAt;
}}

if (!class_exists("StreamPosition")) {
/**
 * @package External
 * @subpackage Marketo
 */
class StreamPosition {
	/**
	 * @access public
	 * @var dateTime
	 */
	public $latestCreatedAt;
	/**
	 * @access public
	 * @var dateTime
	 */
	public $oldestCreatedAt;
	/**
	 * @access public
	 * @var dateTime
	 */
	public $activityCreatedAt;
	/**
	 * @access public
	 * @var string
	 */
	public $offset;
}}

if (!class_exists("SyncStatus")) {
/**
 * @package External
 * @subpackage Marketo
 */
class SyncStatus {
	/**
	 * @access public
	 * @var integer
	 */
	public $leadId;
	/**
	 * @access public
	 * @var tnsLeadSyncStatus
	 */
	public $status;
	/**
	 * @access public
	 * @var string
	 */
	public $error;
}}

if (!class_exists("VersionedItem")) {
/**
 * @package External
 * @subpackage Marketo
 */
class VersionedItem {
	/**
	 * @access public
	 * @var integer
	 */
	public $id;
	/**
	 * @access public
	 * @var string
	 */
	public $name;
	/**
	 * @access public
	 * @var string
	 */
	public $type;
	/**
	 * @access public
	 * @var string
	 */
	public $description;
	/**
	 * @access public
	 * @var dateTime
	 */
	public $timestamp;
}}

if (!class_exists("ParamsDescribeMObject")) {
/**
 * @package External
 * @subpackage Marketo
 */
class ParamsDescribeMObject {
	/**
	 * @access public
	 * @var string
	 */
	public $objectName;
}}

if (!class_exists("ParamsGetCampaignsForSource")) {
/**
 * @package External
 * @subpackage Marketo
 */
class ParamsGetCampaignsForSource {
	/**
	 * @access public
	 * @var tnsReqCampSourceType
	 */
	public $source;
	/**
	 * @access public
	 * @var string
	 */
	public $name;
	/**
	 * @access public
	 * @var boolean
	 */
	public $exactName;
}}

if (!class_exists("ParamsGetLead")) {
/**
 * @package External
 * @subpackage Marketo
 */
class ParamsGetLead {
	/**
	 * @access public
	 * @var LeadKey
	 */
	public $leadKey;
}}

if (!class_exists("ParamsGetLeadActivity")) {
/**
 * @package External
 * @subpackage Marketo
 */
class ParamsGetLeadActivity {
	/**
	 * @access public
	 * @var LeadKey
	 */
	public $leadKey;
	/**
	 * @access public
	 * @var ActivityTypeFilter
	 */
	public $activityFilter;
	/**
	 * @access public
	 * @var StreamPosition
	 */
	public $startPosition;
	/**
	 * @access public
	 * @var integer
	 */
	public $batchSize;
}}

if (!class_exists("ParamsGetLeadChanges")) {
/**
 * @package External
 * @subpackage Marketo
 */
class ParamsGetLeadChanges {
	/**
	 * @access public
	 * @var StreamPosition
	 */
	public $startPosition;
	/**
	 * @access public
	 * @var ActivityTypeFilter
	 */
	public $activityFilter;
	/**
	 * @access public
	 * @var integer
	 */
	public $batchSize;
}}

if (!class_exists("ParamsGetMultipleLeads")) {
/**
 * @package External
 * @subpackage Marketo
 */
class ParamsGetMultipleLeads {
	/**
	 * @access public
	 * @var dateTime
	 */
	public $lastUpdatedAt;
	/**
	 * @access public
	 * @var string
	 */
	public $streamPosition;
	/**
	 * @access public
	 * @var integer
	 */
	public $batchSize;
	/**
	 * @access public
	 * @var string[]
	 */
	public $includeAttributes;
}}

if (!class_exists("ParamsListMObjects")) {
/**
 * @package External
 * @subpackage Marketo
 */
class ParamsListMObjects {
}}

if (!class_exists("ParamsListOperation")) {
/**
 * @package External
 * @subpackage Marketo
 */
class ParamsListOperation {
	/**
	 * @access public
	 * @var tnsListOperationType
	 */
	public $listOperation;
	/**
	 * @access public
	 * @var ListKey
	 */
	public $listKey;
	/**
	 * @access public
	 * @var ArrayOfLeadKey
	 */
	public $listMemberList;
	/**
	 * @access public
	 * @var boolean
	 */
	public $strict;
}}

if (!class_exists("ParamsRequestCampaign")) {
/**
 * @package External
 * @subpackage Marketo
 */
class ParamsRequestCampaign {
	/**
	 * @access public
	 * @var tnsReqCampSourceType
	 */
	public $source;
	/**
	 * @access public
	 * @var integer
	 */
	public $campaignId;
	/**
	 * @access public
	 * @var ArrayOfLeadKey
	 */
	public $leadList;
}}

if (!class_exists("ParamsSyncLead")) {
/**
 * @package External
 * @subpackage Marketo
 */
class ParamsSyncLead {
	/**
	 * @access public
	 * @var LeadRecord
	 */
	public $leadRecord;
	/**
	 * @access public
	 * @var boolean
	 */
	public $returnLead;
	/**
	 * @access public
	 * @var string
	 */
	public $marketoCookie;
}}

if (!class_exists("ParamsSyncMultipleLeads")) {
/**
 * @package External
 * @subpackage Marketo
 */
class ParamsSyncMultipleLeads {
	/**
	 * @access public
	 * @var ArrayOfLeadRecord
	 */
	public $leadRecordList;
	/**
	 * @access public
	 * @var boolean
	 */
	public $dedupEnabled;
}}

if (!class_exists("LeadActivityList")) {
/**
 * @package External
 * @subpackage Marketo
 */
class LeadActivityList {
	/**
	 * @access public
	 * @var integer
	 */
	public $returnCount;
	/**
	 * @access public
	 * @var integer
	 */
	public $remainingCount;
	/**
	 * @access public
	 * @var StreamPosition
	 */
	public $newStartPosition;
	/**
	 * @access public
	 * @var ArrayOfActivityRecord
	 */
	public $activityRecordList;
}}

if (!class_exists("ResultDescribeMObject")) {
/**
 * @package External
 * @subpackage Marketo
 */
class ResultDescribeMObject {
	/**
	 * @access public
	 * @var MObjectMetadata
	 */
	public $metadata;
}}

if (!class_exists("ResultGetCampaignsForSource")) {
/**
 * @package External
 * @subpackage Marketo
 */
class ResultGetCampaignsForSource {
	/**
	 * @access public
	 * @var integer
	 */
	public $returnCount;
	/**
	 * @access public
	 * @var ArrayOfCampaignRecord
	 */
	public $campaignRecordList;
}}

if (!class_exists("ResultGetLeadChanges")) {
/**
 * @package External
 * @subpackage Marketo
 */
class ResultGetLeadChanges {
	/**
	 * @access public
	 * @var integer
	 */
	public $returnCount;
	/**
	 * @access public
	 * @var integer
	 */
	public $remainingCount;
	/**
	 * @access public
	 * @var StreamPosition
	 */
	public $newStartPosition;
	/**
	 * @access public
	 * @var ArrayOfLeadChangeRecord
	 */
	public $leadChangeRecordList;
}}

if (!class_exists("ResultGetLead")) {
/**
 * @package External
 * @subpackage Marketo
 */
class ResultGetLead {
	/**
	 * @access public
	 * @var integer
	 */
	public $count;
	/**
	 * @access public
	 * @var ArrayOfLeadRecord
	 */
	public $leadRecordList;
}}

if (!class_exists("ResultListMObjects")) {
/**
 * @package External
 * @subpackage Marketo
 */
class ResultListMObjects {
	/**
	 * @access public
	 * @var string[]
	 */
	public $objects;
}}

if (!class_exists("ResultListOperation")) {
/**
 * @package External
 * @subpackage Marketo
 */
class ResultListOperation {
	/**
	 * @access public
	 * @var boolean
	 */
	public $success;
	/**
	 * @access public
	 * @var ArrayOfLeadStatus
	 */
	public $statusList;
}}

if (!class_exists("ResultGetMultipleLeads")) {
/**
 * @package External
 * @subpackage Marketo
 */
class ResultGetMultipleLeads {
	/**
	 * @access public
	 * @var integer
	 */
	public $returnCount;
	/**
	 * @access public
	 * @var integer
	 */
	public $remainingCount;
	/**
	 * @access public
	 * @var string
	 */
	public $newStreamPosition;
	/**
	 * @access public
	 * @var ArrayOfLeadRecord
	 */
	public $leadRecordList;
}}

if (!class_exists("ResultRequestCampaign")) {
/**
 * @package External
 * @subpackage Marketo
 */
class ResultRequestCampaign {
	/**
	 * @access public
	 * @var boolean
	 */
	public $success;
}}

if (!class_exists("ResultSyncLead")) {
/**
 * @package External
 * @subpackage Marketo
 */
class ResultSyncLead {
	/**
	 * @access public
	 * @var integer
	 */
	public $leadId;
	/**
	 * @access public
	 * @var tnsLeadSyncStatus
	 */
	public $syncStatus;
	/**
	 * @access public
	 * @var LeadRecord
	 */
	public $leadRecord;
}}

if (!class_exists("ResultSyncMultipleLeads")) {
/**
 * @package External
 * @subpackage Marketo
 */
class ResultSyncMultipleLeads {
	/**
	 * @access public
	 * @var ArrayOfSyncStatus
	 */
	public $syncStatusList;
}}

if (!class_exists("SuccessDescribeMObject")) {
/**
 * @package External
 * @subpackage Marketo
 */
class SuccessDescribeMObject {
	/**
	 * @access public
	 * @var ResultDescribeMObject
	 */
	public $result;
}}

if (!class_exists("SuccessGetCampaignsForSource")) {
/**
 * @package External
 * @subpackage Marketo
 */
class SuccessGetCampaignsForSource {
	/**
	 * @access public
	 * @var ResultGetCampaignsForSource
	 */
	public $result;
}}

if (!class_exists("SuccessGetLead")) {
/**
 * @package External
 * @subpackage Marketo
 */
class SuccessGetLead {
	/**
	 * @access public
	 * @var ResultGetLead
	 */
	public $result;
}}

if (!class_exists("SuccessGetLeadActivity")) {
/**
 * @package External
 * @subpackage Marketo
 */
class SuccessGetLeadActivity {
	/**
	 * @access public
	 * @var LeadActivityList
	 */
	public $leadActivityList;
}}

if (!class_exists("SuccessGetLeadChanges")) {
/**
 * @package External
 * @subpackage Marketo
 */
class SuccessGetLeadChanges {
	/**
	 * @access public
	 * @var ResultGetLeadChanges
	 */
	public $result;
}}

if (!class_exists("SuccessGetMultipleLeads")) {
/**
 * @package External
 * @subpackage Marketo
 */
class SuccessGetMultipleLeads {
	/**
	 * @access public
	 * @var ResultGetMultipleLeads
	 */
	public $result;
}}

if (!class_exists("SuccessListMObjects")) {
/**
 * @package External
 * @subpackage Marketo
 */
class SuccessListMObjects {
	/**
	 * @access public
	 * @var ResultListMObjects
	 */
	public $result;
}}

if (!class_exists("SuccessListOperation")) {
/**
 * @package External
 * @subpackage Marketo
 */
class SuccessListOperation {
	/**
	 * @access public
	 * @var ResultListOperation
	 */
	public $result;
}}

if (!class_exists("SuccessRequestCampaign")) {
/**
 * @package External
 * @subpackage Marketo
 */
class SuccessRequestCampaign {
	/**
	 * @access public
	 * @var ResultRequestCampaign
	 */
	public $result;
}}

if (!class_exists("SuccessSyncLead")) {
/**
 * @package External
 * @subpackage Marketo
 */
class SuccessSyncLead {
	/**
	 * @access public
	 * @var ResultSyncLead
	 */
	public $result;
}}

if (!class_exists("SuccessSyncMultipleLeads")) {
/**
 * @package External
 * @subpackage Marketo
 */
class SuccessSyncMultipleLeads {
	/**
	 * @access public
	 * @var ResultSyncMultipleLeads
	 */
	public $result;
}}

if (!class_exists("AuthenticationHeaderInfo")) {
/**
 * @package External
 * @subpackage Marketo
 */
class AuthenticationHeaderInfo {
	/**
	 * @access public
	 * @var string
	 */
	public $mktowsUserId;
	/**
	 * @access public
	 * @var string
	 */
	public $requestSignature;
	/**
	 * @access public
	 * @var string
	 */
	public $requestTimestamp;
	/**
	 * @access public
	 * @var string
	 */
	public $audit;
	/**
	 * @access public
	 * @var integer
	 */
	public $mode;
}}

if (!class_exists("MktMktowsApiService")) {
/**
 * @package External
 * @subpackage Marketo
 */
class MktMktowsApiService extends SoapClient {
	/**
	 * Default class map for wsdl=>php
	 * @access private
	 * @var array
	 */
	private static $classmap = array(
		"LeadKeyRef" => "LeadKeyRef",
		"LeadSyncStatus" => "LeadSyncStatus",
		"ActivityType" => "ActivityType",
		"ForeignSysType" => "ForeignSysType",
		"ReqCampSourceType" => "ReqCampSourceType",
		"ListKeyType" => "ListKeyType",
		"ListOperationType" => "ListOperationType",
		"ActivityTypeFilter" => "ActivityTypeFilter",
		"Attribute" => "Attribute",
		"LeadRecord" => "LeadRecord",
		"ActivityRecord" => "ActivityRecord",
		"CampaignRecord" => "CampaignRecord",
		"LeadChangeRecord" => "LeadChangeRecord",
		"LeadKey" => "LeadKey",
		"LeadStatus" => "LeadStatus",
		"ListKey" => "ListKey",
		"MObjectMetadata" => "MObjectMetadata",
		"MObjFieldMetadata" => "MObjFieldMetadata",
		"StreamPosition" => "StreamPosition",
		"SyncStatus" => "SyncStatus",
		"VersionedItem" => "VersionedItem",
		"ParamsDescribeMObject" => "ParamsDescribeMObject",
		"ParamsGetCampaignsForSource" => "ParamsGetCampaignsForSource",
		"ParamsGetLead" => "ParamsGetLead",
		"ParamsGetLeadActivity" => "ParamsGetLeadActivity",
		"ParamsGetLeadChanges" => "ParamsGetLeadChanges",
		"ParamsGetMultipleLeads" => "ParamsGetMultipleLeads",
		"ParamsListMObjects" => "ParamsListMObjects",
		"ParamsListOperation" => "ParamsListOperation",
		"ParamsRequestCampaign" => "ParamsRequestCampaign",
		"ParamsSyncLead" => "ParamsSyncLead",
		"ParamsSyncMultipleLeads" => "ParamsSyncMultipleLeads",
		"LeadActivityList" => "LeadActivityList",
		"ResultDescribeMObject" => "ResultDescribeMObject",
		"ResultGetCampaignsForSource" => "ResultGetCampaignsForSource",
		"ResultGetLeadChanges" => "ResultGetLeadChanges",
		"ResultGetLead" => "ResultGetLead",
		"ResultListMObjects" => "ResultListMObjects",
		"ResultListOperation" => "ResultListOperation",
		"ResultGetMultipleLeads" => "ResultGetMultipleLeads",
		"ResultRequestCampaign" => "ResultRequestCampaign",
		"ResultSyncLead" => "ResultSyncLead",
		"ResultSyncMultipleLeads" => "ResultSyncMultipleLeads",
		"SuccessDescribeMObject" => "SuccessDescribeMObject",
		"SuccessGetCampaignsForSource" => "SuccessGetCampaignsForSource",
		"SuccessGetLead" => "SuccessGetLead",
		"SuccessGetLeadActivity" => "SuccessGetLeadActivity",
		"SuccessGetLeadChanges" => "SuccessGetLeadChanges",
		"SuccessGetMultipleLeads" => "SuccessGetMultipleLeads",
		"SuccessListMObjects" => "SuccessListMObjects",
		"SuccessListOperation" => "SuccessListOperation",
		"SuccessRequestCampaign" => "SuccessRequestCampaign",
		"SuccessSyncLead" => "SuccessSyncLead",
		"SuccessSyncMultipleLeads" => "SuccessSyncMultipleLeads",
		"AuthenticationHeaderInfo" => "AuthenticationHeaderInfo",
	);

	/**
	 * Constructor using wsdl location and options array
	 * @param string $wsdl WSDL location for this service
	 * @param array $options Options for the SoapClient
	 */
	public function __construct($wsdl="https://na-g.marketo.com/soap/mktows/1_3?WSDL", $options=array()) {
		foreach(self::$classmap as $wsdlClassName => $phpClassName) {
		    if(!isset($options['classmap'][$wsdlClassName])) {
		        $options['classmap'][$wsdlClassName] = $phpClassName;
		    }
		}
		parent::__construct($wsdl, $options);
	}

	/**
	 * Checks if an argument list matches against a valid argument type list
	 * @param array $arguments The argument list to check
	 * @param array $validParameters A list of valid argument types
	 * @return boolean true if arguments match against validParameters
	 * @throws Exception invalid function signature message
	 */
	public function _checkArguments($arguments, $validParameters) {
		$variables = "";
		foreach ($arguments as $arg) {
		    $type = gettype($arg);
		    if ($type == "object") {
		        $type = get_class($arg);
		    }
		    $variables .= "(".$type.")";
		}
		if (!in_array($variables, $validParameters)) {
		    throw new Exception("Invalid parameter types: ".str_replace(")(", ", ", $variables));
		}
		return true;
	}

	/**
	 * Service Call: describeMObject
	 * Parameter options:
	 * (ParamsDescribeMObject) paramsDescribeMObject
	 * @param mixed,... See function description for parameter options
	 * @return SuccessDescribeMObject
	 * @throws Exception invalid function signature message
	 */
	public function describeMObject($mixed = null) {
		$validParameters = array(
			"(ParamsDescribeMObject)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("describeMObject", $args);
	}


	/**
	 * Service Call: getCampaignsForSource
	 * Parameter options:
	 * (ParamsGetCampaignsForSource) paramsGetCampaignsForSource
	 * @param mixed,... See function description for parameter options
	 * @return SuccessGetCampaignsForSource
	 * @throws Exception invalid function signature message
	 */
	public function getCampaignsForSource($mixed = null) {
		$validParameters = array(
			"(ParamsGetCampaignsForSource)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("getCampaignsForSource", $args);
	}


	/**
	 * Service Call: getLead
	 * Parameter options:
	 * (ParamsGetLead) paramsGetLead
	 * @param mixed,... See function description for parameter options
	 * @return SuccessGetLead
	 * @throws Exception invalid function signature message
	 */
	public function getLead($mixed = null) {
		$validParameters = array(
			"(ParamsGetLead)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("getLead", $args);
	}


	/**
	 * Service Call: getLeadActivity
	 * Parameter options:
	 * (ParamsGetLeadActivity) paramsGetLeadActivity
	 * @param mixed,... See function description for parameter options
	 * @return SuccessGetLeadActivity
	 * @throws Exception invalid function signature message
	 */
	public function getLeadActivity($mixed = null) {
		$validParameters = array(
			"(ParamsGetLeadActivity)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("getLeadActivity", $args);
	}


	/**
	 * Service Call: getLeadChanges
	 * Parameter options:
	 * (ParamsGetLeadChanges) paramsGetLeadChanges
	 * @param mixed,... See function description for parameter options
	 * @return SuccessGetLeadChanges
	 * @throws Exception invalid function signature message
	 */
	public function getLeadChanges($mixed = null) {
		$validParameters = array(
			"(ParamsGetLeadChanges)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("getLeadChanges", $args);
	}


	/**
	 * Service Call: getMultipleLeads
	 * Parameter options:
	 * (ParamsGetMultipleLeads) paramsGetMultipleLeads
	 * @param mixed,... See function description for parameter options
	 * @return SuccessGetMultipleLeads
	 * @throws Exception invalid function signature message
	 */
	public function getMultipleLeads($mixed = null) {
		$validParameters = array(
			"(ParamsGetMultipleLeads)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("getMultipleLeads", $args);
	}


	/**
	 * Service Call: listMObjects
	 * Parameter options:
	 * (ParamsListMObjects) paramsListMObjects
	 * @param mixed,... See function description for parameter options
	 * @return SuccessListMObjects
	 * @throws Exception invalid function signature message
	 */
	public function listMObjects($mixed = null) {
		$validParameters = array(
			"(ParamsListMObjects)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("listMObjects", $args);
	}


	/**
	 * Service Call: listOperation
	 * Parameter options:
	 * (ParamsListOperation) paramsListOperation
	 * @param mixed,... See function description for parameter options
	 * @return SuccessListOperation
	 * @throws Exception invalid function signature message
	 */
	public function listOperation($mixed = null) {
		$validParameters = array(
			"(ParamsListOperation)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("listOperation", $args);
	}


	/**
	 * Service Call: requestCampaign
	 * Parameter options:
	 * (ParamsRequestCampaign) paramsRequestCampaign
	 * @param mixed,... See function description for parameter options
	 * @return SuccessRequestCampaign
	 * @throws Exception invalid function signature message
	 */
	public function requestCampaign($mixed = null) {
		$validParameters = array(
			"(ParamsRequestCampaign)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("requestCampaign", $args);
	}


	/**
	 * Service Call: syncLead
	 * Parameter options:
	 * (ParamsSyncLead) paramsSyncLead
	 * @param mixed,... See function description for parameter options
	 * @return SuccessSyncLead
	 * @throws Exception invalid function signature message
	 */
	public function syncLead($mixed = null) {
		$validParameters = array(
			"(ParamsSyncLead)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("syncLead", $args);
	}


	/**
	 * Service Call: syncMultipleLeads
	 * Parameter options:
	 * (ParamsSyncMultipleLeads) paramsSyncMultipleLeads
	 * @param mixed,... See function description for parameter options
	 * @return SuccessSyncMultipleLeads
	 * @throws Exception invalid function signature message
	 */
	public function syncMultipleLeads($mixed = null) {
		$validParameters = array(
			"(ParamsSyncMultipleLeads)",
		);
		$args = func_get_args();
		$this->_checkArguments($args, $validParameters);
		return $this->__soapCall("syncMultipleLeads", $args);
	}


}}

