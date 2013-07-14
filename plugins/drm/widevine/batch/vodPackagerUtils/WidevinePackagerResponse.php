<?php
class WidevinePackagerResponse extends WidevineVodBaseResponse
{
/*	Examples:
	<PackageNotifyResponse 
		name='entry1_asset1' 
		owner='kaltura' 
		provider='kaltura' 
		id='1234'> 
	</PackageNotifyResponse>
	
	<PackageQuery
		name='package_name'
	</PackageQuery>
	
	<PackageQueryResponse
		name='package1'
		owner='widevine'
		provider='widevine'
		assetid='1000421'
		id='1234'
		status='processing'>
	</PackageQueryResponse>
*/	
	private $errorText;
	private $requestId;
	private $assetid;
	
	private static $PACKAGE_ERROR_STATUSES = array('error' => 'error', 'importFailed' => 'importFailed', 'processingFailed' => 'processingFailed', 
											'exportFailed' => 'exportFailed', 'unknown' => 'unknown', 'packageDeleteFailed' => 'packageDeleteFailed');
	
	const STATUS_SUCCESS = 'successful';
	
	/**
	 * @return the $assetId
	 */
	public function getAssetid() {
		return $this->assetid;
	}

	/**
	 * @param field_type $assetId
	 */
	public function setAssetid($assetId) {
		$this->assetid = $assetId;
	}

	/**
	 * @return the $errorText
	 */
	public function getErrorText() {
		return $this->errorText;
	}

	/**
	 * @return the $requestId
	 */
	public function getRequestId() {
		return $this->requestId;
	}

	/**
	 * @param field_type $errorText
	 */
	public function setErrorText($errorText) {
		$this->errorText = $errorText;
	}

	/**
	 * @param field_type $requestId
	 */
	public function setRequestId($requestId) {
		$this->requestId = $requestId;
	}

	public function isError()
	{
		if(array_key_exists($this->getStatus(), self::$PACKAGE_ERROR_STATUSES))
			return true;
		else
			return false;
	}
	
	public function isSuccess()
	{
		if($this->getStatus() ==  self::STATUS_SUCCESS)
			return true;
		else
			return false;
	}
	
	public static function createWidevinePackagerResponse($responseStr)
	{
		$responseXml = new SimpleXMLElement($responseStr);
		$responseObject = new WidevinePackagerResponse();
		foreach($responseXml->attributes() as $attribute => $value)
		{
			$responseObject->setAttribute($attribute, "$value");
		}	
		return $responseObject;
	}
}
