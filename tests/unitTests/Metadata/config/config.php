<?php
/*
// ===================================================================================================
//                           _  __     _ _
//                          | |/ /__ _| | |_ _  _ _ _ __ _
//                          | ' </ _` | |  _| || | '_/ _` |
//                          |_|\_\__,_|_|\__|\_,_|_| \__,_|
//
// ===================================================================================================
*/

?>
<?php
	class Config
	{
		const SERVER_URL = "";
		
		const PARTNER_ID = "";
		const PARTNER_ADMIN_SECRET = "";
		const PARTNER_USER_ID = 'MetadataProfileUser';
		
		const METADATA_NAME = 'my-profile-unitest-1';
		const METADATA_NAME_2 = 'my-profile-unitest-2';
		const METADATA_XSD_DATA_ADD = '<xsd:schema xmlns:xsd="http://www.w3.org/2001/XMLSchema">
  <xsd:element name="metadata">
    <xsd:complexType>
      <xsd:sequence>
        <xsd:element id="md_EB59D3D4-1FC7-4899-8CC0-3A4E27667DE2" name="MyText1" minOccurs="0" maxOccurs="1" type="textType">
          <xsd:annotation>
            <xsd:documentation></xsd:documentation>
            <xsd:appinfo>
              <label>my text 1</label>
              <key>my text 1</key>
              <searchable>true</searchable>
              <timeControl>false</timeControl>
              <description></description>
            </xsd:appinfo>
          </xsd:annotation>
        </xsd:element>
        <xsd:element id="md_6D950347-409B-27A7-E2DE-3A4E3C6E31E4" name="MyText2" minOccurs="0" maxOccurs="1" type="textType">
          <xsd:annotation>
            <xsd:documentation></xsd:documentation>
            <xsd:appinfo>
              <label>my text 2</label>
              <key>my text 2</key>
              <searchable>true</searchable>
              <timeControl>false</timeControl>
              <description></description>
            </xsd:appinfo>
          </xsd:annotation>
        </xsd:element>
        <xsd:element id="md_AC0B86A2-2D18-91B2-6EE1-3A4E6D1F79CB" name="MyList1" minOccurs="0" maxOccurs="1">
          <xsd:annotation>
            <xsd:documentation></xsd:documentation>
            <xsd:appinfo>
              <label>my list 1</label>
              <key>my list 1</key>
              <searchable>true</searchable>
              <timeControl>false</timeControl>
              <description></description>
            </xsd:appinfo>
          </xsd:annotation>
          <xsd:simpleType>
            <xsd:restriction base="listType">
              <xsd:enumeration value="a"/>
              <xsd:enumeration value="b"/>
              <xsd:enumeration value="c"/>
            </xsd:restriction>
          </xsd:simpleType>
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
</xsd:schema>';
		
		const METADATA_XSD_DATA_UPDATE = '<xsd:schema xmlns:xsd="http://www.w3.org/2001/XMLSchema">
  <xsd:element name="metadata">
    <xsd:complexType>
      <xsd:sequence>
        <xsd:element id="md_EB59D3D4-1FC7-4899-8CC0-3A4E27667DE2" name="MyText1" minOccurs="0" maxOccurs="1" type="textType">
          <xsd:annotation>
            <xsd:documentation/>
            <xsd:appinfo>
              <label>my text 1</label>
              <key>my text 1</key>
              <searchable>true</searchable>
              <timeControl>false</timeControl>
              <description/>
            </xsd:appinfo>
          </xsd:annotation>
        </xsd:element>
        <xsd:element id="md_6D950347-409B-27A7-E2DE-3A4E3C6E31E4" name="MyText2" minOccurs="0" maxOccurs="1" type="textType">
          <xsd:annotation>
            <xsd:documentation/>
            <xsd:appinfo>
              <label>my text 2</label>
              <key>my text 2</key>
              <searchable>true</searchable>
              <timeControl>false</timeControl>
              <description/>
            </xsd:appinfo>
          </xsd:annotation>
        </xsd:element>
        <xsd:element id="md_AC0B86A2-2D18-91B2-6EE1-3A4E6D1F79CB" name="MyList1" minOccurs="0" maxOccurs="1">
          <xsd:annotation>
            <xsd:documentation></xsd:documentation>
            <xsd:appinfo>
              <label>my list 1</label>
              <key>my list 1</key>
              <searchable>true</searchable>
              <timeControl>false</timeControl>
              <description></description>
            </xsd:appinfo>
          </xsd:annotation>
          <xsd:simpleType>
            <xsd:restriction base="listType">
              <xsd:enumeration value="a"/>
              <xsd:enumeration value="c"/>
            </xsd:restriction>
          </xsd:simpleType>
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
</xsd:schema>';
		
		const ENTRY_ID = 'ENTRY_ID';
		const ENTRY_REFERENCE_ID = 'abc123';
		const ENTRY_METADATA = 'ENTRY_METADATA';
		const ENTRY_METADATA_TRANSFORMED = 'ENTRY_METADATA_TRANSFORMED';
		const METADATA = 'METADATA';
		
		static $metadatas = array (
			1 => array (self::ENTRY_METADATA => '<metadata><MyText1>my test 1</MyText1><MyText2>my test 1</MyText2><MyList1>a</MyList1></metadata>',
						self::ENTRY_METADATA_TRANSFORMED => '<?xml version="1.0" encoding="iso-8859-1"?>
<metadata>
  <MyText1>my test 1</MyText1>
  <MyText2>my test 1</MyText2>
  <MyList1>a</MyList1>
</metadata>'),
			2 => array (self::ENTRY_METADATA => '<metadata><MyText1>my test 2</MyText1><MyText2>my test 2</MyText2><MyList1>b</MyList1></metadata>',
						self::ENTRY_METADATA_TRANSFORMED => '<?xml version="1.0" encoding="iso-8859-1"?>
<metadata>
  <MyText1>my test 2</MyText1>
  <MyText2>my test 2</MyText2>
</metadata>'),
			3 => array (self::ENTRY_METADATA => '<metadata><MyText1>my test 3</MyText1><MyText2>my test 3</MyText2></metadata>',
						self::ENTRY_METADATA_TRANSFORMED => '<?xml version="1.0" encoding="iso-8859-1"?>
<metadata>
  <MyText1>my test 3</MyText1>
  <MyText2>my test 3</MyText2>
</metadata>'),
		);
		
		static $metadatasByProfileSystemName = array (
			self::METADATA_NAME => '<?xml version="1.0"?><metadata><MyText1>my text 1</MyText1><MyText2>My new text 2</MyText2><MyList1>a</MyList1></metadata>',
			self::METADATA_NAME_2 => '<metadata><MyText1>my new metadata</MyText1><MyList1>a</MyList1></metadata>');
		
		
		const BULK_UPLOAD_FILE = 'testsData/bulkUpload.xml';
	
	}

