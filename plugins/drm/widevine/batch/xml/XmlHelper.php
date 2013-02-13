<?php

class XmlHelper
{
	const XML_OPENING_ELM = '<';
	const XML_CLOSING_ELM = '/>';
	
	const PACKAGE_NOTIFY_REQUEST_ROOT_NODE = 'PackageNotify';
	const PACKAGE_QUERY_REQUEST_ROOT_NODE = 'PackageQuery';
	const SOURCE_FILES_NODE = 'SourceFiles';
	const FILE_NODE = 'File';
	const NAME_ATTR = 'name';
	const OWNER_ATTR = 'owner';
	const PROVIDER_ATTR = 'provider';
	const SOURCE_URL_ATTR = 'sourceUrl';
	const TARGET_URL_ATTR = 'targetUrl';
	const POLICY_ATTR = 'policy';
	const OUTPUT_FILE_ATTR = 'outputFile';
	const LSTART_DATE_ATTR = 'licenseStartDate';
	const LEND_DATE_ATTR = 'licenseEndDate';
	const SIZE_ATTR = 'size';
	const STATUS_ATTR = 'status';
	const ERROR_TEXT_ATTR = 'errorText';
	const REQUEST_ID_ATTR = 'requestId';
	const ID_ATTR = 'id';
	const ASSET_ID_ATTR = 'assetid';

/*	
	"<PackageNotify 
	name='file5_2_package' 
	owner='kaltura' 
	provider='kaltura' 
	sourceUrl='file:///home/yulir/packages/package5/' 
	targetUrl='file:///home/yulir/packages/completed' 
	policy='default' 
	outputFile='file5_2.wvm'
	licenseStartDate=''
	licenseEndDate=''> 
		<SourceFiles>
			<File name='file.mp4' size='6487487' UseForTrickPlay='y'/>
		</SourceFiles>
	</PackageNotify>"
	
	<PackageNotifyResponse 
		name=’entry1_asset1’ 
		owner=’kaltura’ 
		provider=’kaltura’ 
		id=’1234’> 
	</PackageNotifyResponse>
	
	<PackageQuery
		name=”package_name”
	</PackageQuery>
	
	<PackageQueryResponse
		name=’package1’
		owner=’widevine’
		provider=’widevine’
		assetid=’1000421’
		id=’1234’
		status=’processing’>
	</PackageQueryResponse>
*/
	
	public static function constructPackageNotifyRequestXml(PackageNotifyRequest $input)
	{
		$packageNotifyXml = new SimpleXMLElement(self::XML_OPENING_ELM.self::PACKAGE_NOTIFY_REQUEST_ROOT_NODE.self::XML_CLOSING_ELM);
		$sourceFilesNode = $packageNotifyXml->addChild(self::SOURCE_FILES_NODE);
		foreach ($input->getFiles() as $file) 
		{
    		$fileNode = $sourceFilesNode->addChild(self::FILE_NODE);
    		$fileNode->addAttribute(self::NAME_ATTR, $file);
		}
		$packageNotifyXml->addAttribute(self::NAME_ATTR, $input->getPackageName());
		$packageNotifyXml->addAttribute(self::OWNER_ATTR, $input->getOwner());
		$packageNotifyXml->addAttribute(self::PROVIDER_ATTR, $input->getProvider());
		$packageNotifyXml->addAttribute(self::SOURCE_URL_ATTR, $input->getSourceUrl());
		$packageNotifyXml->addAttribute(self::TARGET_URL_ATTR, $input->getTargetUrl());
		$packageNotifyXml->addAttribute(self::OUTPUT_FILE_ATTR, $input->getOutputFileName());
		if($input->getPolicy())
			$packageNotifyXml->addAttribute(self::POLICY_ATTR, $input->getPolicy());
		if($input->getLicenseStartDate() && $input->getLicenseEndDate())
		{		
			$packageNotifyXml->addAttribute(self::LSTART_DATE_ATTR, $input->getLicenseStartDate());
			$packageNotifyXml->addAttribute(self::LEND_DATE_ATTR, $input->getLicenseEndDate());
		}
		return $packageNotifyXml;
	}
	
	public static function constructPackageQueryRequestXml($packageName)
	{
		$packageQueryXml = new SimpleXMLElement(self::XML_OPENING_ELM.self::PACKAGE_QUERY_REQUEST_ROOT_NODE.self::XML_CLOSING_ELM);
		$packageQueryXml->addAttribute(self::NAME_ATTR, $packageName);
		
		return $packageQueryXml;
	}
	
	public static function parsePackagerResponse($responseStr)
	{
		$responseXml = new SimpleXMLElement($responseStr);
		$responseObject = new PackagerResponse();
		foreach($responseXml->attributes() as $attribute => $value)
		{
			$responseObject->setAttribute($attribute, "$value");
		}	
		return $responseObject;
	}
}