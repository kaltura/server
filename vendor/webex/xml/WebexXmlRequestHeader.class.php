<?php
require_once(__DIR__ . '/WebexXmlSecurityContext.class.php');

class WebexXmlRequestHeader
{
	/**
	 * @var WebexXmlSecurityContext
	 */
	protected $securityContext;
	
	public function __construct(WebexXmlSecurityContext $securityContext)
	{
		$this->securityContext = $securityContext;
	}
	
	public function __toString()
	{
		return "<header>$this->securityContext</header>";
	}
}
