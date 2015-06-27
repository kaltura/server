<?php
/**
 * @package plugins.tvinciDistribution
 * @subpackage lib
 */
class TvinciDistributionFeedHelper
{
	const DATE_FORMAT = 'd/m/Y H:i:s';

	const ACTION_SUBMIT = 'insert';
	const ACTION_UPDATE = 'update';
	const ACTION_DELETE = 'delete';

	const DEFAULT_SCHEMA_ID = 2;
	
	/**
	 * var KalturaTvinciDistributionProfile
	 */
	protected $distributionProfile;

	/**
	 * @var array
	 */
	protected $fieldValues;

	/**
	 * var string
	 */
	protected $entryId;

	/**
	 * var string
	 */
	protected $description;

	/**
	 * var string
	 */
	protected $titleName;

	/**
	 * var string
	 */
	protected $referenceId;

	/**
	 * var string
	 */
	protected $createdAt;

	/**
	 * var array
	 */
	protected $picRatiosArray;

	/**
	 * var string
	 */
	protected $defaultThumbUrl;

	/**
	 * var array
	 */
	protected $videoAssetToUrlMap;

	/**
	 * @var int
	 * @see TvinciDistributionField::METADATA_SCHEMA_ID
	 */
	protected $schemaId;
	
	/**
	 * var string
	 */
	protected $language;

	/**
	 * var string
	 */
	protected $metasXML;

	/**
	 * var date
	 */
	protected $sunrise;

	/**
	 * var date
	 */
	protected $sunset;

	/**
	 * @var DOMDocument
	 */
	protected $_doc;

	public function __construct(KalturaTvinciDistributionProfile $distributionProfile, $fieldValues)
	{
		$this->distributionProfile = $distributionProfile;
		$this->fieldValues = $fieldValues;
		$this->language = strlen($distributionProfile->language) === 0 ? 'eng' : $distributionProfile->language;

		$this->schemaId = $distributionProfile->schemaId;
		if ( $this->schemaId != 1 && $this->schemaId != 2 )
		{
			$this->schemaId = self::DEFAULT_SCHEMA_ID;
		}
	}

	public function setEntryId( $entryId )						{ $this->entryId = $entryId; }
	public function getEntryId()								{ return $this->entryId; }

	public function setDescription( $description )				{ $this->description = $description; }
	public function getDescription()							{ return $this->description; }

	public function setTitleName( $name )						{ $this->titleName = $name; }
	public function getTitleName()								{ return $this->titleName; }

	public function setReferenceId( $referenceId )				{ $this->referenceId = $referenceId; }
	public function getReferenceId()							{ return $this->referenceId; }

	public function setCreatedAt( $createdAt )					{ $this->createdAt = $createdAt; }
	public function getCreatedAt()								{ return $this->createdAt; }

	public function setPicRatiosArray( $picRatiosArray )		{ $this->picRatiosArray = $picRatiosArray; }
	public function getPicRatiosArray()							{ return $this->picRatiosArray; }

	public function setDefaultThumbnailUrl( $defaultThumbUrl )	{ $this->defaultThumbUrl = $defaultThumbUrl; }
	public function getDefaultThumbnailUrl()					{ return $this->defaultThumbUrl; }

	public function schemaId()									{ return $this->schemaId; }

	public function setMetasXML($metadataXml)					{$this->metasXML = $metadataXml; }
	public function getMetasXML()								{return $this->metasXML;}

	public function setSunrise($sunrise)						{$this->sunrise = $sunrise;}
	public function getSunrise()								{return $this->sunrise;}

	public function setSunset($sunset)							{$this->sunset = $sunset;}
	public function getSunset()									{return $this->sunset;}

	public function setVideoAssetUrl( $name, $url )
	{
		$this->videoAssetToUrlMap[$name] = $url;
	}

	public function buildSubmitFeed()
	{
		return $this->createXml( self::ACTION_SUBMIT );
	}

	public function buildUpdateFeed()
	{
		return $this->createXml( self::ACTION_UPDATE );
	}

	public function buildDeleteFeed()
	{
		return $this->createXml( self::ACTION_DELETE );
	}

	private function createXml($action)
	{
		// Init the document
		$this->_doc = new DOMDocument();
		$this->_doc->formatOutput = true;
		$this->_doc->encoding = "UTF-8";
		// Build the feed
		$feed = $this->_doc->createElement('feed');
		$export = $this->_doc->createElement('export');
		$feed->appendChild($export);

		$media = $this->_doc->createElement('media');
		$export->appendChild($media);

		$this->setAttribute($media, "co_guid", $this->referenceId);
		$this->setAttribute($media, "entry_id", $this->entryId);

		if ($action === self::ACTION_DELETE) {
			$this->setAttribute($media, "action", self::ACTION_DELETE);
		} else {
			$this->setAttribute($media, "action", self::ACTION_SUBMIT);
		}

		if ( $action != self::ACTION_DELETE ) // No need for the following content in case of a delete scenario
		{
			//$isActive = $this->fieldValues[TvinciDistributionField::ACTIVATE_PUBLISHING];
			// agreed that the is_active and erase will be hard coded for first phase
			$this->setAttribute($media, "is_active", "true");
			$this->setAttribute($media, "erase", "false");

			$media->appendChild( $this->createBasicElement() );
			$media->appendChild( $this->createStructureElement() );
			$media->appendChild( $this->createFilesElement() );
		}

		// Wrap as a CDATA section
		$feedAsXml = $this->_doc->saveXML($feed);
		// convert the xml using provided XSLT
		chdir(__DIR__);
		$xslt = file_get_contents("../xml/tvinci_default.xslt");
		$feedAsXml = $this->transformXml($feedAsXml, $xslt);
		$data = $this->_doc->createElement('data');
		$data->appendChild($this->_doc->createCDATASection($feedAsXml));

		// Create the document's root node
		$envelopeRootNode = $this->_doc->createElement('s:Envelope');
		$this->setAttribute($envelopeRootNode,"xmlns:s","http://www.w3.org/2003/05/soap-envelope");
		$this->setAttribute($envelopeRootNode,"xmlns:a","http://www.w3.org/2005/08/addressing");

		$envelopeHeaderNode = $this->_doc->createElement('s:Header');
		$envelopeHeaderActionNode = $this->_doc->createElement('a:Action', 'urn:Iservice/InjestTvinciData');
		$this->setAttribute($envelopeHeaderActionNode,"s:mustUnderstand","1");
		$envelopeHeaderNode->appendChild($envelopeHeaderActionNode);

		$envelopeBodyNode = $this->_doc->createElement('s:Body');
		$injestTvinciDataNode = $this->_doc->createElement('InjestTvinciData');
		$tvinciDataRequestNode = $this->_doc->createElement('request');
		$this->setAttribute($tvinciDataRequestNode,"xmlns:i","http://www.w3.org/2001/XMLSchema-instance");

		$tvinciDataRequestNode->appendChild($this->_doc->createElement('userName', $this->distributionProfile->username));
		$tvinciDataRequestNode->appendChild($this->_doc->createElement('passWord', $this->distributionProfile->password));
		$tvinciDataRequestNode->appendChild($this->_doc->createElement('schemaID', $this->distributionProfile->schemaId));

		// Attach the CDATA section
		$tvinciDataRequestNode->appendChild($data);
		$injestTvinciDataNode->appendChild($tvinciDataRequestNode);
		$envelopeBodyNode->appendChild($injestTvinciDataNode);
		$envelopeRootNode->appendChild($envelopeHeaderNode);
		$envelopeRootNode->appendChild($envelopeBodyNode);

		// Attach the root node to the document
		$this->_doc->appendChild($envelopeRootNode);

		return $this->getXml();
	}


	/**
	 * Result XML:
	 * 	<$name>
	 * 		<value lang="$lang">$value</value>
	 * 	</$name>
	 */
	protected function createValueWithLangElement($name, $value, $lang)
	{
		$valueNode = $this->_doc->createElement('value', $value);
		$this->setAttribute($valueNode, "lang", $lang);

		$namedNode = $this->_doc->createElement($name);
		$namedNode->appendChild($valueNode);

		return $namedNode;
	}

	private function createDateElement($fieldName, $timestamp)
	{
		$formattedDate = date(self::DATE_FORMAT, $timestamp);
		$dateNode = $this->_doc->createElement($fieldName, $formattedDate);
		return $dateNode;
	}

	private function createBasicElement()
	{
		$basicNode = $this->_doc->createElement("basic");
		$basicNode->appendChild( $this->createValueWithLangElement('name', $this->getTitleName(), $this->language));
		$basicNode->appendChild( $this->createValueWithLangElement('description', $this->getDescription(), $this->language));

		// Add default thumbnail
		if ( isset($this->defaultThumbUrl) )
		{
			$thumbnail = $this->_doc->createElement("thumb");
			$this->setAttribute($thumbnail, "url", $this->defaultThumbUrl);
			$basicNode->appendChild( $thumbnail );
		}
		$basicNode->appendChild( $this->createDatesElement());
		$basicNode->appendChild( $this->createPicRatiosElement() );

		return $basicNode;
	}

	private function createDatesElement()
	{
		$datesNode = $this->_doc->createElement("dates");
		$datesNode->appendChild( $this->createDateElement('catalog_start', $this->getSunrise()) );
		$datesNode->appendChild( $this->createDateElement('catalog_end', $this->getSunset()) );
		$datesNode->appendChild( $this->createDateElement('start', $this->getCreatedAt()) );
		$datesNode->appendChild( $this->createDateElement('end', $this->getSunset()) );
		return $datesNode;
	}

	private function createPicRatiosElement()
	{
		$picRatiosNode = $this->_doc->createElement("pic_ratios");

		$picRatiosArray = $this->picRatiosArray;
		foreach ( $picRatiosArray as $picRatio )
		{
			$ratioNode = $this->_doc->createElement("ratio");
			$this->setAttribute($ratioNode, "thumb", $picRatio['url']);
			$this->setAttribute($ratioNode, "ratio", $picRatio['ratio']);
			$picRatiosNode->appendChild( $ratioNode );
		}

		return $picRatiosNode;
	}

	private function createStructureElement()
	{
		$structure = $this->_doc->createElement("structure");
 		$structure->appendChild( $this->createMetasElement() );
 		return $structure;
	}


	private function createMetasElement()
	{
		$kalturaMetaDom = new DOMDocument();
		$kalturaMetaDom->formatOutput = true;
		$kalturaMetaDom->encoding = "UTF-8";
		$tmpMetaStructure = "<tmpMetaStructure>".$this->getMetasXML()."</tmpMetaStructure>";
		$kalturaMetaDom->loadXML($tmpMetaStructure);
		$metas = $this->_doc->createElement("metas");
		foreach ($kalturaMetaDom->firstChild->childNodes as $metaNode) {
			$tmpNode = $this->_doc->importNode($metaNode, true);
			$metas->appendChild($tmpNode);
		}
		return $metas;
	}

	private function createFilesElement()
	{
		$files = $this->_doc->createElement("files");
		foreach ( $this->videoAssetToUrlMap as $name => $url )
		{
			$files->appendChild( $this->createFileElement($name, $url) );
		}
 		return $files;
	}

	private function createFileElement($fileType, $url)
	{
		$fileNode = $this->_doc->createElement("file");

		$this->setAttribute($fileNode, "type", $fileType);
		$this->setAttribute($fileNode, "quality", "HIGH");
		$this->setAttribute($fileNode, "handling_type", "CLIP");
		$this->setAttribute($fileNode, "cdn_name", "Default CDN");
		$this->setAttribute($fileNode, "cdn_code", $url);
		$this->setAttribute($fileNode, "co_guid", $fileType);
		$this->setAttribute($fileNode, "billing_type", 'Tvinci');
//		if ( $this->schemaId() == 2 )
//		{
//			$billingType = self::getSafeFieldValue(TvinciDistributionField::METADATA_BILLING_TYPE, null);
//			if ( $billingType )
//			{
//				$this->setAttribute($fileNode, "billing_type", $billingType);
//			}
//
//			$runtime = self::getSafeArrayValue($this->fieldValues, TvinciDistributionField::METADATA_RUNTIME, 0);
//			$this->setAttribute($fileNode, "assetDuration", $runtime * 60);
//		}

		return $fileNode;
	}

	private function setAttribute($node, $attribName, $attribValue)
	{
		$node->setAttribute($attribName, htmlspecialchars($attribValue, ENT_COMPAT, 'UTF-8')); // ENT_COMPAT to leave single-quotes as is
	}

	public function __toString()
	{
		return $this->_doc->saveXML();
	}

	public function getXml()
	{
		return $this->_doc->saveXML();
	}


	/**
	 * Transform XML using XSLT
	 * @param string $xmlStr
	 * @param string $xslStr
	 * @return string the result XML
	 */
	protected function transformXml($xmlStr, $xslStr)
	{
		$xmlObj = new DOMDocument();
		if (!$xmlObj->loadXML($xmlStr))
		{
			throw new Exception('Error loading source XML');
		}

		$xslObj = new DOMDocument();
		if(!$xslObj->loadXML($xslStr))
		{
			throw new Exception('Error loading XSLT');
		}

		$proc = new XSLTProcessor;
		$proc->registerPHPFunctions(kXml::getXslEnabledPhpFunctions());
		$proc->importStyleSheet($xslObj);

		$resultXmlObj = $proc->transformToDoc($xmlObj);
		if (!$resultXmlObj)
		{
			throw new Exception('Error transforming XML');
			return null;
		}

		$resultXmlStr = $resultXmlObj->saveXML();

		// DEBUG logs
		// KalturaLog::debug('source xml = '.$xmlStr);
		// KalturaLog::debug('xslt = '.$xslStr);
		// KalturaLog::debug('result xml = '.$resultXmlStr);

		return $resultXmlStr;
	}
}