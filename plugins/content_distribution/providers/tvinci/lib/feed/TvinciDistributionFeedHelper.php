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
		if($this->distributionProfile->ipadnewPpvModule)
			$arguments["ipadnewPpvModule"] = $this->distributionProfile->ipadnewPpvModule;
		if($this->distributionProfile->ipadnewFileName)
			$arguments["ipadnewTypeName"] = $this->distributionProfile->ipadnewFileName;
		if($this->distributionProfile->ismPpvModule)
			$arguments["ismPpvModule"] = $this->distributionProfile->ismPpvModule;
		if($this->distributionProfile->ismFileName)
			$arguments["ismTypeName"] = $this->distributionProfile->ismFileName;
		if($this->distributionProfile->iphonenewPpvModule)
			$arguments["iphonenewPpvModule"] = $this->distributionProfile->iphonenewPpvModule;
		if($this->distributionProfile->iphonenewFileName)
			$arguments["iphonenewTypeName"] = $this->distributionProfile->iphonenewFileName;
		if($this->distributionProfile->mbrPpvModule)
			$arguments["mbrPpvModule"] = $this->distributionProfile->mbrPpvModule;
		if($this->distributionProfile->mbrFileName)
			$arguments["mbrTypeName"] = $this->distributionProfile->mbrFileName;
		if($this->distributionProfile->dashPpvModule)
			$arguments["dashPpvModule"] = $this->distributionProfile->dashPpvModule;
		if($this->distributionProfile->dashFileName)
			$arguments["dashTypeName"] = $this->distributionProfile->dashFileName;
		if($this->distributionProfile->widevinePpvModule)
			$arguments["widevinePpvModule"] = $this->distributionProfile->widevinePpvModule;
		if($this->distributionProfile->widevineFileName)
			$arguments["widevineTypeName"] = $this->distributionProfile->widevineFileName;
		if($this->distributionProfile->widevineMbrPpvModule)
			$arguments["widevineMbrPpvModule"] = $this->distributionProfile->widevineMbrPpvModule;
		if($this->distributionProfile->widevineMbrFileName)
			$arguments["widevineMbrFileName"] = $this->distributionProfile->widevineMbrFileName;
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