<?php
class PackageNotifyRequest
{
	const FILE_URL_PREFIX = 'file://';
	const KALTURA_PROVIDER = 'kaltura';
	
	private $packageName;
	private $sourceUrl; 
	private $targetUrl;
	private $outputFileName;
	private $files; 
	private $policy = null;
	private $licenseStartDate = null;
	private $licenseEndDate = null;
	
	public function __construct($packageName, $sourceFolder, $targetFolder, $outputFileName, array $files)
	{
		$this->setPackageName($packageName);
		$this->setSourceUrl($sourceFolder);
		$this->setTargetUrl($targetFolder);
		$this->setOutputFileName($outputFileName);
		$this->setFiles($files);
	}
	
	/**
	 * @return the $packageName
	 */
	public function getPackageName() {
		return $this->packageName;
	}

	/**
	 * @return the $sourceUrl
	 */
	public function getSourceUrl() {
		return $this->sourceUrl;
	}

	/**
	 * @return the $targetUrl
	 */
	public function getTargetUrl() {
		return $this->targetUrl;
	}

	/**
	 * @return the $outputFileName
	 */
	public function getOutputFileName() {
		return $this->outputFileName;
	}

	/**
	 * @return the $files
	 */
	public function getFiles() {
		return $this->files;
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
		return self::KALTURA_PROVIDER;
	}

	public function getProvider(){
		return self::KALTURA_PROVIDER;
	}
	
	/**
	 * @param field_type $packageName
	 */
	public function setPackageName($packageName) {
		$this->packageName = $packageName;
	}

	/**
	 * @param string $sourceFolder
	 */
	public function setSourceUrl($sourceFolder) {
		$this->sourceUrl = self::FILE_URL_PREFIX.$sourceFolder;
	}

	/**
	 * @param string $targetFolder
	 */
	public function setTargetUrl($targetFolder) {
		$this->targetUrl = self::FILE_URL_PREFIX.$targetFolder;
	}

	/**
	 * @param field_type $outputFileName
	 */
	public function setOutputFileName($outputFileName) {
		$this->outputFileName = $outputFileName;
	}

	/**
	 * @param field_type $files
	 */
	public function setFiles($files) {
		$this->files = $files;
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
		$this->licenseStartDate = $licenseStartDate;
	}

	/**
	 * @param field_type $licenseEndDate
	 */
	public function setLicenseEndDate($licenseEndDate) {
		$this->licenseEndDate = $licenseEndDate;
	}	
}

class PackagerResponse
{
	private $name;
	private $status; 
	private $errorText;
	private $requestId;
	private $id;
	private $assetId;
	
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
 

	public function setAttribute($attrName, $attrValue)
	{
		if($attrName == XmlHelper::NAME_ATTR)
			$this->setName($attrValue);
		if($attrName == XmlHelper::ID_ATTR)
			$this->setId($attrValue);
		if($attrName == XmlHelper::STATUS_ATTR)
			$this->setStatus($attrValue);
		if($attrName == XmlHelper::ERROR_TEXT_ATTR)
			$this->setErrorText($attrValue);
		if($attrName == XmlHelper::REQUEST_ID_ATTR)
			$this->setRequestId($attrValue);
		if($attrName == XmlHelper::ASSET_ID_ATTR)
			$this->setAssetId($attrValue);
	}
}
