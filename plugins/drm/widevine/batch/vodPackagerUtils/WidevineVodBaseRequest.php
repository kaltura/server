<?php

abstract class WidevineVodBaseRequest
{
	const WV_DATE_FORMAT = 'Y-m-d\TH:i:s\Z';
	
	protected $name;
	private $policy;
	private $portal;
	private $licenseStartDate = null;
	private $licenseEndDate = null;
	
	public function __construct($portal = null)
	{
		$this->policy = WidevinePlugin::DEFAULT_POLICY;
		if($portal)
			$this->portal = $portal;
		else
			$this->portal = WidevinePlugin::KALTURA_PROVIDER;
		$this->setLicenseStartDate(null);
		$this->setLicenseEndDate(null);
	}
		
	/**
	 * @return the $policy
	 */
	public function getPolicy() {
		return $this->policy;
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
	
	public function getOwner(){
		return $this->portal;
	}

	public function getProvider(){
		return $this->portal;
	}
	
	/**
	 * @param field_type $policy
	 */
	public function setPolicy($policy) {
		$this->policy = $policy;
	}

	/**
	 * @param field_type $licenseStartDate
	 */
	public function setLicenseStartDate($licenseStartDate) {	
		if(!$licenseStartDate)
		{
			$dt = new DateTime(WidevinePlugin::DEFAULT_LICENSE_START);
			$licenseStartDate = (int) $dt->format('U');
		}	
		$this->licenseStartDate = date(self::WV_DATE_FORMAT, $licenseStartDate);
	}

	/**
	 * @param field_type $licenseEndDate
	 */
	public function setLicenseEndDate($licenseEndDate) {
		if(!$licenseEndDate)
		{
			$dt = new DateTime(WidevinePlugin::DEFAULT_LICENSE_END);
			$licenseEndDate = (int) $dt->format('U');
		}
		$this->licenseEndDate = date(self::WV_DATE_FORMAT, $licenseEndDate);
	}	
	
	public static function sendPostRequest($url, $requestXml)
	{
		KalturaLog::debug('send VOD request, url: '.$url);		
		KalturaLog::debug('request params: '.$requestXml);
		
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $url );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $requestXml);
		
		$response = curl_exec($ch);
		curl_close($ch);
		
		KalturaLog::debug('VOD response: '.$response);
		
		return $response;
	}
}