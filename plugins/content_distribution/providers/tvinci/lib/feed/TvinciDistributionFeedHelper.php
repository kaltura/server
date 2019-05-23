<?php
/**
 * @package plugins.tvinciDistribution
 * @subpackage lib
 */
class TvinciDistributionFeedHelper
{
	const ACTION_SUBMIT = 'insert';
	const ACTION_UPDATE = 'update';
	const ACTION_DELETE = 'delete';

	const EMPTY_PLACE_HOLDER = '@EMPTY_PLACE_HOLDER@';
	const DELETE_XML = "<feed><export><media co_guid=\"COGUID\" entry_id=\"ENTRYID\" action=\"delete\" is_active=\"true\" erase=\"true\"/></export></feed>";

	/**
	 * var KalturaTvinciDistributionProfile
	 */
	protected $distributionProfile;

	/**
	 * var baseEntry
	 */
	protected $entry;


	/**
	 * @var DOMDocument
	 */
	protected $_doc;

	public function __construct(KalturaTvinciDistributionProfile $distributionProfile, BaseEntry $entry)
	{
		$this->distributionProfile = $distributionProfile;
		$this->entry = $entry;
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

	private function createArgumentsForXSLT()
	{
		$partnerPath = myPartnerUtils::getUrlForPartner($this->entry->getPartnerId(), $this->entry->getSubpId());
		$prefix = myPartnerUtils::getCdnHost($this->entry->getPartnerId(), null , 'api')
			. $partnerPath
			. "/playManifest"
			. "/entryId/";
		$arguments = array(
			"distributionProfileId" => $this->distributionProfile->id,
			"playManifestPrefix" => $prefix);

		$tagsDoc = new DOMDocument();
		$tagsConfiguration = $tagsDoc->createElement('tagsconfiguration');
		foreach($this->distributionProfile->tags as $tag)
		{
			/**
			 * @var KalturaTvinciDistributionTag $tag
			 */
			$tagXML = $tagsDoc->createElement('tag');

			$tagXML->appendChild($this->createAppendXml($tag, $tagsDoc, 'tagname', 'tagname'));
			$tagXML->appendChild($this->createAppendXml($tag, $tagsDoc, 'extension', 'extension'));
			$tagXML->appendChild($this->createAppendXml($tag, $tagsDoc, 'protocol', 'protocol'));
			$tagXML->appendChild($this->createAppendXml($tag, $tagsDoc, 'format', 'format'));
			$tagXML->appendChild($this->createAppendXml($tag, $tagsDoc, 'typename', 'filename'));
			$tagXML->appendChild($this->createAppendXml($tag, $tagsDoc, 'ppvmodule', 'ppvmodule'));

			$tagsConfiguration->appendChild($tagXML);

			if ($tag->tagname == 'ism')
			{
				$arguments["ismPpvModule"] = $tag->ppvmodule;
				$arguments["ismTypeName"] = $tag->filename;
			}
			if ($tag->tagname == 'ipadnew')
			{
				$arguments["ipadnewPpvModule"] = $tag->ppvmodule;
				$arguments["ipadnewTypeName"] = $tag->filename;
			}
			if ($tag->tagname == 'iphonenew')
			{
				$arguments["iphonenewPpvModule"] = $tag->ppvmodule;
				$arguments["iphonenewTypeName"] = $tag->filename;
			}
			if ($tag->tagname == 'mbr')
			{
				$arguments["mbrPpvModule"] = $tag->ppvmodule;
				$arguments["mbrTypeName"] = $tag->filename;
			}
			if ($tag->tagname == 'dash')
			{
				$arguments["dashPpvModule"] = $tag->ppvmodule;
				$arguments["dashTypeName"] = $tag->filename;
			}
			if ($tag->tagname == 'widevine')
			{
				$arguments["widevinePpvModule"] = $tag->ppvmodule;
				$arguments["widevineTypeName"] = $tag->filename;
			}
			if ($tag->tagname == 'widevineMbrFileName')
			{
				$arguments["widevineMbrPpvModule"] = $tag->ppvmodule;
				$arguments["widevineMbrTypeName"] = $tag->filename;
			}
		}
		$tagsDoc->appendChild($tagsConfiguration);
		$arguments['tagsparam'] = $tagsDoc->saveXML();

		return $arguments;
	}

	private function createXml( $action )
	{
		$useCatalogFormat = true;
		if($this->distributionProfile->innerType != TvinciDistributionProfile::INNER_TYPE_CATALOG)
		{
			$useCatalogFormat = false;
		}

		// Init the document
		$this->_doc = new DOMDocument();
		$this->_doc->formatOutput = true;
		if($useCatalogFormat)
		{
			$this->_doc->encoding = "UTF-8";
		}

		$feedAsXml = kMrssManager::getEntryMrssXml($this->entry);
		self::addEmptyValueInMetadataObjects($feedAsXml);

		if ( $action != self::ACTION_DELETE )
		{
			if ($this->distributionProfile->xsltFile &&
				(strlen($this->distributionProfile->xsltFile) !== 0) ) {
				// custom non empty xslt
				$xslt = $this->distributionProfile->xsltFile;
			} else {
				$xslt = file_get_contents(__DIR__."/../xml/tvinci_default.xslt");
			}
			$feedAsString = kXml::transformXmlUsingXslt($feedAsXml->saveXML(), $xslt, $this->createArgumentsForXSLT());
		}
		else {
			$coguid = $this->entry->getReferenceID();
			if ( !$coguid )
				$coguid = $this->entry->getId();

			$feedAsString = str_replace("COGUID", $coguid, self::DELETE_XML);
			$feedAsString = str_replace("ENTRYID", $this->entry->getId(), $feedAsString);
		}

		$feedAsString = str_replace(self::EMPTY_PLACE_HOLDER, '', $feedAsString);
		$data = $this->_doc->createElement('data');
		if(!$useCatalogFormat)
		{
			$this->setAttribute($data,"xmlns","");
		}
		$data->appendChild($this->_doc->createCDATASection($feedAsString));

		list($envelopeRootNode, $envelopeHeaderNode) = $this->setEnvelopeHeaders($useCatalogFormat);
		$this->setEnvelopeBody($envelopeRootNode, $envelopeHeaderNode, $data, $useCatalogFormat);

		return $this->getXml($useCatalogFormat);
	}

	protected function setEnvelopeHeaders($useCatalogFormat)
	{
		// Create the document's root node
		$envelopeRootNode = $this->_doc->createElement('s:Envelope');
		if($useCatalogFormat)
		{
			$this->setAttribute($envelopeRootNode,"xmlns:s","http://www.w3.org/2003/05/soap-envelope");
			$this->setAttribute($envelopeRootNode,"xmlns:a","http://www.w3.org/2005/08/addressing");

			$envelopeHeaderNode = $this->_doc->createElement('s:Header');
			$envelopeHeaderActionNode = $this->_doc->createElement('a:Action', 'urn:Iservice/IngestTvinciData');
			$this->setAttribute($envelopeHeaderActionNode,"s:mustUnderstand","1");
			$envelopeHeaderNode->appendChild($envelopeHeaderActionNode);
		}
		else
		{
			$this->setAttribute($envelopeRootNode,"xmlns:s","http://schemas.xmlsoap.org/soap/envelope/");
			$envelopeHeaderNode = $this->_doc->createElement('s:Header');
		}

		return array($envelopeRootNode, $envelopeHeaderNode);
	}

	protected function setEnvelopeBody($envelopeRootNode, $envelopeHeaderNode, $data, $useCatalogFormat)
	{
		$envelopeBodyNode = $this->_doc->createElement('s:Body');
		$ingestTvinciDataNode = $this->_doc->createElement('IngestTvinciData');
		if(!$useCatalogFormat)
		{
			$this->setAttribute($ingestTvinciDataNode,"xmlns","http://tempuri.org/");
		}
		$tvinciDataRequestNode = $this->_doc->createElement('request');
		$this->setAttribute($tvinciDataRequestNode,"xmlns:i","http://www.w3.org/2001/XMLSchema-instance");

		$tvinciDataUserNode = $this->_doc->createElement('userName', $this->distributionProfile->username);
		$tvinciDataPasswordNode = $this->_doc->createElement('passWord', $this->distributionProfile->password);
		if(!$useCatalogFormat)
		{
			$this->setAttribute($tvinciDataUserNode,"xmlns","");
			$this->setAttribute($tvinciDataPasswordNode,"xmlns","");
		}
		$tvinciDataRequestNode->appendChild($tvinciDataUserNode);
		$tvinciDataRequestNode->appendChild($tvinciDataPasswordNode);

		// Attach the CDATA section
		$tvinciDataRequestNode->appendChild($data);
		$ingestTvinciDataNode->appendChild($tvinciDataRequestNode);
		$envelopeBodyNode->appendChild($ingestTvinciDataNode);
		$envelopeRootNode->appendChild($envelopeHeaderNode);
		$envelopeRootNode->appendChild($envelopeBodyNode);

		// Attach the root node to the document
		$this->_doc->appendChild($envelopeRootNode);
	}

	private static function addEmptyValueInMetadataObjects($feedAsXmlForEntry)
	{
		$metadataObjects = $feedAsXmlForEntry->customData;
		if ($metadataObjects)
		{
			foreach($metadataObjects as $metadataObject)
				self::addEmptyValueInMetadataObject($metadataObject);
		}
	}

	private static function addEmptyValueInMetadataObject($metadataObject)
	{
		$metadataProfileId = kXml::getXmlAttributeAsInt($metadataObject, 'metadataProfileId');
		$metadataProfile = MetadataProfilePeer::retrieveByPK($metadataProfileId);
		if (!$metadataProfile)
			return;

		$metadataFieldsKeys = $metadataProfile->getMetadataFieldsKeys();
		$metadataFieldsKeys = array_flip($metadataFieldsKeys); //because we only need the names of the fields
		$entryKeys = (array)$metadataObject->metadata;
		$diffKeys = array_diff_key($metadataFieldsKeys, $entryKeys);

		if (count($diffKeys) > 0)
		{
			KalturaLog::debug("Adding fields to metadata with profileID [$metadataProfileId] with the keys: " .print_r($diffKeys, true));
			foreach($diffKeys as $key => $val)
				$metadataObject->metadata->addChild($key, self::EMPTY_PLACE_HOLDER);
		}
	}

	

	private function setAttribute($node, $attribName, $attribValue)
	{
		$node->setAttribute($attribName, htmlspecialchars($attribValue, ENT_COMPAT, 'UTF-8')); // ENT_COMPAT to leave single-quotes as is
	}

	public function __toString()
	{
		return $this->_doc->saveXML();
	}

	public function getXml($useCatalogFormat)
	{
		if($useCatalogFormat)
		{
			return $this->_doc->saveXML();
		}

		return $this->_doc->saveXML($this->_doc->documentElement);
	}
	
	private function createAppendXml($tag, $tagsDoc, $xmlNodeName, $propertyName)
	{
		$nameNode = $tagsDoc->createElement($xmlNodeName);
		$nodeText = $tagsDoc->createTextNode($tag->{$propertyName});
		$nameNode->appendChild($nodeText);
		return $nameNode;
	}

}