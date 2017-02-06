<?php
/**
 * Enable caption assets management for entry objects
 * @package plugins.caption
 */
class CaptionPlugin extends KalturaPlugin implements IKalturaServices, IKalturaPermissions, IKalturaEnumerator, IKalturaObjectLoader, IKalturaApplicationPartialView, IKalturaSchemaContributor, IKalturaMrssContributor, IKalturaPlayManifestContributor, IKalturaEventConsumers
{
	const PLUGIN_NAME = 'caption';
	const KS_PRIVILEGE_CAPTION = 'caption';

	const MULTI_CAPTION_FLOW_MANAGER_CLASS = 'kMultiCaptionFlowManager';

       const SERVE_WEBVTT_URL_PREFIX = '/api_v3/index.php/service/caption_captionasset/action/serveWebVTT';

	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/**
	 * Static map between language format used by the whole system and the captions format used by the M3U8 file
	 * @var array
	 */
	public static $captionsFormatMap = array (
				'Abkhazian' =>	'abk',
				'Afar' =>	'aar',
				'Afrikaans' =>	'afr',
				'Albanian' =>	'sqi',
				'Amharic' =>	'amh',
				'Arabic' =>	'ara',
				'Armenian' =>	'hye',
				'Assamese' =>	'asm',
				'Aymara' =>	'aym',
				'Azerbaijani' =>	'aze',
				'Bashkir' =>	'bam',
				'Basque' =>	'eus',
				'Bengali (Bangla)' =>	'ben',
				'Bhutani' =>	'dzo',
				'Bislama' =>	'bis',
				'Breton' =>	'bre',
				'Bulgarian' =>	'bul',
				'Burmese' =>	'mya',
				'Byelorussian (Belarusian)' =>	'bel',
				'Cambodian' =>	'khm',
				'Catalan' =>	'cat',
				'Chinese' =>	'zho',
				'Corsican' =>	'cos',
				'Croatian' =>	'hrv',
				'Czech' =>	'ces',
				'Danish' =>	'dan',
				'Dutch' =>	'nld',
				'English' =>	'eng',
				'Esperanto' =>	'epo',
				'Estonian' =>	'est',
				'Faeroese' =>	'fao',
				'Farsi' =>	'fas',
				'Fiji' =>	'fij',
				'Finnish' =>	'fin',
				'French' =>	'fra',
				'Frisian' =>	'fry',
				'Galician' =>	'glg',
				'Gaelic (Scottish)' =>	'gla',
				'Gaelic (Manx)' =>	'glv',
				'Georgian' =>	'kat',
				'German' =>	'deu',
				'Greek' =>	'ell',
				'Greenlandic' =>	'kal',
				'Guarani' =>	'grn',
				'Gujarati' =>	'guj',
				'Hausa' =>	'hau',
				'Hebrew' =>	'heb',
				'Hindi' =>	'hin',
				'Hungarian' =>	'hun',
				'Icelandic' =>	'isl',
				'Indonesian' =>	'ind',
				'Interlingua' =>	'ina',
				'Interlingue' =>	'ile',
				'Inuktitut' =>	'iku',
				'Inupiak' =>	'ipk',
				'Irish' =>	'gle',
				'Italian' =>	'ita',
				'Japanese' =>	'jpn',
				'Javanese' =>	'jav',
				'Kannada' =>	'kan',
				'Kashmiri' =>	'kas',
				'Kazakh' =>	'kaz',
				'Kinyarwanda (Ruanda)' =>	'kin',
				'Kirghiz' =>	'kir',
				'Kirundi (Rundi)' =>	'run',
				'Korean' =>	'kor',
				'Kurdish' =>	'kur',
				'Laothian' =>	'lao',
				'Latin' =>	'lat',
				'Latvian (Lettish)' =>	'lav',
				'Limburgish ( Limburger)' =>	'lim',
				'Lingala' =>	'lin',
				'Lithuanian' =>	'lit',
				'Macedonian' =>	'mkd',
				'Malagasy' =>	'mlg',
				'Malay' =>	'msa',
				'Malayalam' =>	'mal',
				'Maltese' =>	'mlt',
				'Maori' =>	'mri',
				'Marathi' =>	'mar',
				'Mongolian' =>	'mon',
				'Nauru' =>	'nau',
				'Nepali' =>	'nep',
				'Norwegian' =>	'nor',
				'Occitan' =>	'oci',
				'Oriya' =>	'ori',
				'Oromo (Afan, Galla)' =>	'orm',
				'Pashto (Pushto)' =>	'pus',
				'Polish' =>	'pol',
				'Portuguese' =>	'por',
				'Punjabi' =>	'pan',
				'Quechua' =>	'que',
				'Rhaeto-Romance' =>	'roh',
				'Romanian' =>	'ron',
				'Russian' =>	'rus',
				'Samoan' =>	'smo',
				'Sangro' =>	'sag',
				'Sanskrit' =>	'san',
				'Serbian' =>	'srp',
				'Sesotho' =>	'sot',
				'Setswana' =>	'tsn',
				'Shona' =>	'sna',
				'Sindhi' =>	'snd',
				'Sinhalese' =>	'sin',
				'Siswati' =>	'ssw',
				'Slovak' =>	'slk',
				'Slovenian' =>	'slv',
				'Somali' =>	'som',
				'Spanish' =>	'spa',
				'Sundanese' =>	'sun',
				'Swahili (Kiswahili)' =>	'swa',
				'Swedish' =>	'swe',
				'Tagalog' =>	'tgl',
				'Tajik' =>	'tgk',
				'Tamil' =>	'tam',
				'Tatar' =>	'tat',
				'Telugu' =>	'tel',
				'Thai' =>	'tha',
				'Tibetan' =>	'bod',
				'Tigrinya' =>	'tir',
				'Tonga' =>	'ton',
				'Tsonga' =>	'tso',
				'Turkish' =>	'tur',
				'Turkmen' =>	'tuk',
				'Twi' =>	'twi',
				'Uighur' =>	'uig',
				'Ukrainian' =>	'ukr',
				'Urdu' =>	'urd',
				'Uzbek' =>	'uzb',
				'Vietnamese' =>	'vie',
				'Volapuk' =>	'vol',
				'Welsh' =>	'cym',
				'Wolof' =>	'wol',
				'Xhosa' =>	'xho',
				'Yiddish' =>	'yid',
				'Yoruba' =>	'yor',
				'Zulu' =>	'zul',
	
		);
	
	/* (non-PHPdoc)
	 * @see IKalturaPermissions::isAllowedPartner()
	 */
	public static function isAllowedPartner($partnerId)
	{
		$partner = PartnerPeer::retrieveByPK($partnerId);
		return $partner->getPluginEnabled(self::PLUGIN_NAME);
	}

	/* (non-PHPdoc)
	 * @see IKalturaServices::getServicesMap()
	 */
	public static function getServicesMap()
	{
		$map = array(
			'captionAsset' => 'CaptionAssetService',
			'captionParams' => 'CaptionParamsService',
		);
		return $map;
	}

	/* (non-PHPdoc)
	 * @see IKalturaEventConsumers::getEventConsumers()
	 */
	public static function getEventConsumers()
	{
		return array(
			self::MULTI_CAPTION_FLOW_MANAGER_CLASS,
		);
	}

	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('CaptionAssetType', 'CaptionObjectFeatureType', 'ParseMultiLanguageCaptionAssetBatchType');
	
		if($baseEnumName == 'assetType')
			return array('CaptionAssetType');
		
		if($baseEnumName == 'ObjectFeatureType')
			return array('CaptionObjectFeatureType');

		if ($baseEnumName == 'BatchJobType')
			return array('ParseMultiLanguageCaptionAssetBatchType');
	
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if($baseClass == 'KalturaAsset' && $enumValue == self::getAssetTypeCoreValue(CaptionAssetType::CAPTION))
			return new KalturaCaptionAsset();
	
		if($baseClass == 'KalturaAssetParams' && $enumValue == self::getAssetTypeCoreValue(CaptionAssetType::CAPTION))
			return new KalturaCaptionParams();

		if($baseClass == 'kJobData' && $enumValue == self::getBatchJobTypeCoreValue(ParseMultiLanguageCaptionAssetBatchType::PARSE_MULTI_LANGUAGE_CAPTION_ASSET))
			return new kParseMultiLanguageCaptionAssetJobData();

		if($baseClass == 'KalturaJobData' && $enumValue == self::getApiValue(ParseMultiLanguageCaptionAssetBatchType::PARSE_MULTI_LANGUAGE_CAPTION_ASSET))
			return new KalturaParseMultiLanguageCaptionAssetJobData();
	
		return null;
	}

	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'asset' && $enumValue == self::getAssetTypeCoreValue(CaptionAssetType::CAPTION))
			return 'CaptionAsset';
	
		if($baseClass == 'assetParams' && $enumValue == self::getAssetTypeCoreValue(CaptionAssetType::CAPTION))
			return 'CaptionParams';
			
		return null;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaApplicationPartialView::getApplicationPartialViews()
	 */
	public static function getApplicationPartialViews($controller, $action)
	{
		if($controller == 'batch' && $action == 'entryInvestigation')
		{
			return array(
				new Kaltura_View_Helper_EntryInvestigateCaptionAssets(),
			);
		}
		
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaSchemaContributor::contributeToSchema()
	 */
	public static function contributeToSchema($type)
	{
		$coreType = kPluginableEnumsManager::apiToCore('SchemaType', $type);
		if($coreType != SchemaType::SYNDICATION)
			return null;
			
		$xsd = '	
		
	<!-- ' . self::getPluginName() . ' -->
			
	<xs:complexType name="T_subTitle">
		<xs:sequence>
			<xs:element name="tags" minOccurs="1" maxOccurs="1" type="T_tags">
				<xs:annotation>
					<xs:documentation>Specifies specific tags you want to set for the flavor asset</xs:documentation>
				</xs:annotation>
			</xs:element>
			<xs:element ref="subtitle-extension" minOccurs="0" maxOccurs="unbounded" />
		</xs:sequence>
		
		<xs:attribute name="captionParamsId" type="xs:int" use="optional">
			<xs:annotation>
				<xs:documentation>ID of caption params that associated with the caption asset</xs:documentation>
			</xs:annotation>
		</xs:attribute>
		<xs:attribute name="captionParams" type="xs:string" use="optional">
			<xs:annotation>
				<xs:documentation>System name of caption params that associated with the caption asset</xs:documentation>
			</xs:annotation>
		</xs:attribute>
		<xs:attribute name="captionAssetId" type="xs:string" use="optional">
			<xs:annotation>
				<xs:documentation>Caption asset unique id</xs:documentation>
			</xs:annotation>
		</xs:attribute>
		<xs:attribute name="isDefault" type="xs:boolean" use="optional">
			<xs:annotation>
				<xs:documentation>Indicates if the caption asset is the entry default caption asset</xs:documentation>
			</xs:annotation>
		</xs:attribute>
		<xs:attribute name="format" type="KalturaCaptionType" use="optional">
			<xs:annotation>
				<xs:documentation>Caption asset file format</xs:documentation>
			</xs:annotation>
		</xs:attribute>
		<xs:attribute name="lang" type="KalturaLanguage" use="optional">
			<xs:annotation>
				<xs:documentation>Caption asset file language</xs:documentation>
			</xs:annotation>
		</xs:attribute>
		<xs:attribute name="href" type="xs:string" use="optional">
			<xs:annotation>
				<xs:documentation>Caption asset file download URL</xs:documentation>
			</xs:annotation>
		</xs:attribute>
						
	</xs:complexType>
	
	<xs:element name="subtitle-extension" />
	<xs:element name="subTitle" type="T_subTitle" substitutionGroup="item-extension">
		<xs:annotation>
			<xs:documentation>Caption asset element</xs:documentation>
			<xs:appinfo>
				<example>
					<subTitle href="http://kaltura.domain/path/caption_file.srt" captionAssetId="{caption_asset_id}" isDefault="true" format="2" lang="Hebrew">
						<tags>
							<tag>example</tag>
							<tag>my_tag</tag>
						</tags>
					</subTitle>
				</example>
			</xs:appinfo>
		</xs:annotation>
	</xs:element>
		';
		
		return $xsd;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaMrssContributor::contribute()
	 */
	public function contribute(BaseObject $object, SimpleXMLElement $mrss, kMrssParameters $mrssParams = null)
	{
		if(!($object instanceof entry))
			return;
			
		$types = KalturaPluginManager::getExtendedTypes(assetPeer::OM_CLASS, CaptionPlugin::getAssetTypeCoreValue(CaptionAssetType::CAPTION));
		$captionAssets = assetPeer::retrieveByEntryId($object->getId(), $types);
		
		foreach($captionAssets as $captionAsset)
			$this->contributeCaptionAssets($captionAsset, $mrss);
	}

	/**
	 * @param CaptionAsset $captionAsset
	 * @param SimpleXMLElement $mrss
	 * @return SimpleXMLElement
	 */
	public function contributeCaptionAssets(CaptionAsset $captionAsset, SimpleXMLElement $mrss)
	{
		$subTitle = $mrss->addChild('subTitle');
		$subTitle->addAttribute('href', $captionAsset->getDownloadUrl(true));
		$subTitle->addAttribute('captionAssetId', $captionAsset->getId());
		$subTitle->addAttribute('isDefault', ($captionAsset->getDefault() ? 'true' : 'false'));
		$subTitle->addAttribute('format', $captionAsset->getContainerFormat());
		$subTitle->addAttribute('lang', $captionAsset->getLanguage());
		if($captionAsset->getFlavorParamsId())
			$subTitle->addAttribute('captionParamsId', $captionAsset->getFlavorParamsId());
			
		$tags = $subTitle->addChild('tags');
		foreach(explode(',', $captionAsset->getTags()) as $tag)
			$tags->addChild('tag', kMrssManager::stringToSafeXml($tag));
	}
	
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getAssetTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('assetType', $value);
	}
	
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getObjectFeatureTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('ObjectFeatureType', $value);
	}
	
	/**
	* @return int id of dynamic enum in the DB.
	*/
	public static function getBatchJobTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('BatchJobType', $value);
	}
	
	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaMrssContributor::getObjectFeatureType()
	 */
	public function getObjectFeatureType()
	{
		return self::getObjectFeatureTypeCoreValue(CaptionObjectFeatureType::CAPTIONS);
	}

	/**
	 * @param CaptionAsset $captionAsset
	 * @param int $expiry
	 * @return string
	 */
	static protected function generateKsForCaptionServe($captionAsset, $expiry = 86400)
	{
		$partnerId = $captionAsset->getPartnerId();
		$partner = PartnerPeer::retrieveByPK($partnerId);
		$secret = $partner->getSecret();
		$privileges = self::KS_PRIVILEGE_CAPTION.":".$captionAsset->getEntryId();
       	$privileges .= "," . kSessionBase::PRIVILEGE_DISABLE_ENTITLEMENT_FOR_ENTRY . ":" . $captionAsset->getEntryId();
        	$privileges .= ',' . kSessionBase::PRIVILEGE_URI_RESTRICTION . ':' . self::SERVE_WEBVTT_URL_PREFIX . '*';
		$ksStr = '';
		
		kSessionUtils::startKSession($partnerId, $secret, null, $ksStr, $expiry, false, "", $privileges);
		
		return $ksStr;
	}

	static protected function getLocalCaptionUrl($config, asset $captionAsset)
	{
		$deliveryProfile = $config->deliveryProfile;
			
		$url = $deliveryProfile->getAssetUrl($captionAsset, false);
		$url = preg_replace('/^https?:\/\//', '', $url);
		$url = ltrim($url, "/");
			
		$urlPrefix = $deliveryProfile->getUrl();
		$urlPrefix = preg_replace('/^https?:\/\//', '', $urlPrefix);
		$urlPrefix = $deliveryProfile->getDynamicAttributes()->getMediaProtocol() . '://' . $urlPrefix;
		$urlPrefix = rtrim($urlPrefix, "/") . "/";
		
		$urlPrefixPath = parse_url($urlPrefix, PHP_URL_PATH);
		if ($urlPrefixPath &&
				substr($urlPrefix, -strlen($urlPrefixPath)) == $urlPrefixPath)
		{
			$urlPrefix = substr($urlPrefix, 0, -strlen($urlPrefixPath));
			$url = rtrim($urlPrefixPath, '/') . '/' . ltrim($url, '/');
		}
		
		return array($urlPrefix, $url);
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaPlayManifestContributor::getManifestEditors()
	 */
	public static function getManifestEditors ($config)
	{
		$contributors = array();

		switch ($config->format)
		{
			case PlaybackProtocol::APPLE_HTTP:

				if ($config->rendererClass != 'kM3U8ManifestRenderer')
				{
					return array();
				}

				$contributor = new WebVttCaptionsManifestEditor();
				$contributor->captions = array();
				//retrieve the current working partner's captions according to the entryId
				$c = new Criteria();
				$c->addAnd(assetPeer::ENTRY_ID, $config->entryId);
				$c->addAnd(assetPeer::TYPE, CaptionPlugin::getAssetTypeCoreValue(CaptionAssetType::CAPTION));
				$captionAssets = assetPeer::doSelect($c);
				if (!count($captionAssets))
					return array();

				foreach ($captionAssets as $captionAsset)
				{
					/* @var $captionAsset CaptionAsset */
					$captionAssetObj = array();

					if ($captionAsset->getContainerFormat() == CaptionType::WEBVTT)
					{
						// pass null as storageId in order to support any storage profile and not the one selected by the current video flavors
						$url = $captionAsset->getExternalUrl(null);
						if (!$url)
						{
							list($urlPrefix, $url) = self::getLocalCaptionUrl($config, $captionAsset);
							
							$captionAssetObj['urlPrefix'] = $urlPrefix;
							$captionAssetObj['tokenizer'] = $config->deliveryProfile->getTokenizer();
						}
						
						$captionAssetObj['url'] = $url;
					}
					else
					{
						if (!PermissionPeer::isValidForPartner(CaptionPermissionName::FEATURE_GENERATE_WEBVTT_CAPTIONS, $captionAsset->getPartnerId()))
							continue;
						
						$syncKey = $captionAsset->getSyncKey(asset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
						$fs = kFileSyncUtils::getReadyFileSyncForKey($syncKey, false, false);
						if (reset($fs) === null)
							continue;

						$cdnHost = myPartnerUtils::getCdnHost($captionAsset->getPartnerId());

						$versionStr = '';
						if ($captionAsset->getVersion() > 1)
							$versionStr = '/version/' . $captionAsset->getVersion();

						$ksStr = '';
						if ($captionAsset->isKsNeededForDownload())
						{
							$ksStr = '/ks/' . self::generateKsForCaptionServe($captionAsset);
						}

						$captionAssetObj['url'] = $cdnHost . self::SERVE_WEBVTT_URL_PREFIX .
							'/captionAssetId/' . $captionAsset->getId() . $ksStr . $versionStr . '/a.m3u8';
					}
					$label = $captionAsset->getLabel();
					if (!$label)
						$label = $captionAsset->getLanguage();
					if (!$label)
						$label = 'Track' . (count($contributor->captions) + 1);
					$captionAssetObj['label'] = $label;
					$captionAssetObj['default'] = $captionAsset->getDefault() ? "YES" : "NO";
					if (isset(self::$captionsFormatMap[$captionAsset->getLanguage()]))
						$captionAssetObj['language'] = self::$captionsFormatMap[$captionAsset->getLanguage()];

					KalturaLog::info("Object passed into editor: " . print_r($captionAssetObj, true));
					$contributor->captions[] = $captionAssetObj;
				}

				if ($contributor->captions)
					$contributors[] = $contributor;

				break;
		}

		return $contributors;
	}
}
