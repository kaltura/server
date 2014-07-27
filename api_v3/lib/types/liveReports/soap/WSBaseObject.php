<?php

abstract class WSBaseObject extends SoapObject {
	
	abstract function getKalturaObject();
	
	public function toKalturaObject() {
		$kalturaObj = $this->getKalturaObject();
		self::cloneObject($this, $kalturaObj);
		return $kalturaObj;
	}
	
	public function fromKalturaObject($kalturaObj) {
		self::cloneObject($kalturaObj, $this);
	}
	
	protected static function cloneObject($objA, $objB) {
		$reflect = new ReflectionClass($objA);
		foreach($reflect->getProperties(ReflectionProperty::IS_PUBLIC) as $prop)
		{
			$name = $prop->getName();
			$value = $prop->getValue($objA);
			
			if ($value instanceof WSBaseObject) {
				$value = $value->toKalturaObject();
			} else if($value instanceof SoapArray) {
				/**
				 * @var SoapArray $value
				 */
				$arr = $value->toArray();
				$newObj = array();
				foreach($arr as $val) {
					if ($val instanceof WSBaseObject) {
						$newObj[] = $val->toKalturaObject();
					} else {
						$newObj[] = $val;
					}
				} 
				$value = $newObj;
			}
			
			$objB->$name = $value; 
		}
	}
}

