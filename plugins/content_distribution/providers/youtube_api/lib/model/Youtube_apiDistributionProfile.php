<?php
/**
 * @package plugins.youtube_apiDistribution
 * @subpackage model
 */
class Youtube_apiDistributionProfile extends DistributionProfile
{
	const CUSTOM_DATA_USERNAME = 'username';
	const CUSTOM_DATA_PASSWORD = 'password';
	const CUSTOM_DATA_OWNER_NAME = 'ownerName';
	const CUSTOM_DATA_NOTIFICATION_EMAIL = 'notificationEmail';
	const CUSTOM_DATA_DEFAULT_CATEGORY = 'defaultCategory';
	const CUSTOM_DATA_ALLOW_COMMENTS = 'allowComments';
	const CUSTOM_DATA_ALLOW_EMBEDDING = 'allowEmbedding';
	const CUSTOM_DATA_ALLOW_RATINGS = 'allowRatings';
	const CUSTOM_DATA_ALLOW_RESPONSES = 'allowResponses';
	const CUSTOM_DATA_COMMENRCIAL_POLICY = 'commercialPolicy';
	const CUSTOM_DATA_UGC_POLICY = 'ugcPolicy';
	const CUSTOM_DATA_TARGET = 'target';
	const CUSTOM_DATA_METADATA_PROFILE_ID = 'metadataProfileId';

	const METADATA_FIELD_PLAYLIST = 'YouTubePlaylist';
	const METADATA_FIELD_PLAYLISTS = 'YouTubePlaylists';
	
	const ENTRY_NAME_MINIMUM_LENGTH = 1;
	const ENTRY_NAME_MAXIMUM_LENGTH = 60;
	const ENTRY_DESCRIPTION_MINIMUM_LENGTH = 1;
	const ENTRY_DESCRIPTION_MAXIMUM_LENGTH = 175;
	const ENTRY_TAGS_MINIMUM_LENGTH = 1;
	const ENTRY_TAGS_MAXIMUM_LENGTH = 500;
	const ENTRY_EACH_TAG_MANIMUM_LENGTH = 2;
	const ENTRY_EACH_TAG_MAXIMUM_LENGTH = 30;
	
	
	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return Youtube_apiDistributionPlugin::getProvider();
	}
			
	/* (non-PHPdoc)
	 * @see DistributionProfile::validateForSubmission()
	 */
	public function validateForSubmission(EntryDistribution $entryDistribution, $action)
	{
		$validationErrors = parent::validateForSubmission($entryDistribution, $action);
		$entry = entryPeer::retrieveByPK($entryDistribution->getEntryId());
		if(!$entry)
		{
			KalturaLog::err("Entry [" . $entryDistribution->getEntryId() . "] not found");
			$validationErrors[] = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, 'entry', 'entry not found');
			return $validationErrors;
		}
		
		// validate entry name minumum length of 1 character
		if(strlen($entry->getName()) < self::ENTRY_NAME_MINIMUM_LENGTH)
		{
			$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, entryPeer::NAME, '');
			$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_TOO_SHORT);
			$validationError->setValidationErrorParam(self::ENTRY_NAME_MINIMUM_LENGTH);
			$validationErrors[] = $validationError;
		}
		
		// validate entry name maximum length of 60 characters
		if(strlen($entry->getName()) > self::ENTRY_NAME_MAXIMUM_LENGTH)
		{
			$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, entryPeer::NAME, '');
			$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_TOO_LONG);
			$validationError->setValidationErrorParam(self::ENTRY_NAME_MAXIMUM_LENGTH);
			$validationErrors[] = $validationError;
		}
		
		// validate entry description minumum length of 1 character
		if(strlen($entry->getDescription()) < self::ENTRY_DESCRIPTION_MINIMUM_LENGTH)
		{
			$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, entryPeer::DESCRIPTION, '');
			$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_TOO_SHORT);
			$validationError->setValidationErrorParam(self::ENTRY_DESCRIPTION_MINIMUM_LENGTH);
			$validationErrors[] = $validationError;
		}
		
		// validate entry description maximum length of 60 characters
		if(strlen($entry->getDescription()) > self::ENTRY_DESCRIPTION_MAXIMUM_LENGTH)
		{
			$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, entryPeer::DESCRIPTION, '');
			$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_TOO_LONG);
			$validationError->setValidationErrorParam(self::ENTRY_DESCRIPTION_MAXIMUM_LENGTH);
			$validationErrors[] = $validationError;
		}
		
		// validate entry tags minimum length of 1 character
		if(strlen($entry->getTags()) < self::ENTRY_TAGS_MINIMUM_LENGTH)
		{
			$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, entryPeer::TAGS, '');
			$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_TOO_SHORT);
			$validationError->setValidationErrorParam(self::ENTRY_TAGS_MINIMUM_LENGTH);
			$validationErrors[] = $validationError;
		}
		
		// validate entry tags maximum length of 60 characters
		if(strlen($entry->getTags()) > self::ENTRY_TAGS_MAXIMUM_LENGTH)
		{
			$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, entryPeer::TAGS, '');
			$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_TOO_LONG);
			$validationError->setValidationErrorParam(self::ENTRY_TAGS_MAXIMUM_LENGTH);
			$validationErrors[] = $validationError;
		}
		
		// validate each tag length between 2 and 30 characters
		$tags = explode(',', $entry->getTags());
		foreach($tags as &$tag)
			$tag = trim($tag);
			
		foreach($tags as $tag)
		{
			if (strlen($tag) < self::ENTRY_EACH_TAG_MANIMUM_LENGTH)
			{
				$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, 'Each tag', $tag);
				$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_TOO_SHORT);
				$validationError->setValidationErrorParam(self::ENTRY_EACH_TAG_MANIMUM_LENGTH);
				$validationErrors[] = $validationError;
			}
			
			if (strlen($tag) > self::ENTRY_EACH_TAG_MAXIMUM_LENGTH)
			{
				$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, 'Each tag', $tag);
				$validationError->setValidationErrorType(DistributionValidationErrorType::STRING_TOO_LONG);
				$validationError->setValidationErrorParam(self::ENTRY_EACH_TAG_MAXIMUM_LENGTH);
				$validationErrors[] = $validationError;
			}
		}
		
		return $validationErrors;
	}

	public function getUsername()				{return $this->getFromCustomData(self::CUSTOM_DATA_USERNAME);}
	public function getPassword()				{return $this->getFromCustomData(self::CUSTOM_DATA_PASSWORD);}
	public function getOwnerName()				{return $this->getFromCustomData(self::CUSTOM_DATA_OWNER_NAME);}
	public function getNotificationEmail()		{return $this->getFromCustomData(self::CUSTOM_DATA_NOTIFICATION_EMAIL);}
	public function getDefaultCategory()		{return $this->getFromCustomData(self::CUSTOM_DATA_DEFAULT_CATEGORY);}
	public function getAllowComments()			{return $this->getFromCustomData(self::CUSTOM_DATA_ALLOW_COMMENTS);}
	public function getAllowEmbedding()			{return $this->getFromCustomData(self::CUSTOM_DATA_ALLOW_EMBEDDING);}
	public function getAllowRatings()			{return $this->getFromCustomData(self::CUSTOM_DATA_ALLOW_RATINGS);}
	public function getAllowResponses()			{return $this->getFromCustomData(self::CUSTOM_DATA_ALLOW_RESPONSES);}
	public function getCommercialPolicy()		{return $this->getFromCustomData(self::CUSTOM_DATA_COMMENRCIAL_POLICY);}
	public function getUgcPolicy()				{return $this->getFromCustomData(self::CUSTOM_DATA_UGC_POLICY);}
	public function getTarget()					{return $this->getFromCustomData(self::CUSTOM_DATA_TARGET);}
	public function getMetadataProfileId()		{return $this->getFromCustomData(self::CUSTOM_DATA_METADATA_PROFILE_ID);}
	
	public function setUsername($v)				{$this->putInCustomData(self::CUSTOM_DATA_USERNAME, $v);}
	public function setPassword($v)				{$this->putInCustomData(self::CUSTOM_DATA_PASSWORD, $v);}
	public function setOwnerName($v)			{$this->putInCustomData(self::CUSTOM_DATA_OWNER_NAME, $v);}
	public function setNotificationEmail($v)	{$this->putInCustomData(self::CUSTOM_DATA_NOTIFICATION_EMAIL, $v);}
	public function setDefaultCategory($v)		{$this->putInCustomData(self::CUSTOM_DATA_DEFAULT_CATEGORY, $v);}
	public function setAllowComments($v)		{$this->putInCustomData(self::CUSTOM_DATA_ALLOW_COMMENTS, $v);}
	public function setAllowEmbedding($v)		{$this->putInCustomData(self::CUSTOM_DATA_ALLOW_EMBEDDING, $v);}
	public function setAllowRatings($v)			{$this->putInCustomData(self::CUSTOM_DATA_ALLOW_RATINGS, $v);}
	public function setAllowResponses($v)		{$this->putInCustomData(self::CUSTOM_DATA_ALLOW_RESPONSES, $v);}
	public function setCommercialPolicy($v)		{$this->putInCustomData(self::CUSTOM_DATA_COMMENRCIAL_POLICY, $v);}
	public function setUgcPolicy($v)			{$this->putInCustomData(self::CUSTOM_DATA_UGC_POLICY, $v);}
	public function setTarget($v)				{$this->putInCustomData(self::CUSTOM_DATA_TARGET, $v);}
	public function setMetadataProfileId($v)	{$this->putInCustomData(self::CUSTOM_DATA_METADATA_PROFILE_ID, $v);}}