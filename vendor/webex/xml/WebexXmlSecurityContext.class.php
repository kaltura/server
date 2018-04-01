<?php

class WebexXmlSecurityContext
{
	protected $uid; // WebEx username
	protected $pwd; // WebEx password
	protected $sid; // Site SiteID
	protected $pid; // Site PartnerID
	
	public function __toString()
	{
		$xml = "<securityContext>";
		$xml .= "<webExID>$this->uid</webExID>";
		$xml .= "<password>$this->pwd</password>";
		$xml .= "<siteID>$this->sid</siteID>";
		$xml .= "<partnerID>$this->pid</partnerID>";
		$xml .= "<returnAdditionalInfo>TRUE</returnAdditionalInfo>";
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
}
