<?php
require_once(__DIR__ . '/WebexXmlRequestBodyContent.class.php');
require_once(__DIR__ . '/WebexXmlAssignWebACDQueues.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXml.class.php');

class WebexXmlAssignWebACDQueuesRequest extends WebexXmlRequestBodyContent
{
	/**
	 *
	 * @var string
	 */
	protected $webExId;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXml>
	 */
	protected $queue;
	
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'webExId',
			'queue',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'webExId',
			'queue',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getServiceType()
	 */
	protected function getServiceType()
	{
		return 'use';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getRequestType()
	 */
	public function getRequestType()
	{
		return 'use:AssignWebACDQueues';
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestBodyContent::getContentType()
	 */
	public function getContentType()
	{
		return 'WebexXmlAssignWebACDQueues';
	}
	
	/**
	 * @param string $webExId
	 */
	public function setWebExId($webExId)
	{
		$this->webExId = $webExId;
	}
	
	/**
	 * @param WebexXmlArray<WebexXml> $queue
	 */
	public function setQueue(WebexXmlArray $queue)
	{
		if($queue->getType() != 'WebexXml')
			throw new WebexXmlException(get_class($this) . "::queue must be of type WebexXml");
		
		$this->queue = $queue;
	}
	
}
		
