<?php
class DummyForDocsClientGenerator extends ClientGeneratorFromPhp 
{
	protected $_text = "";
	protected $_enums = array();
	protected $_classes = array();
	
	public function DummyForDocsClientGenerator()
	{
		parent::ClientGeneratorFromPhp();
		
	}
	public function load()
	{
		parent::load();
		
		foreach($this->_types as $type)
		{
			if ($type->isEnum())
			{
				$this->_enums[] = $type;
			}
			else if ($type->isStringEnum())
			{
				$this->_stringEnums[] = $type;
			}
			else if ($type->isArray())
			{
				$this->_arrays[] = $type;
			}
			else 
			{
				if (strpos($type->getType(), "Filter", strlen($type->getType()) - 6))
					$this->_filters[] = $type;
				else
					$this->_objects[] = $type;
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

	protected function writeBeforeService(KalturaServiceReflector $serviceReflector)
	{
	}
	
	protected function writeServiceAction($serviceId, $serviceName, $action, $actionParams, $outputTypeReflector)
	{
	}
	
	protected function writeAfterService(KalturaServiceReflector $serviceReflector)
	{
	}
	
	protected function writeAfterServices()
	{
	}
	
	private function writeMainClassDeclaration()
	{
	}
	
	private function writeMainClassServiceDeclaration(KalturaServiceReflector $serviceReflector)
	{
	}
	
	private function writeMainClassConstructorDeclaration()
	{
	}
	
	private function writeMainClassServiceInitialization(KalturaServiceReflector $serviceReflector)
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