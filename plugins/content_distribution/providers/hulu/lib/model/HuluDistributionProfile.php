<?php
/**
 * @package plugins.huluDistribution
 * @subpackage model
 */
class HuluDistributionProfile extends ConfigurableDistributionProfile
{
	const CUSTOM_DATA_SFTP_HOST = 'sftpHost';
	const CUSTOM_DATA_SFTP_LOGIN = 'sftpLogin';
	const CUSTOM_DATA_SFTP_PASS = 'sftpPass';
	const CUSTOM_DATA_SERIES_CHANNEL = 'seriesChannel';
	const CUSTOM_DATA_SERIES_PRIMARY_CATEGORY = 'seriesPrimaryCategory';
	const CUSTOM_DATA_SERIES_ADDITIONAL_CATEGORIES = 'seriesAdditionalCategories';
	const CUSTOM_DATA_SEASON_NUMBER = 'seasonNumber';
	const CUSTOM_DATA_SEASON_SYNOPSIS = 'seasonSynopsis';
	const CUSTOM_DATA_SEASON_TUNE_IN_INFORMATION = 'seasonTuneInInformation';
	const CUSTOM_DATA_VIDEO_MEDIA_TYPE = 'videoMediaType';
	const CUSTOM_DATA_DISABLE_EPISODE_NUMBER_CUSTOM_VALIDATION= 'disableEpisodeNumberCustomValidation';
	const CUSTOM_DATA_ASPERA_HOST = 'asperaHost';
	const CUSTOM_DATA_ASPERA_LOGIN = 'asperaLogin';
	const CUSTOM_DATA_ASPERA_PASS = 'asperaPass';
	const CUSTOM_DATA_ASPERA_PUBLIC_KEY = 'asperaPublicKey';
	const CUSTOM_DATA_ASPERA_PRIVATE_KEY = 'asperaPrivateKey';
	const CUSTOM_DATA_PORT = 'port';
	const CUSTOM_DATA_PASSPHRASE = 'passphrase';
	const CUSTOM_DATA_PROTOCOL = 'protocol';
	
	protected $maxLengthValidation= array (
		HuluDistributionField::SERIES_TITLE => 96,
		HuluDistributionField::SEASON_SYNOPSIS => 2000,
		HuluDistributionField::VIDEO_TITLE => 150,
		HuluDistributionField::VIDEO_DESCRIPTION => 255,
		HuluDistributionField::VIDEO_FULL_DESCRIPTION => 2000,
		HuluDistributionField::VIDEO_COPYRIGHT => 85,
		HuluDistributionField::VIDEO_KEYWORDS => 1024,
	);
	
	protected $inListOrNullValidation = array (
		HuluDistributionField::VIDEO_MEDIA_TYPE => array('TV', 'Film', 'Music Video', 'Web Original', 'Sports'),
		HuluDistributionField::VIDEO_RATING => array(
			// WEB
			'NSFW',
		
			// TV
			'TV-Y', 'TV-Y7', 'TV-Y7-FV', 'TV-G', 'TV-PG', 'TV-14', 'TV-MA',
		
			// Film MPAA Ratings
			'G', 'PG', 'PG-13', 'R', 'NC-17', 'X',
		
			// Video Game ESRB Ratings
			'RP', 'EC', 'E', 'E10+', 'T', 'M', 'AO',
		),
		HuluDistributionField::VIDEO_PROGRAMMING_TYPE => array(
			'Behind the Scenes',
			'Commentary',
			'Concert',
			'Condensed Game',
			'Current Preview',
			'Excerpt',
			'Event',
			'Full Episode',
			'Full Game',
			'Full Movie',
			'Highlights',
			'Interview',
			'Music Video',
			'Outtake',
			'Performance',
			'Recap',
			'Short Film',
			'Sneak Peek',
			'Special',
			'Teaser Trailer',
			'Trailer',
			'Web Exclusive',
		),
		HuluDistributionField::SERIES_PRIMARY_CATEGORY => array(
			'Action and Adventure',
			'Animation',
			'Celebrity and Gossip',
			'College Football',
			'College Sports',
			'Comedy',
			'Crime and Mystery',
			'Documentary and Biography',
			'Drama',
			'Extreme Sports',
			'Family and Kids',
			'Gaming',
			'Horror and Thriller',
			'House and Home',
			'International',
			'Lifestyle and Fashion',
			'Live Events and Specials',
			'Mixed Martial Arts/Fighting',
			'Music',
			'News and Information',
			'Outdoor Sports',
			'Political',
			'Reality and Game Show',
			'Sci Fi and Fantasy',
			'Soap Opera',
			'Sports and Fitness',
			'Talk and Interview',
			'Technology',
			'Travel and Nature',
		)
	);
	
	protected function getDefaultFieldConfigArray()
	{
		$fieldConfigArray = parent::getDefaultFieldConfigArray();
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(HuluDistributionField::SERIES_TITLE);
		$fieldConfig->setUserFriendlyFieldName('Series Title');
		$fieldConfig->setEntryMrssXslt($this->getSeriesTitleXsl());
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(HuluDistributionField::SERIES_DESCRIPTION);
		$fieldConfig->setUserFriendlyFieldName('Series Description');
		$fieldConfig->setEntryMrssXslt($this->getSeriesDescriptionXsl());
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(HuluDistributionField::SERIES_PRIMARY_CATEGORY);
		$fieldConfig->setUserFriendlyFieldName('Series Primary Category');
		$fieldConfig->setEntryMrssXslt($this->getSeriesPrimaryCategoryXsl());
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(HuluDistributionField::SERIES_ADDITIONAL_CATEGORIES);
		$fieldConfig->setUserFriendlyFieldName('Series Additional Categories');
		$fieldConfig->setEntryMrssXslt($this->getSeriesAdditionalCategoriesXsl());
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(HuluDistributionField::SERIES_CHANNEL);
		$fieldConfig->setUserFriendlyFieldName('Series Channel');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/SeriesChannel" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(HuluDistributionField::SEASON_NUMBER);
		$fieldConfig->setUserFriendlyFieldName('Season Number');
		$fieldConfig->setEntryMrssXslt($this->getSeasonNumberXsl());
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(HuluDistributionField::SEASON_SYNOPSIS);
		$fieldConfig->setUserFriendlyFieldName('Season Synopsis');
		$fieldConfig->setEntryMrssXslt($this->getSeasonSynopsisXsl());
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(HuluDistributionField::SEASON_TUNEIN_INFORMATION);
		$fieldConfig->setUserFriendlyFieldName('SeasonTuneInInformation');
		$fieldConfig->setEntryMrssXslt($this->getSeasonTuneInInformationXsl());
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(HuluDistributionField::VIDEO_MEDIA_TYPE);
		$fieldConfig->setUserFriendlyFieldName('Media Type');
		$fieldConfig->setEntryMrssXslt($this->getVideoMediaTypeXsl());
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(HuluDistributionField::VIDEO_TITLE);
		$fieldConfig->setUserFriendlyFieldName('Entry Name');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(name)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(HuluDistributionField::VIDEO_EPISODE_NUMBER);
		$fieldConfig->setUserFriendlyFieldName('Episode Number');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/EpisodeNumber" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED); // See special validation
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(HuluDistributionField::VIDEO_RATING);
		$fieldConfig->setUserFriendlyFieldName('Content Rating');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/ContentRating" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(HuluDistributionField::VIDEO_CONTENT_RATING_REASON);
		$fieldConfig->setUserFriendlyFieldName('Content Rating Reason');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/ContentRatingReason" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(HuluDistributionField::VIDEO_AVAILABLE_DATE);
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunrise" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(HuluDistributionField::VIDEO_EXPIRATION_DATE);
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunset" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(HuluDistributionField::VIDEO_DESCRIPTION);
		$fieldConfig->setUserFriendlyFieldName('Entry Description');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(description)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(HuluDistributionField::VIDEO_FULL_DESCRIPTION);
		$fieldConfig->setUserFriendlyFieldName('Hulu Full Description');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/HuluFullDescription" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(HuluDistributionField::VIDEO_COPYRIGHT);
		$fieldConfig->setUserFriendlyFieldName('Hulu Copyright');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/HuluCopyright" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::NOT_REQUIRED);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(HuluDistributionField::VIDEO_KEYWORDS);
		$fieldConfig->setUserFriendlyFieldName('Entry Tags');
		$fieldConfig->setEntryMrssXslt(
					'<xsl:for-each select="tags/tag">
						<xsl:if test="position() &gt; 1">
							<xsl:text>,</xsl:text>
						</xsl:if>
						<xsl:value-of select="." />
					</xsl:for-each>');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(HuluDistributionField::VIDEO_LANGUAGE);
		$fieldConfig->setUserFriendlyFieldName('Video Language');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/VideoLanguage" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(HuluDistributionField::VIDEO_PROGRAMMING_TYPE);
		$fieldConfig->setUserFriendlyFieldName('Hulu Programming Type');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/HuluProgrammingType" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(HuluDistributionField::VIDEO_EXTERNAL_ID);
		$fieldConfig->setUserFriendlyFieldName('Entry ID');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(entryId)" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(HuluDistributionField::VIDEO_ORIGINAL_PREMIERE_DATE);
		$fieldConfig->setUserFriendlyFieldName('Original Premier Date');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/OriginalPremierDate" />');
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
		
		return $fieldConfigArray;
	}

	public function validateForSubmission(EntryDistribution $entryDistribution, $action)
	{
		$validationErrors = parent::validateForSubmission($entryDistribution, $action);
		
		$allFieldValues = $this->getAllFieldValues($entryDistribution);
		if (!$allFieldValues || !is_array($allFieldValues)) 
		{
			KalturaLog::err('Error getting field values from entry distribution id ['.$entryDistribution->getId().'] profile id ['.$this->getId().']');
			return $validationErrors;
		}
		
		$validationErrors = array_merge($validationErrors, $this->validateMaxLength($this->maxLengthValidation, $allFieldValues, $action));
		$validationErrors = array_merge($validationErrors, $this->validateInListOrNull($this->inListOrNullValidation, $allFieldValues, $action));
		if ($this->getDisableEpisodeNumberCustomValidation() !== true)
			$validationErrors = array_merge($validationErrors, $this->validateEpisodeNumber($allFieldValues, $action));
		$validationErrors = array_merge($validationErrors, $this->validateContentRatingReason($allFieldValues, $action));

		return $validationErrors;
	}
	
	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return HuluDistributionPlugin::getProvider();
	}
	
	protected function getSeriesTitleXsl()
	{
		return '<xsl:choose>
	<xsl:when test="customData/metadata/SeriesTitle">
		<xsl:value-of select="customData/metadata/SeriesTitle" />
	</xsl:when>
	<xsl:when test="customData/metadata/Series_item/title">
		<xsl:value-of select="customData/metadata/Series_item/title" />
	</xsl:when>
	<xsl:otherwise>
		<xsl:text></xsl:text>
	</xsl:otherwise>
</xsl:choose>';
	}
	
	protected function getSeriesDescriptionXsl()
	{
		return '<xsl:choose>
	<xsl:when test="customData/metadata/SeriesDescription">
		<xsl:value-of select="customData/metadata/SeriesDescription" />
	</xsl:when>
	<xsl:when test="customData/metadata/Series_item/description">
		<xsl:value-of select="customData/metadata/Series_item/description" />
	</xsl:when>
	<xsl:otherwise>
		<xsl:text></xsl:text>
	</xsl:otherwise>
</xsl:choose>';
	}
	
	protected function getSeriesPrimaryCategoryXsl()
	{
		return '<xsl:choose>
	<xsl:when test="customData/metadata/SeriesPrimaryCategory">
		<xsl:value-of select="customData/metadata/SeriesPrimaryCategory" />
	</xsl:when>
	<xsl:when test="customData/metadata/Series_item/customData/metadata/PrimaryCategory">
		<xsl:value-of select="customData/metadata/Series_item/customData/metadata/PrimaryCategory" />
	</xsl:when>
	<xsl:when test="distribution[@entryDistributionId=$entryDistributionId]/SeriesPrimaryCategory">
		<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/SeriesPrimaryCategory" />
	</xsl:when>
	<xsl:otherwise>
		<xsl:text></xsl:text>
	</xsl:otherwise>
</xsl:choose>';
	}
	
	protected function getSeriesAdditionalCategoriesXsl()
	{
		return '<xsl:choose>
	<xsl:when test="customData/metadata/SeriesAdditionalCategorie">
		<xsl:for-each select="customData/metadata/SeriesAdditionalCategorie">
			<xsl:if test="position() &gt; 1">
				<xsl:text>,</xsl:text>
			</xsl:if>
			<xsl:value-of select="." />
		</xsl:for-each>
	</xsl:when>
	<xsl:when test="customData/metadata/Series_item/customData/metadata/AdditionalCategories">
		<xsl:for-each select="customData/metadata/Series_item/customData/metadata/AdditionalCategories">
			<xsl:if test="position() &gt; 1">
				<xsl:text>,</xsl:text>
			</xsl:if>
			<xsl:value-of select="." />
		</xsl:for-each>
	</xsl:when>
	<xsl:when test="distribution[@entryDistributionId=$entryDistributionId]/AdditionalCategories">
		<xsl:for-each select="distribution[@entryDistributionId=$entryDistributionId]/AdditionalCategories">
			<xsl:if test="position() &gt; 1">
				<xsl:text>,</xsl:text>
			</xsl:if>
			<xsl:value-of select="." />
		</xsl:for-each>
	</xsl:when>
	<xsl:otherwise>
		<xsl:text></xsl:text>
	</xsl:otherwise>
</xsl:choose>';
	}
	
	protected function getSeasonNumberXsl()
	{
		return '<xsl:choose>
	<xsl:when test="customData/metadata/SeasonNumber">
		<xsl:value-of select="customData/metadata/SeasonNumber" />
	</xsl:when>
	<xsl:when test="customData/metadata/Series_item/customData/metadata/SeasonNumber">
		<xsl:value-of select="customData/metadata/Series_item/customData/metadata/SeasonNumber" />
	</xsl:when>
	<xsl:when test="distribution[@entryDistributionId=$entryDistributionId]/SeasonNumber">
		<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/SeasonNumber" />
	</xsl:when>
	<xsl:otherwise>
		<xsl:text></xsl:text>
	</xsl:otherwise>
</xsl:choose>';
	}
	
	protected function getSeasonSynopsisXsl()
	{
		return '<xsl:choose>
	<xsl:when test="customData/metadata/SeasonSynopsis">
		<xsl:value-of select="customData/metadata/SeasonSynopsis" />
	</xsl:when>
	<xsl:when test="customData/metadata/Series_item/description">
		<xsl:value-of select="customData/metadata/Series_item/description" />
	</xsl:when>
	<xsl:when test="distribution[@entryDistributionId=$entryDistributionId]/SeasonSynopsis">
		<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/SeasonSynopsis" />
	</xsl:when>
	<xsl:otherwise>
		<xsl:text></xsl:text>
	</xsl:otherwise>
</xsl:choose>';
	}
	
	protected function getSeasonTuneinInformationXsl()
	{
		return '<xsl:choose>
	<xsl:when test="customData/metadata/SeasonTuneInInformation">
		<xsl:value-of select="customData/metadata/SeasonTuneInInformation" />
	</xsl:when>
	<xsl:when test="customData/metadata/Series_item/customData/metadata/SeasonTuneInInformation">
		<xsl:value-of select="customData/metadata/Series_item/customData/metadata/SeasonTuneInInformation" />
	</xsl:when>
	<xsl:when test="distribution[@entryDistributionId=$entryDistributionId]/SeasonTuneInInformation">
		<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/SeasonTuneInInformation" />
	</xsl:when>
	<xsl:otherwise>
		<xsl:text></xsl:text>
	</xsl:otherwise>
</xsl:choose>';
	}
	
	protected function getVideoMediaTypeXsl()
	{
		return '<xsl:choose>
	<xsl:when test="customData/metadata/MediaType">
		<xsl:value-of select="customData/metadata/MediaType" />
	</xsl:when>
	<xsl:when test="customData/metadata/Series_item/customData/metadata/MediaType">
		<xsl:value-of select="customData/metadata/Series_item/customData/metadata/MediaType" />
	</xsl:when>
	<xsl:when test="distribution[@entryDistributionId=$entryDistributionId]/MediaType">
		<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/MediaType" />
	</xsl:when>
	<xsl:otherwise>
		<xsl:text></xsl:text>
	</xsl:otherwise>
</xsl:choose>';
	}

	public function validateEpisodeNumber($allFieldValues, $action)
	{
		$validationErrors = array(); 
		if ($allFieldValues[HuluDistributionField::VIDEO_MEDIA_TYPE] == 'TV' || $allFieldValues[HuluDistributionField::VIDEO_MEDIA_TYPE] == 'Web Original')
		{
			if (!isset($allFieldValues[HuluDistributionField::VIDEO_EPISODE_NUMBER]) || trim($allFieldValues[HuluDistributionField::VIDEO_EPISODE_NUMBER]) === '')
			{
				$errorMsg = $this->getUserFriendlyFieldName(HuluDistributionField::VIDEO_EPISODE_NUMBER).' is required when ' . $this->getUserFriendlyFieldName(HuluDistributionField::VIDEO_MEDIA_TYPE) .' is ' . $allFieldValues[HuluDistributionField::VIDEO_MEDIA_TYPE];
				$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, $this->getUserFriendlyFieldName(HuluDistributionField::VIDEO_EPISODE_NUMBER));
				$validationError->setValidationErrorType(DistributionValidationErrorType::CUSTOM_ERROR);
				$validationError->setValidationErrorParam($errorMsg);
				$validationError->setDescription($errorMsg);
				$validationErrors[] = $validationError;
			}
			else 
			{
				$value = isset($allFieldValues[HuluDistributionField::VIDEO_EPISODE_NUMBER]) ? intval($allFieldValues[HuluDistributionField::VIDEO_EPISODE_NUMBER]) : 0;
				if ($value < -1 || $value > 5000)
				{
					$errorMsg = $this->getUserFriendlyFieldName(HuluDistributionField::VIDEO_EPISODE_NUMBER).' should be between 1 and 5000';
					$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, $this->getUserFriendlyFieldName(HuluDistributionField::VIDEO_EPISODE_NUMBER));
					$validationError->setValidationErrorType(DistributionValidationErrorType::CUSTOM_ERROR);
					$validationError->setValidationErrorParam($errorMsg);
					$validationError->setDescription($errorMsg);
					$validationErrors[] = $validationError;
				}
			}
		}
		
		return $validationErrors;
	}
	
	public function validateContentRatingReason($allFieldValues, $action)
	{
		$validationErrors = array();
		$allowedValues = array(
			// TV
			'D', 'FV', 'L', 'S', 'V',
			
			// Film
			'AT', 'N', 'BN', 'SS', 'SL', 'V',
		);
		
		if (isset($allFieldValues[HuluDistributionField::VIDEO_CONTENT_RATING_REASON]) && $allFieldValues[HuluDistributionField::VIDEO_CONTENT_RATING_REASON] != '')
		{
			$reasons = explode(',', $allFieldValues[HuluDistributionField::VIDEO_CONTENT_RATING_REASON]);
			foreach($reasons as &$tempReason)
				$tempReason = trim($tempReason);
				
			foreach($reasons as $reason)
			{
				if (!in_array($reason, $allowedValues))
				{
					$errorMsg = '"' . $reason . '" is invalid value for ' . $this->getUserFriendlyFieldName(HuluDistributionField::VIDEO_CONTENT_RATING_REASON).' and must be in ['.implode(',', $allowedValues).']';
					$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, $this->getUserFriendlyFieldName(HuluDistributionField::VIDEO_CONTENT_RATING_REASON));
					$validationError->setValidationErrorType(DistributionValidationErrorType::CUSTOM_ERROR);
					$validationError->setValidationErrorParam($errorMsg);
					$validationError->setDescription($errorMsg);
					$validationErrors[] = $validationError;
				}
			}
		}
		
		return $validationErrors;
	}
	
	public function getSftpHost()						{return $this->getFromCustomData(self::CUSTOM_DATA_SFTP_HOST);}
	public function getSftpLogin()						{return $this->getFromCustomData(self::CUSTOM_DATA_SFTP_LOGIN);}
	public function getSftpPass()						{return $this->getFromCustomData(self::CUSTOM_DATA_SFTP_PASS);}
	public function getSeriesChannel()					{return $this->getFromCustomData(self::CUSTOM_DATA_SERIES_CHANNEL);}
	public function getSeriesPrimaryCategory()			{return $this->getFromCustomData(self::CUSTOM_DATA_SERIES_PRIMARY_CATEGORY);}
	public function getSeriesAdditionalCategories()		{return $this->getFromCustomData(self::CUSTOM_DATA_SERIES_ADDITIONAL_CATEGORIES);}
	public function getSeasonNumber()					{return $this->getFromCustomData(self::CUSTOM_DATA_SEASON_NUMBER);}
	public function getSeasonSynopsis()					{return $this->getFromCustomData(self::CUSTOM_DATA_SEASON_SYNOPSIS);}
	public function getSeasonTuneInInformation()		{return $this->getFromCustomData(self::CUSTOM_DATA_SEASON_TUNE_IN_INFORMATION);}
	public function getVideoMediaType()					{return $this->getFromCustomData(self::CUSTOM_DATA_VIDEO_MEDIA_TYPE);}
	public function getDisableEpisodeNumberCustomValidation()	{return $this->getFromCustomData(self::CUSTOM_DATA_DISABLE_EPISODE_NUMBER_CUSTOM_VALIDATION);}
	public function getAsperaHost()						{return $this->getFromCustomData(self::CUSTOM_DATA_ASPERA_HOST);}
	public function getAsperaLogin()					{return $this->getFromCustomData(self::CUSTOM_DATA_ASPERA_LOGIN);}
	public function getAsperaPass()						{return $this->getFromCustomData(self::CUSTOM_DATA_ASPERA_PASS);}
	public function getAsperaPublicKey()				{return $this->getFromCustomData(self::CUSTOM_DATA_ASPERA_PUBLIC_KEY);}
	public function getAsperaPrivateKey()				{return $this->getFromCustomData(self::CUSTOM_DATA_ASPERA_PRIVATE_KEY);}
	public function getPort()							{return $this->getFromCustomData(self::CUSTOM_DATA_PORT);}
	public function getPassphrase()						{return $this->getFromCustomData(self::CUSTOM_DATA_PASSPHRASE);}
	public function getProtocol()						{return $this->getFromCustomData(self::CUSTOM_DATA_PROTOCOL);}
	
	public function setSftpHost($v)						{$this->putInCustomData(self::CUSTOM_DATA_SFTP_HOST, $v);}
	public function setSftpLogin($v)					{$this->putInCustomData(self::CUSTOM_DATA_SFTP_LOGIN, $v);}
	public function setSftpPass($v)						{$this->putInCustomData(self::CUSTOM_DATA_SFTP_PASS, $v);}
	public function setSeriesChannel ($v)				{$this->putInCustomData(self::CUSTOM_DATA_SERIES_CHANNEL, $v);}
	public function setSeriesPrimaryCategory ($v)		{$this->putInCustomData(self::CUSTOM_DATA_SERIES_PRIMARY_CATEGORY, $v);}
	public function setSeriesAdditionalCategories ($v)	{$this->putInCustomData(self::CUSTOM_DATA_SERIES_ADDITIONAL_CATEGORIES, $v);}
	public function setSeasonNumber ($v)				{$this->putInCustomData(self::CUSTOM_DATA_SEASON_NUMBER, $v);}
	public function setSeasonSynopsis ($v)				{$this->putInCustomData(self::CUSTOM_DATA_SEASON_SYNOPSIS, $v);}
	public function setSeasonTuneInInformation ($v)		{$this->putInCustomData(self::CUSTOM_DATA_SEASON_TUNE_IN_INFORMATION, $v);}
	public function setVideoMediaType ($v)				{$this->putInCustomData(self::CUSTOM_DATA_VIDEO_MEDIA_TYPE, $v);}
	public function setDisableEpisodeNumberCustomValidation ($v)	{$this->putInCustomData(self::CUSTOM_DATA_DISABLE_EPISODE_NUMBER_CUSTOM_VALIDATION, $v);}
	public function setAsperaHost($v)					{$this->putInCustomData(self::CUSTOM_DATA_ASPERA_HOST, $v);}
	public function setAsperaLogin($v)					{$this->putInCustomData(self::CUSTOM_DATA_ASPERA_LOGIN, $v);}
	public function setAsperaPass($v)					{$this->putInCustomData(self::CUSTOM_DATA_ASPERA_PASS, $v);}
	public function setAsperaPublicKey($v)				{$this->putInCustomData(self::CUSTOM_DATA_ASPERA_PUBLIC_KEY, $v);}
    public function setAsperaPrivateKey($v)				{$this->putInCustomData(self::CUSTOM_DATA_ASPERA_PRIVATE_KEY, $v);}
	public function setPort($v)							{$this->putInCustomData(self::CUSTOM_DATA_PORT, $v);}
 	public function setPassphrase($v)				    {$this->putInCustomData(self::CUSTOM_DATA_PASSPHRASE, $v);}
	public function setProtocol($v)						{$this->putInCustomData(self::CUSTOM_DATA_PROTOCOL, $v);}
}