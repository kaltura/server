<?php
class WidevinePackagerResponse
{
/*	Examples:
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
	private $name;
	private $status; 
	private $errorText;
	private $requestId;
	private $id;
	private $assetId;
	
	private static $PACKAGE_ERROR_STATUSES = array('error' => 'error', 'importFailed' => 'importFailed', 'processingFailed' => 'processingFailed', 
											'exportFailed' => 'exportFailed', 'unknown' => 'unknown', 'packageDeleteFailed' => 'packageDeleteFailed');
	
	const STATUS_SUCCESS = 'successful';
	
	/**
	 * @return the $assetId
	 */
	public function getAssetId() {
		return $this->assetId;
	}

	/**
	 * @param field_type $assetId
	 */
	public function setAssetId($assetId) {
		$this->assetId = $assetId;
	}

	/**
	 * @return the $name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return the $status
	 */
	public function getStatus() {
		return $this->status;
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
	 * @return the $id
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param field_type $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @param field_type $status
	 */
	public function setStatus($status) {
		$this->status = $status;
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

	/**
	 * @param field_type $id
	 */
	public function setId($id) {
		$this->id = $id;
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
	
	protected function setAttribute($attrName, $attrValue)
	{
		if($attrName == 'name')
			$this->setName($attrValue);
		if($attrName == 'id')
			$this->setId($attrValue);
		if($attrName == 'status')
			$this->setStatus($attrValue);
		if($attrName == 'errorText')
			$this->setErrorText($attrValue);
		if($attrName == 'requestId')
			$this->setRequestId($attrValue);
		if($attrName == 'assetid')
			$this->setAssetId($attrValue);
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
