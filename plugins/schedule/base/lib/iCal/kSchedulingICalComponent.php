<?php

abstract class kSchedulingICalComponent
{
	/**
	 * @var array
	 */
	protected $fields = array();
	
	/**
	 * @var array
	 */
	protected $components = array();
	
	/**
	 * @var kSchedulingICalComponent
	 */
	protected $parent = null;
	
	/**
	 * @var KalturaScheduleEventType
	 */
	protected $eventsType = null;
	
	/**
	 * @var boolean
	 */
	protected static $writeToStdout = false;

	abstract protected function getType();

	public function __construct($data = null)
	{
		if($data)
		{
			$lines = explode($this->getLineDelimiter(), $data);
			$this->parseLines($lines);
		}
	}
	
	public static function setWriteToStdout($write)
	{
		self::$writeToStdout = $write;
	}

	protected function getLineDelimiter()
	{
		 return "\r\n";
	}

	protected function getFieldDelimiter()
	{
		 return ':';
	}
	
	public function parseLines(array &$lines)
	{
		do
		{
			$line = array_shift($lines);
			
			if(!trim($line))
				continue;
			
			list($field, $value) = explode($this->getFieldDelimiter(), $line, 2);
			$field = strtoupper($field);
			$value = trim($value);
			
			if(strtoupper($value) === $this->getType())
			{
				if($field === 'BEGIN')
					continue;

				if($field === 'END')
					break;
			}

			if($field === 'BEGIN')
			{
				$component = kSchedulingICal::parseComponent(strtoupper($value), $lines);
				$this->addComponent($component);
				continue;
			}
				
			$setter = 'set' . str_replace('-', '', preg_replace('/^x-/', '', $field));
			if(method_exists($this, $setter))
			{
				$this->$setter($value);
			}
			
			$this->setField($field, $value);
			
		} while(count($lines));
	}
	
	public function getRaw()
	{
		$lines = array();
		$lines[] = 'BEGIN:' . $this->getType();
		foreach($this->fields as $field => $value)
			$lines[] = "{$field}:{$value}";
		$lines[] = 'END:' . $this->getType();
		
		return implode("\r\n", $lines);
	}
	
	public function setParentComponent(kSchedulingICalComponent $parent)
	{
		$this->parent = $parent;
	}
	
	public function addComponent(kSchedulingICalComponent $component)
	{
		$component->setParentComponent($this);
		$this->components[] = $component;
	}
	
	public function getComponents()
	{
		return $this->components;
	}
	
	public function setField($field, $value)
	{
		$this->fields[strtoupper($field)] = $value;
	}
	
	public function getField($field)
	{
		if(isset($this->fields[strtoupper($field)]))
			return $this->fields[strtoupper($field)];
		
		return null;
	}
	
	public function addFields($fields, $prefix = null)
	{
		foreach($fields as $field => $value)
		{
			if($prefix)
				$field = "{$prefix}-{$field}";
			
			$this->setField($field, $value);
		}
	}
	
	public function toObject()
	{
		return null;
	}
	
	/**
	 * @param KalturaScheduleEventType $type
	 */
	protected function setKalturaType($type)
	{
		$this->eventsType = $type;
	}
	
	/**
	 * @return KalturaScheduleEventType
	 */
	protected function getKalturaType()
	{
		if($this->eventsType)
			return $this->eventsType;

		if($this->parent)
			return $this->parent->getKalturaType();
		
		return null;
	}
	
	public function writeField($field, $value)
	{
		$str = $field . $this->getFieldDelimiter() . $value . $this->getLineDelimiter();
		if(self::$writeToStdout)
			echo $str;
		
		return $str;
	}
	
	protected function writeBody()
	{
		$ret = '';
		foreach($this->fields as $field => $value)
			$ret .= $this->writeField($field, $value);
		
		return $ret;
	}
	
	public function begin()
	{	
		return $this->writeField('BEGIN', $this->getType());
	}
	
	public function end()
	{
		return $this->writeField('END', $this->getType());
	}
	
	public function write()
	{
		$ret = '';
		
		$ret .= $this->begin();
		$ret .= $this->writeBody();
		$ret .= $this->end();
		
		return $ret;
	}
}
