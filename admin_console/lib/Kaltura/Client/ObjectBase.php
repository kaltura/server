<?php
/**
 * Abstract base class for all client objects
 * 
 * @package Admin
 * @subpackage Client
 */
abstract class Kaltura_Client_ObjectBase
{
	abstract public function getKalturaObjectType();
	
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
		$params = array();
		$params["objectType"] = get_class($this);
	    foreach($this as $prop => $val)
		{
			$this->addIfNotNull($params, $prop, $val);
		}
		return $params;
	}
}
