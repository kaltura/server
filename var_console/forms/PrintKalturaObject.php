<?php
class Kaltura_View_Helper_PrintKalturaObject extends Zend_View_Helper_Abstract
{
	private function printFriedlyName($name)
	{
		$arr = preg_split('/([A-Z])/', $name, -1, PREG_SPLIT_DELIM_CAPTURE);
		$words = array($arr[0]);
		for($i = 1; $i < count($arr); $i += 2)
		{
			$word = $arr[$i] . $arr[$i + 1];
			if(strtolower($word) == 'id')
				$word = 'ID';
				
			$words[] = $word;
		}
			
		return ucwords(join(' ', $words));
	}
	
	private function printEntity($object)
	{
		if(is_object($object))
			return $this->printObject($object);
			
		if(is_array($object))
			return $this->printArray($object);
			
		return htmlspecialchars(print_r($object, true));
	}
	
	private function printObject($object)
	{
		$class = get_class($object);
		if(!class_exists($class))
			return print_r($object, true);
			
		$oClass = new ReflectionClass($class);
		$properties = $oClass->getProperties();
		
		$ret = '<table>';
		$ret .= "<tr><td>Object Type:</td><td>$class</td></tr>";
		
		foreach($properties as $property)
		{
			if(!$property->isPublic())
				continue;
				
			$propertyName = $property->getName();
			$propertyValue = $object->$propertyName;
			$propertyName = $this->printFriedlyName($propertyName);
			
			$propertyValue = $this->printEntity($propertyValue);
			
			$ret .= "<tr><td>$propertyName:</td><td>$propertyValue</td></tr>";
		}
		$ret .= '</table>';
		
		return $ret;
	}
	
	private function printArray($array)
	{
		$ret = '<table>';
		$ret .= "<tr><td>Object Type:</td><td>Array</td></tr>";
		
		foreach($array as $propertyName => $propertyValue)
		{
			$propertyName = $this->printFriedlyName($propertyName);
			$propertyValue = $this->printEntity($propertyValue); 
				
			$ret .= "<tr><td>$propertyName:</td><td>$propertyValue</td></tr>";
		}
		$ret .= '</table>';
		
		return $ret;
	}
	
	public function printKalturaObject($object)
	{
		if(is_array($object))
			return $this->printArray($object); 
		
		return $this->printObject($object); 
	}
}