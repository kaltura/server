<?php
/**
 *  This class will help you validate if domains are allowing Kaltura.
 *  @package infra
 *  @subpackage general
 */

class kSpfMailerValidator
{
	const MAILER_KALTURA_COM = 'mailer.kaltura.com';
	const SPF = 'v=spf1';
	const TXT = 'txt';

	/*
	 *  Get list of domains that are not allowing Kaltura
	 *  @param array $domains
	 *  @return array $domainsNotAllowed
	 */
	public static function getDomainsNotAllowed($domains)
	{
		$domainsNotAllowed = array();
		foreach ($domains as $domain)
		{
			$validationResult = self::validateDomain($domain);
			if($validationResult)
			{
				KalturaLog::debug("$domain allows Kaltura to send on behalf of it\n");
			}
			else
			{
				$domainsNotAllowed[$domain] = $domain;
				KalturaLog::debug("$domain is not allowing Kaltura\n");
			}
		}
		return $domainsNotAllowed;
	}

	/*
	 *  Check if specific domain is allowing Kaltura.
	 *  @param string $domain
	 *  @return boolean
	 */
	public static function validateDomain($domain)
	{
		$dnsRecords = dns_get_record($domain, DNS_TXT);
		if (!$dnsRecords)
		{
			return false;
		}
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
