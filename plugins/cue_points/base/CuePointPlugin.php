<?php
/**
 * Enable time based cue point objects management on entry objects
 * @package plugins.cuePoint
 */
class CuePointPlugin extends KalturaPlugin implements IKalturaServices, IKalturaPermissions, IKalturaEventConsumers, IKalturaVersion, IKalturaEnumerator, IKalturaSchemaContributor, IKalturaSchemaDefiner, IKalturaMrssContributor, IKalturaSearchDataContributor
{
	const PLUGIN_NAME = 'cuePoint';
	const PLUGIN_VERSION_MAJOR = 1;
	const PLUGIN_VERSION_MINOR = 0;
	const PLUGIN_VERSION_BUILD = 0;
	const CUE_POINT_MANAGER = 'kCuePointManager';
	const SEARCH_FIELD_DATA = 'data';
	const SEARCH_TEXT_SUFFIX = 'cpend';
	const ENTRY_CUE_POINT_INDEX_PREFIX = 'cps_';
	const ENTRY_CUE_POINT_INDEX_SUFFIX = 'cpe_';
	const ENTRY_CUE_POINT_INDEX_SUB_TYPE = 'cpst';
	
	const CUE_POINT_FETCH_LIMIT = 1000;
	
	
	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaVersion::getVersion()
	 */
	public static function getVersion()
	{
		return new KalturaVersion(
			self::PLUGIN_VERSION_MAJOR,
			self::PLUGIN_VERSION_MINOR,
			self::PLUGIN_VERSION_BUILD
		);
	}

	/* (non-PHPdoc)
	 * @see IKalturaPermissions::isAllowedPartner()
	 */
	public static function isAllowedPartner($partnerId)
	{
		$partner = PartnerPeer::retrieveByPK($partnerId);
		return $partner->getPluginEnabled(self::PLUGIN_NAME);
	}

	/* (non-PHPdoc)
	 * @see IKalturaServices::getServicesMap()
	 */
	public static function getServicesMap()
	{
		$map = array(
			'cuePoint' => 'CuePointService',
			'liveCuePoint' => 'LiveCuePointService',
		);
		return $map;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaEventConsumers::getEventConsumers()
	 */
	public static function getEventConsumers()
	{
		return array(
			self::CUE_POINT_MANAGER,
		);
	}

	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('CuePointSchemaType', 'CuePointObjectFeatureType');
		
		if($baseEnumName == 'SchemaType')
			return array('CuePointSchemaType');
			
		if($baseEnumName == 'ObjectFeatureType')
			return array('CuePointObjectFeatureType');
			
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaSchemaContributor::contributeToSchema()
	 */
	public static function contributeToSchema($type)
	{
		$coreType = kPluginableEnumsManager::apiToCore('SchemaType', $type);
		if($coreType != SchemaType::SYNDICATION)
			return null;
			
		
		$xsd = '
		
	<!-- ' . self::getPluginName() . ' -->
	
	<xs:complexType name="T_scenes">
		<xs:sequence>
			<xs:element ref="scene" minOccurs="1" maxOccurs="unbounded">
				<xs:annotation>
					<xs:documentation>Cue point element</xs:documentation>
				</xs:annotation>
			</xs:element>
		</xs:sequence>
	</xs:complexType>	
	
	<xs:complexType name="T_scene" abstract="true">
		<xs:sequence>
			<xs:element name="sceneStartTime" minOccurs="1" maxOccurs="1" type="xs:time">
				<xs:annotation>
					<xs:documentation>Cue point start time</xs:documentation>
				</xs:annotation>
			</xs:element>
			<xs:element name="createdAt" minOccurs="1" maxOccurs="1" type="xs:dateTime">
				<xs:annotation>
					<xs:documentation>Cue point creation date</xs:documentation>
				</xs:annotation>
			</xs:element>
			<xs:element name="updatedAt" minOccurs="1" maxOccurs="1" type="xs:dateTime">
				<xs:annotation>
					<xs:documentation>Cue point last update date</xs:documentation>
				</xs:annotation>
			</xs:element>
			<xs:element name="userId" minOccurs="0" maxOccurs="1" type="xs:string">
				<xs:annotation>
					<xs:documentation>Cue point owner user id</xs:documentation>
				</xs:annotation>
			</xs:element>
			<xs:element ref="tags" minOccurs="0" maxOccurs="1">
				<xs:annotation>
					<xs:documentation>Cue point searchable keywords</xs:documentation>
				</xs:annotation>
			</xs:element>
		</xs:sequence>
		
		<xs:attribute name="sceneId" use="required">
			<xs:annotation>
				<xs:documentation>ID of cue point to apply update/delete action on</xs:documentation>
			</xs:annotation>
			<xs:simpleType>
				<xs:restriction base="xs:string">
					<xs:maxLength value="250"/>
				</xs:restriction>
			</xs:simpleType>
		</xs:attribute>
		<xs:attribute name="systemName" use="optional">
			<xs:annotation>
				<xs:documentation>System name of cue point to apply update/delete action on</xs:documentation>
			</xs:annotation>
			<xs:simpleType>
				<xs:restriction base="xs:string">
					<xs:maxLength value="120"/>
				</xs:restriction>
			</xs:simpleType>
		</xs:attribute>
		
	</xs:complexType>
	
	<xs:element name="scenes" type="T_scenes" substitutionGroup="item-extension">
		<xs:annotation>
			<xs:documentation>Cue points wrapper</xs:documentation>
			<xs:appinfo>
				<example>
					<scenes>
						<scene-ad-cue-point entryId="{entry id}" systemName="MY_AD_CUE_POINT_SYSTEM_NAME">...</scene-ad-cue-point>
						<scene-annotation entryId="{entry id}" systemName="MY_ANNOTATION_PARENT_SYSTEM_NAME">...</scene-annotation>
						<scene-annotation entryId="{entry id}">...</scene-annotation>
						<scene-code-cue-point entryId="{entry id}">...</scene-code-cue-point>
						<scene-thumb-cue-point entryId="{entry id}">...</scene-thumb-cue-point>
					</scenes>
				</example>
			</xs:appinfo>
		</xs:annotation>
	</xs:element>
	
	<xs:element name="scene" type="T_scene">
		<xs:annotation>
			<xs:documentation>
				Base cue point element<br/>
				Is abstract and cannot be used<br/>
				Use the extended elements only
			</xs:documentation>
		</xs:annotation>
	</xs:element>
	
	<xs:element name="scene-extension" />
		';
		
		return $xsd;
	}

	/* (non-PHPdoc)
	 * @see IKalturaMrssContributor::contribute()
	 */
	public function contribute(BaseObject $object, SimpleXMLElement $mrss, kMrssParameters $mrssParams = null)
	{
		if(!($object instanceof entry))
			return;
		
		$cuePoints = CuePointPeer::retrieveByEntryId($object->getId());
		if(!count($cuePoints))
			return;
		
		$scenes = $mrss->addChild('scenes');
		kCuePointManager::syndicate($cuePoints, $scenes);
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaSchemaContributor::contributeToSchema()
	 */
	public static function getPluginSchema($type)
	{
		$coreType = kPluginableEnumsManager::apiToCore('SchemaType', $type);
		
		if($coreType == self::getSchemaTypeCoreValue(CuePointSchemaType::INGEST_API))
			return new SimpleXMLElement(file_get_contents(dirname(__FILE__) . '/xml/ingestion.xsd'));
			
		if($coreType == self::getSchemaTypeCoreValue(CuePointSchemaType::SERVE_API))
			return new SimpleXMLElement(file_get_contents(dirname(__FILE__) . '/xml/serve.xsd'));
			
		return null;
	}
		
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getSchemaTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('SchemaType', $value);
	}
	
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getObjectFeatureTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('ObjectFeatureType', $value);
	}
	
	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaMrssContributor::getObjectFeatureType()
	 */
	public function getObjectFeatureType ()
	{
		return self::getObjectFeatureTypeCoreValue(CuePointObjectFeatureType::CUE_POINT);
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaSearchDataContributor::getSearchData()
	 */
	public static function getSearchData(BaseObject $object)
	{
		if($object instanceof entry && self::isAllowedPartner($object->getPartnerId()))
			return self::getCuePointSearchData($object);
			
		return null;
	}
	
	public static function getCuePointSearchData(entry $entry)
	{
		$indexOnEntryTypes = self::getIndexOnEntryTypes();
		if(!count($indexOnEntryTypes))
			return;
		
		CuePointPeer::setUseCriteriaFilter(false);
		$cuePointsCount = CuePointPeer::countByEntryIdAndTypes($entry->getId(), $indexOnEntryTypes);
		CuePointPeer::setUseCriteriaFilter(true);
		
		$offset = 0;
		$searchData = '';
		while($offset < $cuePointsCount)
		{
			CuePointPeer::setUseCriteriaFilter(false);
			$cuePointObjects = CuePointPeer::retrieveByEntryIdTypeAndLimit($entry->getId(), self::CUE_POINT_FETCH_LIMIT, $offset, $indexOnEntryTypes);
			CuePointPeer::setUseCriteriaFilter(true);
			
			foreach($cuePointObjects as $cuePoint)
			{
				/* @var $cuePoint CuePoint */
				$contributedData = $cuePoint->contributeData();
					
				if(!$contributedData)
					continue;
			
				$cuePointType = $cuePoint->getType();
					
				$contributedData = self::buildDataToIndexOnEntry($contributedData, $cuePointType, $cuePoint->getPartnerId(), $cuePoint->getId(), $cuePoint->getSubType());
					
				$searchData .= $contributedData . ' ';
			}
			
			$handledObjectsCount = count($cuePointObjects);
			//In case cue point was deleted during index execution than offset will not reach count so breake when count is 0
			if(!$handledObjectsCount)
				break;
			
			$offset += $handledObjectsCount;
		}
		
		$dataField  = CuePointPlugin::getSearchFieldName(CuePointPlugin::SEARCH_FIELD_DATA);
		$searchValues = array(
			$dataField => CuePointPlugin::PLUGIN_NAME . "_" . $entry->getPartnerId() . ' ' . $searchData . ' ' . CuePointPlugin::SEARCH_TEXT_SUFFIX
		);
		
		return $searchValues;
	}
	
	public static function buildDataToIndexOnEntry($contributedData, $type, $partnerId, $cuePointId, $subType = null)
	{	
		$prefix = self::ENTRY_CUE_POINT_INDEX_PREFIX . $partnerId . "_" . $type;
		
		if($subType)
			$prefix .= " " . self::ENTRY_CUE_POINT_INDEX_SUB_TYPE . $subType;
		
		$suffix = self::ENTRY_CUE_POINT_INDEX_SUFFIX . $partnerId . "_" . $type;
			
		return $cuePointId . " " . $prefix . " " . $contributedData . $suffix;
	}
	
	public static function getIndexOnEntryTypes()
	{
		$indexOnEntryTypes = array();
		
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaCuePoint');
		foreach ($pluginInstances as $pluginInstance)
		{
			$currIndexOnEntryTypes = $pluginInstance::getTypesToIndexOnEntry();
			
			$indexOnEntryTypes = array_merge($indexOnEntryTypes, $currIndexOnEntryTypes);
		}
		
		return $indexOnEntryTypes;
	}
	
	/**
	 * return field name as appears in index schema
	 * @param string $fieldName
	 */
	public static function getSearchFieldName($fieldName){
		if ($fieldName == self::SEARCH_FIELD_DATA)
			return  'plugins_data';
			
		return CuePointPlugin::getPluginName() . '_' . $fieldName;
	}
}
