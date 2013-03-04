<?php
class WidevinePackageNotifyRequest
{
	/*	Example:
	<PackageNotify 
	name='file5_2_package' 
	owner='kaltura' 
	provider='kaltura' 
	sourceUrl='file:///home/packages/package5/' 
	targetUrl='file:///home/packages/completed' 
	policy='default' 
	outputFile='file5_2.wvm'
	licenseStartDate=''
	licenseEndDate=''> 
		<SourceFiles>
			<File name='file.mp4'/>
		</SourceFiles>
	</PackageNotify>
	*/
	
	const FILE_URL_PREFIX = 'file://';
	const WV_DATE_FORMAT = 'Y-m-d\TH:i:s\Z';
	
	private $packageName;
	private $sourceUrl; 
	private $targetUrl;
	private $outputFileName;
	private $files; 
	private $policy;
	private $portal;
	private $licenseStartDate = null;
	private $licenseEndDate = null;
	
	public function __construct($packageName, $sourceFolder, $targetFolder, $outputFileName, array $files)
	{
		$this->setPackageName($packageName);
		$this->setSourceUrl($sourceFolder);
		$this->setTargetUrl($targetFolder);
		$this->setOutputFileName($outputFileName);
		$this->setFiles($files);
		$this->policy = WidevinePlugin::DEFAULT_POLICY;
		$this->portal = WidevinePlugin::getWidevineConfigParam('portal');
		if(!$this->portal)
			$this->portal = WidevinePlugin::KALTURA_PROVIDER;
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
		return $this->portal;
	}

	public function getProvider(){
		return $this->portal;
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
		
		$this->licenseStartDate = date(self::WV_DATE_FORMAT, $licenseStartDate);
	}

	/**
	 * @param field_type $licenseEndDate
	 */
	public function setLicenseEndDate($licenseEndDate) {
		$this->licenseEndDate = date(self::WV_DATE_FORMAT, $licenseEndDate);
	}	
	
	public function createPackageNotifyRequestXml()
	{
		$packageNotifyXml = new SimpleXMLElement('<PackageNotify/>');
		$sourceFilesNode = $packageNotifyXml->addChild('SourceFiles');
		foreach ($this->getFiles() as $file) 
		{
    		$fileNode = $sourceFilesNode->addChild('File');
    		$fileNode->addAttribute('name', $file);
		}
		$packageNotifyXml->addAttribute('name', $this->getPackageName());
		$packageNotifyXml->addAttribute('owner', $this->getOwner());
		$packageNotifyXml->addAttribute('provider', $this->getProvider());
		$packageNotifyXml->addAttribute('sourceUrl', $this->getSourceUrl());
		$packageNotifyXml->addAttribute('targetUrl', $this->getTargetUrl());
		$packageNotifyXml->addAttribute('outputFile', $this->getOutputFileName());
		if($this->getPolicy())
			$packageNotifyXml->addAttribute('policy', $this->getPolicy());
		if($this->getLicenseStartDate() && $this->getLicenseEndDate())
		{		
			$packageNotifyXml->addAttribute('licenseStartDate', $this->getLicenseStartDate());
			$packageNotifyXml->addAttribute('licenseEndDate', $this->getLicenseEndDate());
		}
		return $packageNotifyXml->asXML();
	}
}