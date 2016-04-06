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

			$tagname = $tagsDoc->createElement('tagname');
			$tagnameText = $tagsDoc->createTextNode($tag->tagname);
			$tagname->appendChild($tagnameText);
			$tagXML->appendChild($tagname);

			$extension = $tagsDoc->createElement('extension');
			$extensionText = $tagsDoc->createTextNode($tag->extension);
			$extension->appendChild($extensionText);
			$tagXML->appendChild($extension);

			$protocol = $tagsDoc->createElement('protocol');
			$protocolText = $tagsDoc->createTextNode($tag->protocol);
			$protocol->appendChild($protocolText);
			$tagXML->appendChild($protocol);

			$format = $tagsDoc->createElement('format');
			$formatText = $tagsDoc->createTextNode($tag->format);
			$format->appendChild($formatText);
			$tagXML->appendChild($format);

			$typename = $tagsDoc->createElement('typename');
			$typenameText = $tagsDoc->createTextNode($tag->filename);
			$typename->appendChild($typenameText);
			$tagXML->appendChild($typename);

			$ppvmodule = $tagsDoc->createElement('ppvmodule');
			$ppvmoduleText = $tagsDoc->createTextNode($tag->ppvmodule);
			$ppvmodule->appendChild($ppvmoduleText);
			$tagXML->appendChild($ppvmodule);

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
		KalturaLog::err("@@NA arguments [".print_r($arguments,true)."]");

		return $arguments;
	}

	private function createXml()
	{
		// Init the document
		$this->_doc = new DOMDocument();
		$this->_doc->formatOutput = true;
		$this->_doc->encoding = "UTF-8";

		$feedAsXml = kMrssManager::getEntryMrssXml($this->entry);

		if ($this->distributionProfile->xsltFile &&
			(strlen($this->distributionProfile->xsltFile) !== 0) ) {
			// custom non empty xslt
			$xslt = $this->distributionProfile->xsltFile;
		} else {
			$xslt = file_get_contents(__DIR__."/../xml/tvinci_default.xslt");
		}
		$feedAsString = kXml::transformXmlUsingXslt($feedAsXml->saveXML(), $xslt, $this->createArgumentsForXSLT());

		$data = $this->_doc->createElement('data');
		$data->appendChild($this->_doc->createCDATASection($feedAsString));

		// Create the document's root node
		$envelopeRootNode = $this->_doc->createElement('s:Envelope');
		$this->setAttribute($envelopeRootNode,"xmlns:s","http://www.w3.org/2003/05/soap-envelope");
		$this->setAttribute($envelopeRootNode,"xmlns:a","http://www.w3.org/2005/08/addressing");

		$envelopeHeaderNode = $this->_doc->createElement('s:Header');
		$envelopeHeaderActionNode = $this->_doc->createElement('a:Action', 'urn:Iservice/IngestTvinciData');
		$this->setAttribute($envelopeHeaderActionNode,"s:mustUnderstand","1");
		$envelopeHeaderNode->appendChild($envelopeHeaderActionNode);

		$envelopeBodyNode = $this->_doc->createElement('s:Body');
		$ingestTvinciDataNode = $this->_doc->createElement('IngestTvinciData');
		$tvinciDataRequestNode = $this->_doc->createElement('request');
		$this->setAttribute($tvinciDataRequestNode,"xmlns:i","http://www.w3.org/2001/XMLSchema-instance");

		$tvinciDataRequestNode->appendChild($this->_doc->createElement('userName', $this->distributionProfile->username));
		$tvinciDataRequestNode->appendChild($this->_doc->createElement('passWord', $this->distributionProfile->password));

		// Attach the CDATA section
		$tvinciDataRequestNode->appendChild($data);
		$ingestTvinciDataNode->appendChild($tvinciDataRequestNode);
		$envelopeBodyNode->appendChild($ingestTvinciDataNode);
		$envelopeRootNode->appendChild($envelopeHeaderNode);
		$envelopeRootNode->appendChild($envelopeBodyNode);

		// Attach the root node to the document
		$this->_doc->appendChild($envelopeRootNode);

		return $this->getXml();
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

}