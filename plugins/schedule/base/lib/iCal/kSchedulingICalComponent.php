<?php

abstract class kSchedulingICalComponent
{
	/**
	 * @var array
	 */
	private $fields = array();
	
	/**
	 * @var array
	 */
	private $components = array();
	
	/**
	 * @var kSchedulingICalComponent
	 */
	private $parent = null;
	
	/**
	 * @var KalturaScheduleEventType
	 */
	private $eventsType = null;
	
	/**
	 * @var resource
	 */
	private $stdout = null;

	abstract protected function getType();

	public function __construct($data = null)
	{
		if($data)
		{
			$lines = explode($this->getLineDelimiter(), $data);
			$this->parseLines($lines);
		}
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
	
	public function setStdOut($stdout)
	{
		$this->stdout = $stdout;
	}
	
	protected function writeField($field, $value)
	{
		fwrite($this->stdout, $field . $this->getFieldDelimiter() . $value . $this->getLineDelimiter());
	}
	
	protected function writeBody()
	{
		foreach($this->fields as $field => $value)
			$this->writeField($field, $value);
	}
	
	public function begin()
	{
		if(!$this->stdout)
		{
			$this->stdout = fopen('php://stdout', 'w');
		}
		
		$this->writeField('BEGIN', $this->getType());
	}
	
	public function end()
	{
		$this->writeField('END', $this->getType());
	}
	
	public function write()
	{
		$this->begin();
		$this->writeBody();
		$this->end();
	}
}
