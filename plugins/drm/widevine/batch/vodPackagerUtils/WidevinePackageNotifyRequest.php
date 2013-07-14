<?php
class WidevinePackageNotifyRequest extends WidevineVodBaseRequest
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
	
	private $sourceUrl; 
	private $targetUrl;
	private $outputFileName;
	private $files; 
	
	public function __construct($packageName, $sourceFolder, $targetFolder, $outputFileName, array $files, $portal = null)
	{
		parent::__construct($portal);
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
		return $this->name;
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
	 * @param field_type $packageName
	 */
	public function setPackageName($packageName) {
		$this->name = $packageName;
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
		$packageNotifyXml->addAttribute('licenseStartDate', $this->getLicenseStartDate());			
		$packageNotifyXml->addAttribute('licenseEndDate', $this->getLicenseEndDate());
		if($this->getPolicy())
			$packageNotifyXml->addAttribute('policy', $this->getPolicy());

		return $packageNotifyXml->asXML();
	}
}