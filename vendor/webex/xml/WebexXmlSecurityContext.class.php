<?php

class WebexXmlSecurityContext
{
	protected $uid; // WebEx username
	protected $pwd; // WebEx password
	protected $sid; // Site SiteID
	protected $pid; // Site PartnerID
	protected $siteName; // Site Name

	public function __toString()
	{
		$xml = "<securityContext>";
		$xml .= "<webExID>$this->uid</webExID>";
		$xml .= "<password>$this->pwd</password>";
		if(empty($this->siteName))
		{
			$xml .= "<siteID>$this->sid</siteID>";
			$xml .= "<partnerID>$this->pid</partnerID>";
		}
		else
			$xml .= "<siteName>$this->siteName</siteName>";

		$xml .= "</securityContext>";
		
		return $xml;
	}
	
	/**
	 * @param field_type $uid
	 */
	public function setUid($uid)
	{
		$this->uid = $uid;
	}

	/**
	 * @param field_type $pwd
	 */
	public function setPwd($pwd)
	{
		$this->pwd = $pwd;
	}

	/**
	 * @param field_type $sid
	 */
	public function setSid($sid)
	{
		$this->sid = $sid;
	}

	/**
	 * @param field_type $pid
	 */
	public function setPid($pid)
	{
		$this->pid = $pid;
	}

	/**
	 * @param field_type $siteName
	 */
	public function setSiteName($siteName)
	{
		$this->siteName = $siteName;
	}
}
