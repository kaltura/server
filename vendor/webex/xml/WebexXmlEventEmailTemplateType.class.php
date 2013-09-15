<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEventEmailTemplateType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXml
	 */
	protected $subject;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $from;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $replyTo;
	
	/**
	 *
	 * @var string
	 */
	protected $content;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'subject':
				return 'WebexXml';
	
			case 'from':
				return 'WebexXml';
	
			case 'replyTo':
				return 'WebexXml';
	
			case 'content':
				return 'string';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'subject',
			'from',
			'replyTo',
			'content',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'emailTemplateType';
	}
	
	/**
	 * @param WebexXml $subject
	 */
	public function setSubject(WebexXml $subject)
	{
		$this->subject = $subject;
	}
	
	/**
	 * @return WebexXml $subject
	 */
	public function getSubject()
	{
		return $this->subject;
	}
	
	/**
	 * @param WebexXml $from
	 */
	public function setFrom(WebexXml $from)
	{
		$this->from = $from;
	}
	
	/**
	 * @return WebexXml $from
	 */
	public function getFrom()
	{
		return $this->from;
	}
	
	/**
	 * @param WebexXml $replyTo
	 */
	public function setReplyTo(WebexXml $replyTo)
	{
		$this->replyTo = $replyTo;
	}
	
	/**
	 * @return WebexXml $replyTo
	 */
	public function getReplyTo()
	{
		return $this->replyTo;
	}
	
	/**
	 * @param string $content
	 */
	public function setContent($content)
	{
		$this->content = $content;
	}
	
	/**
	 * @return string $content
	 */
	public function getContent()
	{
		return $this->content;
	}
	
}
		
