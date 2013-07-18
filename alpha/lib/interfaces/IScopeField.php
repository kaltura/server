<?php
/**
 * @package Core
 * @subpackage model.interfaces
 */ 
interface IScopeField
{
	protected function getFieldValue(kScope $scope = null);
	
	public function setScope(kScope $scope);
}