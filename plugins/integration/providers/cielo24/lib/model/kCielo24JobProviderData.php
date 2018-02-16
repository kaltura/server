<?php
/**
 * @package plugins.cielo24
 * @subpackage model.data
 */
class kCielo24JobProviderData extends kIntegrationJobProviderData
{
	const CUSTOM_DATA_SPOKEN_LANGUAGE = "spokenLanguage";

	/**
	 * @var string
	 */
	private $entryId;

	/**
	 * @var string
	 */
	private $flavorAssetId;

	/**
	 * @var string
	 */
	private $captionAssetFormats;

	/**
	 * @var string
	 */
	private $priority;

	/**
	 * @var string
	 */
	private $fidelity;

	/**
	 * @var string
	 */
	private $username;

	/**
	 * @var string
	 */
	private $password;

	/**
	 * @var string
	 */
	private $baseUrl;

	/**
	 * @var string
	 */
	private $spokenLanguage;
	
	/**
	 * should replace remote media content
	 * @var bool
	 */
	private $replaceMediaContent;
	
	/**
	 * @var string
	 */
	private $additionalParameters;
	
	/**
	 * @return string
	 */
	public function getEntryId()
	{
		return $this->entryId;
	}
	
	/**
	 * @param string $entryId
	 */
	public function setEntryId($entryId)
	{
		$this->entryId = $entryId;
	}
	
	/**
	 * @return string
	 */
	public function getFlavorAssetId()
	{
		return $this->flavorAssetId;
	}
	
	/**
	 * @param string $flavorAssetId
	 */
	public function setFlavorAssetId($flavorAssetId)
	{
		$this->flavorAssetId = $flavorAssetId;
	}

	/**
	 * @return string
	 */
	public function getCaptionAssetFormats()
	{
		return $this->captionAssetFormats;
	}

	/**
	 * @param string $captionAssetFormats
	 */
	public function setCaptionAssetFormats($captionAssetFormats)
	{
		$this->captionAssetFormats = $captionAssetFormats;
	}

	/**
	 * @return string
	 */
	public function getPriority()
	{
		return $this->priority;
	}

	/**
	 * @param string $priority
	 */
	public function setPriority($priority)
	{
		$this->priority = $priority;
	}

	/**
	 * @return string
	 */
	public function getFidelity()
	{
		return $this->fidelity;
	}

	/**
	 * @param string $fidelity
	 */
	public function setFidelity($fidelity)
	{
		$this->fidelity = $fidelity;
	}

	/**
	 * @return string
	 */
	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * @param string $username
	 */
	public function setUsername($username)
	{
		$this->username = $username;
	}

	/**
	 * @return string
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * @param string $password
	 */
	public function setPassword($password)
	{
		$this->password = $password;
	}

	/**
	 * @return string
	 */
	public function getBaseUrl()
	{
		return $this->baseUrl;
	}

	/**
	 * @param string $baseUrl
	 */
	public function setBaseUrl($baseUrl)
	{
		$this->baseUrl = $baseUrl;
	}

	/**
	 * @return string
	 */
	public function getSpokenLanguage()
	{
		return $this->spokenLanguage;
	}

	/**
	 * @param string $spokenLanguage
	 */
	public function setSpokenLanguage($spokenLanguage)
	{
		$this->spokenLanguage = $spokenLanguage;
	}

	/**
	 * @return bool
	 */
	public function getReplaceMediaContent()
	{
		return $this->replaceMediaContent;
	}

	/**
	 * @param bool $replaceMediaContent
	 */
	public function setReplaceMediaContent($replaceMediaContent)
	{
		$this->replaceMediaContent = $replaceMediaContent;
	}

	/**
	 * @return string
	 */
	public function getAdditionalParameters()
	{
		return $this->additionalParameters;
	}

	/**
	 * @param string $additionalParameters
	 */
	public function setAdditionalParameters($additionalParameters)
	{
		$this->additionalParameters = $additionalParameters;
	}

	/**
	 * kVoicebaseJobProviderData constructor.
	 * The VoiceBase job provider data must include the partner's additional params.
	 */
	public function __construct()
	{
		$partnerOptions = Cielo24Plugin::getPartnerCielo24Options(kCurrentContext::getCurrentPartnerId());

		if($partnerOptions->defaultParams)
			$this->setAdditionalParameters($partnerOptions->defaultParams);
	}
}
