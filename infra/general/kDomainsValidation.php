<?php
/**
 * 	This class will help you validate if domains are allowing Kaltura.
 *  @package infra
 *  @subpackage general
 */

class kDomainsValidation
{
	const MAILER_KALTURA_COM = 'mailer.kaltura.com';
	const SPF = 'v=spf1';
	const AT = '@';
	const TXT = 'txt';

	/*
	 * 	Get list of domains that are not allowing Kaltura
	 *  @param array $emailsList
	 *  @return array $domainsNotAllowed
	 */
	public static function getDomainsNotAllowed($emailsList)
	{
		$domainsNotAllowed = array();
		foreach ($emailsList as $email)
		{
			$domainPos = strpos($email,self::AT);
			if ($domainPos == false || $domainPos === strlen($email) - 1) //validate domain position exist
			{
				$domainsNotAllowed[$email] = $email;
				continue;
			}
			$domain = substr($email, $domainPos + 1);
			$validationResult = self::validateDomain($domain);
			if($validationResult)
			{
				KalturaLog::debug(print_r("$domain allows Kaltura to send on behalf of it\n",true));
			}
			else
			{
				$domainsNotAllowed[$domain] = $domain;
				KalturaLog::debug(print_r("$domain is not allowing Kaltura\n",true));
			}
		}
		return $domainsNotAllowed;
	}

	/*
	 * Check if specific domain is allowing Kaltura.
	 * 	@param string $domain
	 *  @return boolean
	 */
	public static function validateDomain($domain)
	{
		$dnsRecords = dns_get_record($domain, DNS_TXT);
		foreach($dnsRecords as $record)
		{
			if((strpos($record[self::TXT], self::SPF) !== false)
				&& (strpos($record[self::TXT], self::MAILER_KALTURA_COM) !== false))
			{
				return true;
			}
		}
		return false;
	}
}