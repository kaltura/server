<?php
class WidevineAssetNotifyResponse extends WidevineVodBaseResponse
{
/*	Examples:
	<GetAssetResponse>
		<Asset	
			Id='1610842609'
			Name='0_rv1dvca5_0_wx040bu1'
			Owner='kaltura'>
			<Providers>
				<Provider 
					Name='kaltura' 
					LicenseStartDate='2011-01-01 01:00:00' 
					LicenseEndDate='2015-01-01 01:00:00' 
					Policy='default'/>
			</Providers>
		</Asset>
	</GetAssetResponse>
	
	<RegisterAssetResponse>
		<AssetResponse 	
			StatusCode='1'
			StatusText='OK'
			Id='1610866352'
			Name='0_1099tjc3_0_ub0ikqmw'
			Owner='kaltura' />
	</RegisterAssetResponse>
*/	
	private $statusText;
	private $policy;
	private $owner;
	private $licenseStartDate;
	private $licenseEndDate;
	
	const STATUS_SUCCESS = '1';

	/**
	 * @return the $statusText
	 */
	public function getStatusText() {
		return $this->statusText;
	}

	/**
	 * @return the $owner
	 */
	public function getOwner() {
		return $this->owner;
	}

	/**
	 * @return the $policy
	 */
	public function getPolicy() {
		return $this->policy;
	}
	
	/**
	 * @param field_type $statusText
	 */
	public function setStatusText($statusText) {
		$this->statusText = $statusText;
	}
 
	/**
	 * @param field_type $owner
	 */
	public function setOwner($owner) {
		$this->owner = $owner;
	}
	
	/**
	 * @param field_type $policy
	 */
	public function setPolicy($policy) {
		$this->policy = $policy;
	}
	
	public function isSuccess()
	{
		if($this->getStatus() ==  self::STATUS_SUCCESS)
			return true;
		else
			return false;
	}
		
	/**
	 * @return the $licenseStartDate
	 */
	public function getLicenseStartDate() {
		return $this->licenseStartDate;
	}

	/**
	 * @return the $licenseEndDate
	 */
	public function getLicenseEndDate() {
		return $this->licenseEndDate;
	}

	/**
	 * @param field_type $licenseStartDate
	 */
	public function setLicenseStartDate($licenseStartDate) {
		$this->licenseStartDate = $licenseStartDate;
	}

	/**
	 * @param field_type $licenseEndDate
	 */
	public function setLicenseEndDate($licenseEndDate) {
		$this->licenseEndDate = $licenseEndDate;
	}
	
	public function setStatusCode($status) {
		$this->setStatus($status);
	}

	public static function createWidevineAssetNotifyResponse($responseStr)
	{
		$responseObject = new WidevineAssetNotifyResponse();
		if(!$responseStr)
			return $responseObject;

		try
		{
			$responseXml = new SimpleXMLElement($responseStr);		
			$asset = reset($responseXml->children());
			if(!$asset)
				return $responseObject;
				
			foreach($asset->attributes() as $attribute => $value)
			{
				$responseObject->setAttribute($attribute, "$value");
			}
			if($asset->count())
			{
				$provider = reset(reset($asset->children())->children());
				if(!$provider)
					return $responseObject;
					
				foreach($provider->attributes() as $attribute => $value)
				{
					if($attribute != 'Name')
						$responseObject->setAttribute($attribute, "$value");
				}
			}
		}
		catch (Exception $e)
		{
			$responseObject->setStatus('0');
		}
		return $responseObject;
	}
}
