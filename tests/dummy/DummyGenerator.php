<?php
class DummyGenerator extends ClientGeneratorFromPhp 
{
	protected $_text = "";
	
	public function DummyForDocsClientGenerator()
	{
		parent::ClientGeneratorFromPhp();
		
	}
	public function load()
	{
		parent::load();
	}
	
	public function getServices()
	{
		return $this->_services;
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
	
	protected function writeServiceAction($serviceId, $action, $actionParams, $outputTypeReflector)
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