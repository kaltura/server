<?php
/**
 * @package deployment
 * @subpackage live.liveStream
 *
 * Create email-notification and custom-data profile on partner 99
 *
 * No need to re-run after server code deploy
 */

chdir(__DIR__);
require_once (__DIR__ . '/../../bootstrap.php');

$realRun = isset($argv[1]) && $argv[1] == 'realrun';
KalturaStatement::setDryRun(!$realRun);

const FEATURE_TVINCI_INGEST_BASE = 'FEATURE_TVINCI_INGEST_V';

function createMetadataProfile($version, $versionToXsdMap)
{
	$metadataProfile = new MetadataProfile();
	$metadataProfile->setPartnerId(99);
	$metadataProfile->setStatus(MetadataProfile::STATUS_ACTIVE);
	$metadataProfile->setName('Tvinci ingest v' . $version);
	$metadataProfile->setSystemName('TvinciIngestV' . $version);
	$metadataProfile->setDescription('Tvinci ingest schema version ' . $version);
	$metadataProfile->setObjectType(MetadataObjectType::ENTRY);
	$metadataProfile->setRequiredCopyTemplatePermissions(FEATURE_TVINCI_INGEST_BASE . $version);
	$metadataProfile->save();
	
	$xsdData = $versionToXsdMap[$version];
	
	$key = $metadataProfile->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_DEFINITION);
	kFileSyncUtils::file_put_contents($key, $xsdData);
	
	kMetadataManager::parseProfileSearchFields($metadataProfile->getPartnerId(), $metadataProfile);
}

// Main
$versionToXsdMap = getVersionToXsdMap();
createMetadataProfile(1, $versionToXsdMap);
createMetadataProfile(2, $versionToXsdMap);

// Version => XSD map
function getVersionToXsdMap() {
	return array(
		1 =>	'<xsd:schema xmlns:xsd="http://www.w3.org/2001/XMLSchema">
				  <xsd:element name="metadata">
				    <xsd:complexType>
				      <xsd:sequence>
				        <xsd:element id="md_6F31C638-3D3C-CA83-4201-57106E03E97A" name="WorkflowStatus" minOccurs="0" maxOccurs="1">
				          <xsd:annotation>
				            <xsd:documentation/>
				            <xsd:appinfo>
				              <label>Workflow Status</label>
				              <key>Workflow Status</key>
				              <searchable>true</searchable>
				              <timeControl>false</timeControl>
				              <description/>
				            </xsd:appinfo>
				          </xsd:annotation>
				          <xsd:simpleType>
				            <xsd:restriction base="listType">
				              <xsd:enumeration value="Ready for Review"/>
				              <xsd:enumeration value="Pending Moderation"/>
				              <xsd:enumeration value="Approved"/>
				              <xsd:enumeration value="Rejected"/>
				            </xsd:restriction>
				          </xsd:simpleType>
				        </xsd:element>
				        <xsd:element id="md_F3AF53A5-867D-3906-B10D-B1ED1EFB142E" name="Activate" minOccurs="0" maxOccurs="1">
				          <xsd:annotation>
				            <xsd:documentation/>
				            <xsd:appinfo>
				              <label>Activate</label>
				              <key>Activate Publishing</key>
				              <searchable>true</searchable>
				              <timeControl>false</timeControl>
				              <description/>
				            </xsd:appinfo>
				          </xsd:annotation>
				          <xsd:simpleType>
				            <xsd:restriction base="listType">
				              <xsd:enumeration value="Yes"/>
				              <xsd:enumeration value="No"/>
				            </xsd:restriction>
				          </xsd:simpleType>
				        </xsd:element>
				        <xsd:element id="md_8C73CFAF-154F-41CB-2F59-A7F432C67BF5" name="MediaType" minOccurs="0" maxOccurs="1">
				          <xsd:annotation>
				            <xsd:documentation/>
				            <xsd:appinfo>
				              <label>Media Type</label>
				              <key>Media Type</key>
				              <searchable>true</searchable>
				              <timeControl>false</timeControl>
				              <description/>
				            </xsd:appinfo>
				          </xsd:annotation>
				          <xsd:simpleType>
				            <xsd:restriction base="listType">
				              <xsd:enumeration value="Movie"/>
				              <xsd:enumeration value="Episode"/>
				              <xsd:enumeration value="Linear"/>
				            </xsd:restriction>
				          </xsd:simpleType>
				        </xsd:element>
				        <xsd:element id="md_CD488E62-820A-034B-7424-B1A141433608" name="CatalogStartDate" minOccurs="0" maxOccurs="1" type="dateType">
				          <xsd:annotation>
				            <xsd:documentation/>
				            <xsd:appinfo>
				              <label>Catalog Start Date</label>
				              <key>Catalog Start Date</key>
				              <searchable>false</searchable>
				              <timeControl>false</timeControl>
				              <description/>
				            </xsd:appinfo>
				          </xsd:annotation>
				        </xsd:element>
				        <xsd:element id="md_30142CA5-700C-C98A-9560-A7F695B7D3AC" name="StartDate" minOccurs="0" maxOccurs="1" type="dateType">
				          <xsd:annotation>
				            <xsd:documentation/>
				            <xsd:appinfo>
				              <label>Start Date</label>
				              <key>Start Date</key>
				              <searchable>false</searchable>
				              <timeControl>false</timeControl>
				              <description/>
				            </xsd:appinfo>
				          </xsd:annotation>
				        </xsd:element>
				        <xsd:element id="md_65C0D254-5F55-1083-E230-A7F6DBC79C7A" name="CatalogEndDate" minOccurs="0" maxOccurs="1" type="dateType">
				          <xsd:annotation>
				            <xsd:documentation/>
				            <xsd:appinfo>
				              <label>Catalog End Date</label>
				              <key>Catalog End Date</key>
				              <searchable>false</searchable>
				              <timeControl>false</timeControl>
				              <description/>
				            </xsd:appinfo>
				          </xsd:annotation>
				        </xsd:element>
				        <xsd:element id="md_332D4F2B-3DBF-4C2D-67C4-A7F70DCF2164" name="FinalEndDate" minOccurs="0" maxOccurs="1" type="dateType">
				          <xsd:annotation>
				            <xsd:documentation/>
				            <xsd:appinfo>
				              <label>Final End Date</label>
				              <key>Final End Date</key>
				              <searchable>false</searchable>
				              <timeControl>false</timeControl>
				              <description/>
				            </xsd:appinfo>
				          </xsd:annotation>
				        </xsd:element>
				        <xsd:element id="md_A7F0BA1A-A288-4C9D-7F25-C619651312BC" name="WatchPermissionRule" minOccurs="0" maxOccurs="1">
				          <xsd:annotation>
				            <xsd:documentation/>
				            <xsd:appinfo>
				              <label>Watch Permission Rule</label>
				              <key>Watch Permission Rule</key>
				              <searchable>true</searchable>
				              <timeControl>false</timeControl>
				              <description/>
				            </xsd:appinfo>
				          </xsd:annotation>
				          <xsd:simpleType>
				            <xsd:restriction base="listType">
				              <xsd:enumeration value="Parent allowed"/>
				            </xsd:restriction>
				          </xsd:simpleType>
				        </xsd:element>
				        <xsd:element id="md_5D7F2786-4E07-EE63-9234-C61AF5F0E9B5" name="GeoBlockRule" minOccurs="0" maxOccurs="1">
				          <xsd:annotation>
				            <xsd:documentation/>
				            <xsd:appinfo>
				              <label>Geo block Rule</label>
				              <key>Geo-Block Rule</key>
				              <searchable>true</searchable>
				              <timeControl>false</timeControl>
				              <description/>
				            </xsd:appinfo>
				          </xsd:annotation>
				          <xsd:simpleType>
				            <xsd:restriction base="listType">
				              <xsd:enumeration value="Not Geo-Blocked"/>
				            </xsd:restriction>
				          </xsd:simpleType>
				        </xsd:element>
				        <xsd:element id="md_039BB695-6767-C3EF-2D3F-B2DE5AF9A3B0" name="Runtime" minOccurs="0" maxOccurs="1" type="textType">
				          <xsd:annotation>
				            <xsd:documentation/>
				            <xsd:appinfo>
				              <label>Runtime</label>
				              <key>Runtime</key>
				              <searchable>true</searchable>
				              <timeControl>false</timeControl>
				              <description/>
				            </xsd:appinfo>
				          </xsd:annotation>
				        </xsd:element>
				        <xsd:element id="md_DA79678F-81EF-9311-2678-B2E121561412" name="ReleaseYear" minOccurs="0" maxOccurs="1" type="textType">
				          <xsd:annotation>
				            <xsd:documentation/>
				            <xsd:appinfo>
				              <label>Release Year</label>
				              <key>Release Year</key>
				              <searchable>true</searchable>
				              <timeControl>false</timeControl>
				              <description/>
				            </xsd:appinfo>
				          </xsd:annotation>
				        </xsd:element>
				        <xsd:element id="md_A9FE911A-D211-13F0-6915-B2DF45A8C6B7" name="ReleaseDate" minOccurs="0" maxOccurs="1" type="textType">
				          <xsd:annotation>
				            <xsd:documentation/>
				            <xsd:appinfo>
				              <label>Release Date</label>
				              <key>Release Date</key>
				              <searchable>true</searchable>
				              <timeControl>false</timeControl>
				              <description/>
				            </xsd:appinfo>
				          </xsd:annotation>
				        </xsd:element>
				        <xsd:element id="md_6D08065F-87CB-CAE1-188D-B1A1DA53223A" name="Genre" minOccurs="0" maxOccurs="unbounded" type="textType">
				          <xsd:annotation>
				            <xsd:documentation/>
				            <xsd:appinfo>
				              <label>Genre</label>
				              <key>Genre</key>
				              <searchable>true</searchable>
				              <timeControl>false</timeControl>
				              <description/>
				            </xsd:appinfo>
				          </xsd:annotation>
				        </xsd:element>
				        <xsd:element id="md_A4FA7789-12F9-651C-DE42-B1A219B3805E" name="SubGenre" minOccurs="0" maxOccurs="unbounded" type="textType">
				          <xsd:annotation>
				            <xsd:documentation/>
				            <xsd:appinfo>
				              <label>Sub genre</label>
				              <key>Sub genre</key>
				              <searchable>true</searchable>
				              <timeControl>false</timeControl>
				              <description/>
				            </xsd:appinfo>
				          </xsd:annotation>
				        </xsd:element>
				        <xsd:element id="md_27C92E20-14FD-0379-E016-B1A26A46E9F0" name="Rating" minOccurs="0" maxOccurs="unbounded" type="textType">
				          <xsd:annotation>
				            <xsd:documentation/>
				            <xsd:appinfo>
				              <label>Rating</label>
				              <key>Rating</key>
				              <searchable>true</searchable>
				              <timeControl>false</timeControl>
				              <description/>
				            </xsd:appinfo>
				          </xsd:annotation>
				        </xsd:element>
				        <xsd:element id="md_F259A677-08DC-2D9D-89B5-B1A2A830DD02" name="Country" minOccurs="0" maxOccurs="unbounded" type="textType">
				          <xsd:annotation>
				            <xsd:documentation/>
				            <xsd:appinfo>
				              <label>Country</label>
				              <key>Country</key>
				              <searchable>true</searchable>
				              <timeControl>false</timeControl>
				              <description/>
				            </xsd:appinfo>
				          </xsd:annotation>
				        </xsd:element>
				        <xsd:element id="md_0E15BD77-F6BC-A724-E014-B1A2EA0E6E1F" name="Cast" minOccurs="0" maxOccurs="unbounded" type="textType">
				          <xsd:annotation>
				            <xsd:documentation/>
				            <xsd:appinfo>
				              <label>Cast</label>
				              <key>Cast</key>
				              <searchable>true</searchable>
				              <timeControl>false</timeControl>
				              <description/>
				            </xsd:appinfo>
				          </xsd:annotation>
				        </xsd:element>
				        <xsd:element id="md_10478717-9203-B53B-7D47-B1A31A38C3E3" name="Director" minOccurs="0" maxOccurs="unbounded" type="textType">
				          <xsd:annotation>
				            <xsd:documentation/>
				            <xsd:appinfo>
				              <label>Director</label>
				              <key>Director</key>
				              <searchable>true</searchable>
				              <timeControl>false</timeControl>
				              <description/>
				            </xsd:appinfo>
				          </xsd:annotation>
				        </xsd:element>
				        <xsd:element id="md_EBF66FA2-AEF2-D17E-65FF-B1A365B18B1F" name="AudioLanguage" minOccurs="0" maxOccurs="unbounded" type="textType">
				          <xsd:annotation>
				            <xsd:documentation/>
				            <xsd:appinfo>
				              <label>Audio language</label>
				              <key>Audio language</key>
				              <searchable>true</searchable>
				              <timeControl>false</timeControl>
				              <description/>
				            </xsd:appinfo>
				          </xsd:annotation>
				        </xsd:element>
				        <xsd:element id="md_E97FAD47-7180-9B63-DB91-B1A39B52AEB9" name="Studio" minOccurs="0" maxOccurs="unbounded" type="textType">
				          <xsd:annotation>
				            <xsd:documentation/>
				            <xsd:appinfo>
				              <label>Studio</label>
				              <key>Studio</key>
				              <searchable>true</searchable>
				              <timeControl>false</timeControl>
				              <description/>
				            </xsd:appinfo>
				          </xsd:annotation>
				        </xsd:element>
				      </xsd:sequence>
				    </xsd:complexType>
				  </xsd:element>
				  <xsd:complexType name="textType">
				    <xsd:simpleContent>
				      <xsd:extension base="xsd:string"/>
				    </xsd:simpleContent>
				  </xsd:complexType>
				  <xsd:complexType name="dateType">
				    <xsd:simpleContent>
				      <xsd:extension base="xsd:long"/>
				    </xsd:simpleContent>
				  </xsd:complexType>
				  <xsd:complexType name="objectType">
				    <xsd:simpleContent>
				      <xsd:extension base="xsd:string"/>
				    </xsd:simpleContent>
				  </xsd:complexType>
				  <xsd:simpleType name="listType">
				    <xsd:restriction base="xsd:string"/>
				  </xsd:simpleType>
				</xsd:schema>',
						
		2 =>	'<xsd:schema xmlns:xsd="http://www.w3.org/2001/XMLSchema">
				  <xsd:element name="metadata">
				    <xsd:complexType>
				      <xsd:sequence>
				        <xsd:element id="md_6F31C638-3D3C-CA83-4201-57106E03E97A" name="WorkflowStatus" minOccurs="0" maxOccurs="1">
				          <xsd:annotation>
				            <xsd:documentation></xsd:documentation>
				            <xsd:appinfo>
				              <label>Workflow Status</label>
				              <key>Workflow Status</key>
				              <searchable>true</searchable>
				              <timeControl>false</timeControl>
				              <description></description>
				            </xsd:appinfo>
				          </xsd:annotation>
				          <xsd:simpleType>
				            <xsd:restriction base="listType">
				              <xsd:enumeration value="Ready for Review"/>
				              <xsd:enumeration value="Pending Moderation"/>
				              <xsd:enumeration value="Approved"/>
				              <xsd:enumeration value="Rejected"/>
				            </xsd:restriction>
				          </xsd:simpleType>
				        </xsd:element>
				        <xsd:element id="md_F3AF53A5-867D-3906-B10D-B1ED1EFB142E" name="Activate" minOccurs="0" maxOccurs="1">
				          <xsd:annotation>
				            <xsd:documentation></xsd:documentation>
				            <xsd:appinfo>
				              <label>Activate</label>
				              <key>Activate Publishing</key>
				              <searchable>true</searchable>
				              <timeControl>false</timeControl>
				              <description></description>
				            </xsd:appinfo>
				          </xsd:annotation>
				          <xsd:simpleType>
				            <xsd:restriction base="listType">
				              <xsd:enumeration value="Yes"/>
				              <xsd:enumeration value="No"/>
				            </xsd:restriction>
				          </xsd:simpleType>
				        </xsd:element>
				        <xsd:element id="md_DC5E0A56-F7B0-4BC7-9EB5-84D97AAFF2CA" name="ShortTitle" minOccurs="0" maxOccurs="1" type="textType">
				          <xsd:annotation>
				            <xsd:documentation></xsd:documentation>
				            <xsd:appinfo>
				              <label>Short title</label>
				              <key>Short Title</key>
				              <searchable>true</searchable>
				              <timeControl>false</timeControl>
				              <description></description>
				            </xsd:appinfo>
				          </xsd:annotation>
				        </xsd:element>
				        <xsd:element id="md_8C73CFAF-154F-41CB-2F59-A7F432C67BF5" name="MediaType" minOccurs="0" maxOccurs="1">
				          <xsd:annotation>
				            <xsd:documentation></xsd:documentation>
				            <xsd:appinfo>
				              <label>Media Type</label>
				              <key>Media Type</key>
				              <searchable>true</searchable>
				              <timeControl>false</timeControl>
				              <description></description>
				            </xsd:appinfo>
				          </xsd:annotation>
				          <xsd:simpleType>
				            <xsd:restriction base="listType">
				              <xsd:enumeration value="Movie"/>
				              <xsd:enumeration value="Episode"/>
				              <xsd:enumeration value="Linear"/>
				            </xsd:restriction>
				          </xsd:simpleType>
				        </xsd:element>
				        <xsd:element id="md_CD488E62-820A-034B-7424-B1A141433608" name="CatalogStartDate" minOccurs="0" maxOccurs="1" type="dateType">
				          <xsd:annotation>
				            <xsd:documentation></xsd:documentation>
				            <xsd:appinfo>
				              <label>Catalog Start Date</label>
				              <key>Catalog Start Date</key>
				              <searchable>false</searchable>
				              <timeControl>false</timeControl>
				              <description></description>
				            </xsd:appinfo>
				          </xsd:annotation>
				        </xsd:element>
				        <xsd:element id="md_30142CA5-700C-C98A-9560-A7F695B7D3AC" name="StartDate" minOccurs="0" maxOccurs="1" type="dateType">
				          <xsd:annotation>
				            <xsd:documentation></xsd:documentation>
				            <xsd:appinfo>
				              <label>Start Date</label>
				              <key>Start Date</key>
				              <searchable>false</searchable>
				              <timeControl>false</timeControl>
				              <description></description>
				            </xsd:appinfo>
				          </xsd:annotation>
				        </xsd:element>
				        <xsd:element id="md_65C0D254-5F55-1083-E230-A7F6DBC79C7A" name="CatalogEndDate" minOccurs="0" maxOccurs="1" type="dateType">
				          <xsd:annotation>
				            <xsd:documentation></xsd:documentation>
				            <xsd:appinfo>
				              <label>Catalog End Date</label>
				              <key>Catalog End Date</key>
				              <searchable>false</searchable>
				              <timeControl>false</timeControl>
				              <description></description>
				            </xsd:appinfo>
				          </xsd:annotation>
				        </xsd:element>
				        <xsd:element id="md_332D4F2B-3DBF-4C2D-67C4-A7F70DCF2164" name="FinalEndDate" minOccurs="0" maxOccurs="1" type="dateType">
				          <xsd:annotation>
				            <xsd:documentation></xsd:documentation>
				            <xsd:appinfo>
				              <label>Final End Date</label>
				              <key>Final End Date</key>
				              <searchable>false</searchable>
				              <timeControl>false</timeControl>
				              <description></description>
				            </xsd:appinfo>
				          </xsd:annotation>
				        </xsd:element>
				        <xsd:element id="md_A7F0BA1A-A288-4C9D-7F25-C619651312BC" name="WatchPermissionRule" minOccurs="0" maxOccurs="1">
				          <xsd:annotation>
				            <xsd:documentation></xsd:documentation>
				            <xsd:appinfo>
				              <label>Watch Permission Rule</label>
				              <key>Watch Permission Rule</key>
				              <searchable>true</searchable>
				              <timeControl>false</timeControl>
				              <description></description>
				            </xsd:appinfo>
				          </xsd:annotation>
				          <xsd:simpleType>
				            <xsd:restriction base="listType">
				              <xsd:enumeration value="Parent allowed"/>
				            </xsd:restriction>
				          </xsd:simpleType>
				        </xsd:element>
				        <xsd:element id="md_5D7F2786-4E07-EE63-9234-C61AF5F0E9B5" name="GeoBlockRule" minOccurs="0" maxOccurs="1">
				          <xsd:annotation>
				            <xsd:documentation></xsd:documentation>
				            <xsd:appinfo>
				              <label>Geo block Rule</label>
				              <key>Geo-Block Rule</key>
				              <searchable>true</searchable>
				              <timeControl>false</timeControl>
				              <description></description>
				            </xsd:appinfo>
				          </xsd:annotation>
				          <xsd:simpleType>
				            <xsd:restriction base="listType">
				              <xsd:enumeration value="Not Geo-Blocked"/>
				            </xsd:restriction>
				          </xsd:simpleType>
				        </xsd:element>
				        <xsd:element id="md_039BB695-6767-C3EF-2D3F-B2DE5AF9A3B0" name="Runtime" minOccurs="0" maxOccurs="1" type="textType">
				          <xsd:annotation>
				            <xsd:documentation></xsd:documentation>
				            <xsd:appinfo>
				              <label>Runtime</label>
				              <key>Runtime</key>
				              <searchable>true</searchable>
				              <timeControl>false</timeControl>
				              <description></description>
				            </xsd:appinfo>
				          </xsd:annotation>
				        </xsd:element>
				        <xsd:element id="md_DA79678F-81EF-9311-2678-B2E121561412" name="ReleaseYear" minOccurs="0" maxOccurs="1" type="textType">
				          <xsd:annotation>
				            <xsd:documentation></xsd:documentation>
				            <xsd:appinfo>
				              <label>Release Year</label>
				              <key>Release Year</key>
				              <searchable>true</searchable>
				              <timeControl>false</timeControl>
				              <description></description>
				            </xsd:appinfo>
				          </xsd:annotation>
				        </xsd:element>
				        <xsd:element id="md_A9FE911A-D211-13F0-6915-B2DF45A8C6B7" name="ReleaseDate" minOccurs="0" maxOccurs="1" type="textType">
				          <xsd:annotation>
				            <xsd:documentation></xsd:documentation>
				            <xsd:appinfo>
				              <label>Release Date</label>
				              <key>Release Date</key>
				              <searchable>true</searchable>
				              <timeControl>false</timeControl>
				              <description></description>
				            </xsd:appinfo>
				          </xsd:annotation>
				        </xsd:element>
				        <xsd:element id="md_89E05986-2185-6A2B-2B28-84DAED29E62D" name="Dimension" minOccurs="0" maxOccurs="1" type="textType">
				          <xsd:annotation>
				            <xsd:documentation></xsd:documentation>
				            <xsd:appinfo>
				              <label>Dimension</label>
				              <key>Dimension</key>
				              <searchable>true</searchable>
				              <timeControl>false</timeControl>
				              <description></description>
				            </xsd:appinfo>
				          </xsd:annotation>
				        </xsd:element>
				        <xsd:element id="md_00DDB192-FD8A-FC00-C4A5-84E4E5C24791" name="BillingType" minOccurs="0" maxOccurs="1" type="textType">
				          <xsd:annotation>
				            <xsd:documentation></xsd:documentation>
				            <xsd:appinfo>
				              <label>Billing Type</label>
				              <key>Billing Type</key>
				              <searchable>true</searchable>
				              <timeControl>false</timeControl>
				              <description></description>
				            </xsd:appinfo>
				          </xsd:annotation>
				        </xsd:element>
				        <xsd:element id="md_62E0272E-B0C8-6F14-8668-84EE84A43243" name="ParentalRating" minOccurs="0" maxOccurs="1" type="textType">
				          <xsd:annotation>
				            <xsd:documentation></xsd:documentation>
				            <xsd:appinfo>
				              <label>Parental Rating</label>
				              <key>Parental Rating</key>
				              <searchable>true</searchable>
				              <timeControl>false</timeControl>
				              <description></description>
				            </xsd:appinfo>
				          </xsd:annotation>
				        </xsd:element>
				        <xsd:element id="md_6D08065F-87CB-CAE1-188D-B1A1DA53223A" name="Genre" minOccurs="0" maxOccurs="unbounded" type="textType">
				          <xsd:annotation>
				            <xsd:documentation></xsd:documentation>
				            <xsd:appinfo>
				              <label>Genre</label>
				              <key>Genre</key>
				              <searchable>true</searchable>
				              <timeControl>false</timeControl>
				              <description></description>
				            </xsd:appinfo>
				          </xsd:annotation>
				        </xsd:element>
				        <xsd:element id="md_A4FA7789-12F9-651C-DE42-B1A219B3805E" name="SubGenre" minOccurs="0" maxOccurs="unbounded" type="textType">
				          <xsd:annotation>
				            <xsd:documentation></xsd:documentation>
				            <xsd:appinfo>
				              <label>Sub genre</label>
				              <key>Sub genre</key>
				              <searchable>true</searchable>
				              <timeControl>false</timeControl>
				              <description></description>
				            </xsd:appinfo>
				          </xsd:annotation>
				        </xsd:element>
				        <xsd:element id="md_F259A677-08DC-2D9D-89B5-B1A2A830DD02" name="Country" minOccurs="0" maxOccurs="unbounded" type="textType">
				          <xsd:annotation>
				            <xsd:documentation></xsd:documentation>
				            <xsd:appinfo>
				              <label>Country</label>
				              <key>Country</key>
				              <searchable>true</searchable>
				              <timeControl>false</timeControl>
				              <description></description>
				            </xsd:appinfo>
				          </xsd:annotation>
				        </xsd:element>
				        <xsd:element id="md_C0114D1F-38A7-CC14-3D65-84DCA0133784" name="MainCast" minOccurs="0" maxOccurs="unbounded" type="textType">
				          <xsd:annotation>
				            <xsd:documentation></xsd:documentation>
				            <xsd:appinfo>
				              <label>Main Cast</label>
				              <key>Main Cast</key>
				              <searchable>true</searchable>
				              <timeControl>false</timeControl>
				              <description></description>
				            </xsd:appinfo>
				          </xsd:annotation>
				        </xsd:element>
				        <xsd:element id="md_10478717-9203-B53B-7D47-B1A31A38C3E3" name="Director" minOccurs="0" maxOccurs="unbounded" type="textType">
				          <xsd:annotation>
				            <xsd:documentation></xsd:documentation>
				            <xsd:appinfo>
				              <label>Director</label>
				              <key>Director</key>
				              <searchable>true</searchable>
				              <timeControl>false</timeControl>
				              <description></description>
				            </xsd:appinfo>
				          </xsd:annotation>
				        </xsd:element>
				        <xsd:element id="md_EBF66FA2-AEF2-D17E-65FF-B1A365B18B1F" name="AudioLanguage" minOccurs="0" maxOccurs="unbounded" type="textType">
				          <xsd:annotation>
				            <xsd:documentation></xsd:documentation>
				            <xsd:appinfo>
				              <label>Audio language</label>
				              <key>Audio language</key>
				              <searchable>true</searchable>
				              <timeControl>false</timeControl>
				              <description></description>
				            </xsd:appinfo>
				          </xsd:annotation>
				        </xsd:element>
				        <xsd:element id="md_E97FAD47-7180-9B63-DB91-B1A39B52AEB9" name="Studio" minOccurs="0" maxOccurs="unbounded" type="textType">
				          <xsd:annotation>
				            <xsd:documentation></xsd:documentation>
				            <xsd:appinfo>
				              <label>Studio</label>
				              <key>Studio</key>
				              <searchable>true</searchable>
				              <timeControl>false</timeControl>
				              <description></description>
				            </xsd:appinfo>
				          </xsd:annotation>
				        </xsd:element>
				      </xsd:sequence>
				    </xsd:complexType>
				  </xsd:element>
				  <xsd:complexType name="textType">
				    <xsd:simpleContent>
				      <xsd:extension base="xsd:string"/>
				    </xsd:simpleContent>
				  </xsd:complexType>
				  <xsd:complexType name="dateType">
				    <xsd:simpleContent>
				      <xsd:extension base="xsd:long"/>
				    </xsd:simpleContent>
				  </xsd:complexType>
				  <xsd:complexType name="objectType">
				    <xsd:simpleContent>
				      <xsd:extension base="xsd:string"/>
				    </xsd:simpleContent>
				  </xsd:complexType>
				  <xsd:simpleType name="listType">
				    <xsd:restriction base="xsd:string"/>
				  </xsd:simpleType>
				</xsd:schema>'
	);
}