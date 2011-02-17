<?php
/**
 * @package Core
 * @subpackage model.data
 */
class siteRestriction extends baseRestriction
{
	const SITE_RESTRICTION_TYPE_RESTRICT_LIST = 0;
	const SITE_RESTRICTION_TYPE_ALLOW_LIST = 1;
	
	/**
	 * Allow or restrict
	 * 
	 * @var int
	 */
	protected $type;
	
	/**
	 * @var string
	 */
	protected $siteList;

	/**
	 * @param int $type
	 */
	function setType($type)
	{
		$this->type = $type;
	}
	
	/**
	 * @param string $siteList
	 */
	function setSiteList($siteList)
	{
		$list = explode(",", $siteList);
		$newList = array();
		foreach($list as $item)
		{
			if (trim($item) != "")
			{
				$item = strtolower(trim($item));
				// starts with "a-z" and/or "0-9", following with a "." and not ending with "."
				if (preg_match("/^([a-z0-9\-]+\.?)+[^\.]$/", $item))
					$newList[$item] = $item; // to remove duplicates
			}
		}
		
		$this->siteList = implode(",", $newList);
	}
	
	/**
	 * @return int
	 */
	function getType()
	{
		return $this->type;	
	}
	
	/**
	 * @return string
	 */
	function getSiteList()
	{
		return $this->siteList;
	}
	
	/**
	 * return the domain name from a referrer URL string
	 *
	 * @return string
	 */
	public static function extractDomainFromReferrer($referrerDomain)
	{
		$referrerDetails = parse_url($referrerDomain);
		if(isset($referrerDetails['host']))
		{
			$suspectedDomain = $referrerDetails['host'];
		}
		elseif(isset($referrerDetails['path']))
		{
			// parse_url could not extract domain, but returned path
			// we validate that this path could be considered a domain
			$suspectedDomain = rtrim($referrerDetails['path'], '/'); // trim trailing slashes. example: www.kaltura.com/test.php
			
			// stop string at first slash. example: httpssss/google.com - malformed url...
			if (strpos($suspectedDomain, "/") !== false)
			{
				$suspectedDomain = substr($suspectedDomain, 0, strpos($suspectedDomain, "/"));
			}
		}
		else // empty path and host, cannot parse the URL
		{
			return null;
		}
		
		// some urls might return host or path which is not yet clean for comparison with user's input
		if (strpos($suspectedDomain, "?") !== false)
		{
			$suspectedDomain = substr($suspectedDomain, 0, strpos($suspectedDomain, "?"));
		}
		if (strpos($suspectedDomain, "#") !== false)
		{
			$suspectedDomain = substr($suspectedDomain, 0, strpos($suspectedDomain, "#"));
		}
		if (strpos($suspectedDomain, "&") !== false)
		{
			$suspectedDomain = substr($suspectedDomain, 0, strpos($suspectedDomain, "&"));
		}
		return $suspectedDomain;
	}
	
	/**
	 * validate a referrer either in 'restricted type' or 'allowed type'
	 * 
	 * @return bool
	 */
	function isValid()
	{
		// get context info
		$accessControlScope = $this->getAccessControlScope();
		$referrerDomain = $accessControlScope->getReferrer();

		//$referrerDomain = preg_replace("/http[s]?\:\/\/([a-z0-9\-\.]*)\//", "\${1}", $accessControlScope->getReferrer());
		
		$referrerDomain = $this->extractDomainFromReferrer($referrerDomain);
		
		// extract site list from restriction object
		if ($this->siteList === "")
			$sites = array();
		else
			$sites = explode(",", $this->siteList);
		
		// if there is no referrer, request is not valid
		if (strlen($referrerDomain) == 0)
			return false;
			
		// no site lists, use defaults by the restriction type
		if (count($sites) == 0)
		{
			if ($this->type == self::SITE_RESTRICTION_TYPE_RESTRICT_LIST)
				return true;
			else if ($this->type == self::SITE_RESTRICTION_TYPE_ALLOW_LIST)
				return false;
		}
		
		// match referrer to restricted domain list
		if ($this->type == self::SITE_RESTRICTION_TYPE_RESTRICT_LIST)
		{
			foreach($sites as $site)
			{
				if ($this->isReferrerOnSameDomain($referrerDomain, $site))
				{
					return false;
				}
			}
			return true;
		}
		else if ($this->type == self::SITE_RESTRICTION_TYPE_ALLOW_LIST) // match referrer to allowed domain list
		{
			foreach($sites as $site)
			{
				if ($this->isReferrerOnSameDomain($referrerDomain, $site))
				{
					return true;
				}
			}
			return false;
		}
	}
	
	/**
	 * Referrer www.facebook.com is on the domain facebook.com, but referrer facebook.com is not the domain www.facebook.com
	 * 
	 * @param $referrer
	 * @param $domain
	 * @return bool
	 */
	private function isReferrerOnSameDomain($referrer, $domain)
	{
		if ($referrer === $domain)
			return true;
		else 
			return (strpos($referrer, ".".$domain) !== false);
	}
}