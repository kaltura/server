<?php
/**
 * 
 * Internal Tools Service
 * 
 * @service kalturaInternalToolsSystemHelper
 * @package plugins.KalturaInternalTools
 * @subpackage api.services
 */
class KalturaInternalToolsSystemHelperService extends KalturaBaseService
{

	/**
	 * KS from Secure String
	 * @action fromSecureString
	 * @param string $str
	 * @return KalturaInternalToolsSession
	 * 
	 */
	public function fromSecureStringAction($str)
	{
		$ks =  ks::fromSecureString ( $str );
		
		$ksFromSecureString = new KalturaInternalToolsSession();
		$ksFromSecureString->fromObject($ks, $this->getResponseProfile());
		
		return $ksFromSecureString;
	}
	
	/**
	 * from ip to country
	 * @action iptocountry
	 * @param string $remote_addr
	 * @return string
	 * 
	 */
	public function iptocountryAction($remote_addr)
	{
		$ip_geo = new myIPGeocoder();
		$res = $ip_geo->iptocountry($remote_addr); 
		return $res;
	}
	
	/**
	 * @action getRemoteAddress
	 * @return string
	 * 
	 */
	public function getRemoteAddressAction()
	{
		$remote_addr = requestUtils::getRemoteAddress();
		return $remote_addr;	
	}
}