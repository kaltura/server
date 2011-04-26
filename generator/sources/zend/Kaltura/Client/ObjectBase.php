<?php
/**
 * Abstract base class for all client objects
 * 
 * @package Kaltura
 * @subpackage Client
 */
abstract class Kaltura_Client_ObjectBase
{
	abstract public function getKalturaObjectType();
	
	public function __construct(SimpleXMLElement $xml = null)
	{
		
	}
	
	protected function addIfNotNull(&$params, $paramName, $paramValue)
	{
		if ($paramValue !== null)
		{
			if($paramValue instanceof Kaltura_Client_ObjectBase)
			{
				$params[$paramName] = $paramValue->toParams();
			}
			else
			{
				$params[$paramName] = $paramValue;
			}
		}
	}
	
	public function toParams()
	{
		$params = array(
			'objectType' => $this->getKalturaObjectType()
		);
		
	    foreach($this as $prop => $val)
			$this->addIfNotNull($params, $prop, $val);
			
		return $params;
	}
}
