<?php
/**
 * @package External
 * @subpackage Marketo
 */
class MarketoApiService extends MktMktowsApiService
{
	const MKTOWS_NAMESPACE = 'http://www.marketo.com/mktows/';
	
	public $accessKey;
	public $secretKey;
	
	public function setCredentials($accessKey, $secretKey)
	{
		$this->accessKey = $accessKey;
		$this->secretKey = $secretKey;
		$this->__setSoapHeaders($this->__getAuthenticationHeader());
	}
	
	public function createAttribute($attrName, $attrValue, $attrType = null)
	{
		$attr = new Attribute();
		$attr->attrName = $attrName;
		$attr->attrValue = $attrValue;
		$attr->attrType = $attrType;
		return $attr;
	}
	
	protected function __getAuthenticationHeader()
	{
		$dtObj = new DateTime('now');
		$timestamp = $dtObj->format(DATE_W3C);

		$encryptString = $timestamp.$this->accessKey;
		
		$signature = hash_hmac('sha1', $encryptString, $this->secretKey);

		$attrs = new stdClass();
		$attrs->mktowsUserId = $this->accessKey;
		$attrs->requestSignature = $signature;
		$attrs->requestTimestamp = $timestamp;
		
		$soapHdr = new SoapHeader(self::MKTOWS_NAMESPACE, 'AuthenticationHeader', $attrs);
		return $soapHdr;
	}
} 