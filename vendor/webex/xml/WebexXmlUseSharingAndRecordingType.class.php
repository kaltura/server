<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlUseSharingAndRecordingType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlComSharingViewType
	 */
	protected $sharingView;
	
	/**
	 *
	 * @var WebexXmlComSharingColorType
	 */
	protected $sharingColor;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $recording;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'sharingView':
				return 'WebexXmlComSharingViewType';
	
			case 'sharingColor':
				return 'WebexXmlComSharingColorType';
	
			case 'recording':
				return 'WebexXml';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'sharingView',
			'sharingColor',
			'recording',
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
		return 'sharingAndRecordingType';
	}
	
	/**
	 * @param WebexXmlComSharingViewType $sharingView
	 */
	public function setSharingView(WebexXmlComSharingViewType $sharingView)
	{
		$this->sharingView = $sharingView;
	}
	
	/**
	 * @return WebexXmlComSharingViewType $sharingView
	 */
	public function getSharingView()
	{
		return $this->sharingView;
	}
	
	/**
	 * @param WebexXmlComSharingColorType $sharingColor
	 */
	public function setSharingColor(WebexXmlComSharingColorType $sharingColor)
	{
		$this->sharingColor = $sharingColor;
	}
	
	/**
	 * @return WebexXmlComSharingColorType $sharingColor
	 */
	public function getSharingColor()
	{
		return $this->sharingColor;
	}
	
	/**
	 * @param WebexXml $recording
	 */
	public function setRecording(WebexXml $recording)
	{
		$this->recording = $recording;
	}
	
	/**
	 * @return WebexXml $recording
	 */
	public function getRecording()
	{
		return $this->recording;
	}
	
}
		
