<?php
class kYouTubeDistributionJobProviderData extends kDistributionJobProviderData
{
	/**
	 * @var string
	 */
	public $sftpDirectory;
	
	/**
	 * @var string
	 */
	public $sftpMetadataFilename;

	/**
	 * @return the $sftpDirectory
	 */
	public function getSftpDirectory() {
		return $this->sftpDirectory;
	}

	/**
	 * @return the $sftpMetadataFilename
	 */
	public function getSftpMetadataFilename() {
		return $this->sftpMetadataFilename;
	}

	/**
	 * @param $sftpDirectory the $sftpDirectory to set
	 */
	public function setSftpDirectory($sftpDirectory) {
		$this->sftpDirectory = $sftpDirectory;
	}

	/**
	 * @param $sftpMetadataFilename the $sftpMetadataFilename to set
	 */
	public function setSftpMetadataFilename($sftpMetadataFilename) {
		$this->sftpMetadataFilename = $sftpMetadataFilename;
	}

	public function __construct(kDistributionJobData $distributionJobData = null)
	{
		parent::__construct($distributionJobData);
	}
}