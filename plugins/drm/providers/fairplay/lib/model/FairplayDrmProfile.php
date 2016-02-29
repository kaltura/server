<?php

/**
* @package plugins.fairplay
* @subpackage model
*/
class FairplayDrmProfile extends DrmProfile
{	
	// ------------------------------------------
	// -- Custom data columns -------------------
	// ------------------------------------------
	
	const CUSTOM_DATA_FAIRPLAY_PUBLIC_CERTIFICATE = 'fairplay_public_certificate';
	
	/**
	 * @return string
	 */
	public function getPublicCertificate()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FAIRPLAY_PUBLIC_CERTIFICATE);
	}
	
	public function setPublicCertificate($publicCertificate)
	{
		$this->putInCustomData(self::CUSTOM_DATA_FAIRPLAY_PUBLIC_CERTIFICATE, $publicCertificate);
	}
}
