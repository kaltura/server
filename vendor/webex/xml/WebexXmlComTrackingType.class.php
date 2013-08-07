<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlComTrackingType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXml
	 */
	protected $trackingCode1;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $trackingCode2;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $trackingCode3;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $trackingCode4;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $trackingCode5;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $trackingCode6;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $trackingCode7;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $trackingCode8;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $trackingCode9;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $trackingCode10;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'trackingCode1':
				return 'WebexXml';
	
			case 'trackingCode2':
				return 'WebexXml';
	
			case 'trackingCode3':
				return 'WebexXml';
	
			case 'trackingCode4':
				return 'WebexXml';
	
			case 'trackingCode5':
				return 'WebexXml';
	
			case 'trackingCode6':
				return 'WebexXml';
	
			case 'trackingCode7':
				return 'WebexXml';
	
			case 'trackingCode8':
				return 'WebexXml';
	
			case 'trackingCode9':
				return 'WebexXml';
	
			case 'trackingCode10':
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
			'trackingCode1',
			'trackingCode2',
			'trackingCode3',
			'trackingCode4',
			'trackingCode5',
			'trackingCode6',
			'trackingCode7',
			'trackingCode8',
			'trackingCode9',
			'trackingCode10',
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
		return 'trackingType';
	}
	
	/**
	 * @param WebexXml $trackingCode1
	 */
	public function setTrackingCode1(WebexXml $trackingCode1)
	{
		$this->trackingCode1 = $trackingCode1;
	}
	
	/**
	 * @return WebexXml $trackingCode1
	 */
	public function getTrackingCode1()
	{
		return $this->trackingCode1;
	}
	
	/**
	 * @param WebexXml $trackingCode2
	 */
	public function setTrackingCode2(WebexXml $trackingCode2)
	{
		$this->trackingCode2 = $trackingCode2;
	}
	
	/**
	 * @return WebexXml $trackingCode2
	 */
	public function getTrackingCode2()
	{
		return $this->trackingCode2;
	}
	
	/**
	 * @param WebexXml $trackingCode3
	 */
	public function setTrackingCode3(WebexXml $trackingCode3)
	{
		$this->trackingCode3 = $trackingCode3;
	}
	
	/**
	 * @return WebexXml $trackingCode3
	 */
	public function getTrackingCode3()
	{
		return $this->trackingCode3;
	}
	
	/**
	 * @param WebexXml $trackingCode4
	 */
	public function setTrackingCode4(WebexXml $trackingCode4)
	{
		$this->trackingCode4 = $trackingCode4;
	}
	
	/**
	 * @return WebexXml $trackingCode4
	 */
	public function getTrackingCode4()
	{
		return $this->trackingCode4;
	}
	
	/**
	 * @param WebexXml $trackingCode5
	 */
	public function setTrackingCode5(WebexXml $trackingCode5)
	{
		$this->trackingCode5 = $trackingCode5;
	}
	
	/**
	 * @return WebexXml $trackingCode5
	 */
	public function getTrackingCode5()
	{
		return $this->trackingCode5;
	}
	
	/**
	 * @param WebexXml $trackingCode6
	 */
	public function setTrackingCode6(WebexXml $trackingCode6)
	{
		$this->trackingCode6 = $trackingCode6;
	}
	
	/**
	 * @return WebexXml $trackingCode6
	 */
	public function getTrackingCode6()
	{
		return $this->trackingCode6;
	}
	
	/**
	 * @param WebexXml $trackingCode7
	 */
	public function setTrackingCode7(WebexXml $trackingCode7)
	{
		$this->trackingCode7 = $trackingCode7;
	}
	
	/**
	 * @return WebexXml $trackingCode7
	 */
	public function getTrackingCode7()
	{
		return $this->trackingCode7;
	}
	
	/**
	 * @param WebexXml $trackingCode8
	 */
	public function setTrackingCode8(WebexXml $trackingCode8)
	{
		$this->trackingCode8 = $trackingCode8;
	}
	
	/**
	 * @return WebexXml $trackingCode8
	 */
	public function getTrackingCode8()
	{
		return $this->trackingCode8;
	}
	
	/**
	 * @param WebexXml $trackingCode9
	 */
	public function setTrackingCode9(WebexXml $trackingCode9)
	{
		$this->trackingCode9 = $trackingCode9;
	}
	
	/**
	 * @return WebexXml $trackingCode9
	 */
	public function getTrackingCode9()
	{
		return $this->trackingCode9;
	}
	
	/**
	 * @param WebexXml $trackingCode10
	 */
	public function setTrackingCode10(WebexXml $trackingCode10)
	{
		$this->trackingCode10 = $trackingCode10;
	}
	
	/**
	 * @return WebexXml $trackingCode10
	 */
	public function getTrackingCode10()
	{
		return $this->trackingCode10;
	}
	
}
		
