<?php
class DummyForDocsClientGenerator extends ClientGeneratorFromPhp 
{
	protected $_text = "";
	protected $_enums = array();
	protected $_classes = array();
	
	public function __construct()
	{
	    ini_set("memory_limit", "256M");
	    set_time_limit(600);
	    
		parent::__construct();
	}
	
	public function load()
	{
		parent::load();
		
		foreach($this->_types as $type)
		{
			if ($type->isEnum())
			{
				$this->_enums[$type->getType()] = $type;
			}
			else if ($type->isStringEnum())
			{
				$this->_stringEnums[$type->getType()] = $type;
			}
			else if ($type->isArray())
			{
				$this->_arrays[$type->getType()] = $type;
			}
			else 
			{
				if (strpos($type->getType(), "Filter", strlen($type->getType()) - 6))
					$this->_filters[$type->getType()] = $type;
				else
					$this->_objects[$type->getType()] = $type;
			}
		}
	}
	
	public function getEnums()
	{
		return $this->_enums;
	}
	
	public function getStringEnums()
	{
		return $this->_stringEnums;
	}
	
	public function getFilters()
	{
		return $this->_filters;
	}
	
	public function getArrays()
	{
		return $this->_arrays;
	}
	
	public function getObjects()
	{
		return $this->_objects;
	}
	
	public function generate() 
	{
	}
	
	protected function writeHeader()
	{
	}
	
	protected function writeFooter()
	{
	}
	
	protected function writeBeforeTypes()
	{
		
	}
	
	protected function writeType(KalturaTypeReflector $typeReflector)
	{
	}
	
	protected function writeAfterTypes()
	{
	}
	
	protected function writeBeforeServices()
	{
	}

	protected function writeBeforeService(KalturaServiceActionItem $serviceReflector)
	{
	}
	
	protected function writeServiceAction($serviceId, $serviceName, $action, $actionParams, $outputTypeReflector)
	{
	}
	
	protected function writeAfterService(KalturaServiceActionItem $serviceReflector)
	{
	}
	
	protected function writeAfterServices()
	{
	}
	
	private function writeMainClassDeclaration()
	{
	}
	
	private function writeMainClassServiceDeclaration(KalturaServiceActionItem $serviceReflector)
	{
	}
	
	private function writeMainClassConstructorDeclaration()
	{
	}
	
	private function writeMainClassServiceInitialization(KalturaServiceActionItem $serviceReflector)
	{
	}
	
	private function writeMainClassConstructorClosure()
	{
	}
	
	private function writeMainClassClosure()
	{
	}
	
	protected function echoLine($text = "")
	{
	}
	
	protected function upperCaseFirstLetter($text)
	{
	}
	
	protected function sortTypesForPhp()
	{
	}
}
