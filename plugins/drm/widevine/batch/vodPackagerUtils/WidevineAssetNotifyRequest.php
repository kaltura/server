<?php
class WidevineAssetNotifyRequest extends WidevineVodBaseRequest
{
	/*	Example:
		<RegisterAsset>
			<Asset 
				Name='0_1099tjc3_0_ub0ikqmw' 
				Owner='kaltura' 
				Overwrite='true' >
				<Providers>
					<Provider 
						Name='kaltura' 
						Policy='Case1_basic' 
						LicenseStartDate='2011-01-01T01:00:00Z' 
						LicenseEndDate='2015-01-01T01:00:00Z'/>
				</Providers>
			</Asset>
		</RegisterAsset>
		
		<GetAsset>
			<Asset 
				Id='1610842609' 
				Provider='kaltura' 
				Owner='kaltura'/>
		</GetAsset>
	*/
		
	const REQUEST_GET = 0;
	const REQUEST_REGISTER = 1;
	
	private $requestType;
	private $assetId;
	
	public function __construct($requestType, $portal = null)
	{
		parent::__construct($portal);
		$this->requestType = $requestType;
	}
	
	/**
	 * @return the $assetName
	 */
	public function getAssetName() {
		return $this->name;
	}

	/**
	 * @return the $assetId
	 */
	public function getAssetId() {
		return $this->assetId;
	}
	
	/**
	 * @param field_type $assetName
	 */
	public function setAssetName($assetName) {
		$this->name = $assetName;
	}
	
	/**
	 * @param field_type $assetId
	 */
	public function setAssetId($assetId) {
		$this->assetId = $assetId;
	}
	
	public function createAssetNotifyRequestXml()
	{
		if($this->requestType == self::REQUEST_GET)
			return $this->createAssetGetRequest();
		else if($this->requestType == self::REQUEST_REGISTER)
			return $this->createAssetRegisterRequest();
		else
			return null;
	}
	
	private function createAssetRegisterRequest()
	{
		$assetNotifyXml = new SimpleXMLElement('<RegisterAsset/>');
		$assetNode = $assetNotifyXml->addChild('Asset');
		$assetNode->addAttribute('Name', $this->getAssetName());
		$assetNode->addAttribute('Owner', $this->getOwner());
		$assetNode->addAttribute('Overwrite', 'true');
		$providersNode = $assetNode->addChild('Providers');
		$providerNode = $providersNode->addChild('Provider');		
		$providerNode->addAttribute('Name', $this->getProvider());
		$providerNode->addAttribute('LicenseStartDate', $this->getLicenseStartDate());			
		$providerNode->addAttribute('LicenseEndDate', $this->getLicenseEndDate());
		$providerNode->addAttribute('Policy', $this->getPolicy());

		return $assetNotifyXml->asXML();		
	}
	
	private function createAssetGetRequest()
	{
		$assetGetXml = new SimpleXMLElement('<GetAsset/>');
		$assetNode = $assetGetXml->addChild('Asset');
		$assetNode->addAttribute('Id', $this->getAssetId());
		$assetNode->addAttribute('Owner', $this->getOwner());
		$assetNode->addAttribute('Provider', $this->getProvider());

		return $assetGetXml->asXML();				
	}
}