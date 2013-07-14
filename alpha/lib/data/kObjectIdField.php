<?php
/**
 * Calculate value of an object ID based on a specific context.
 * 
 * @package Core
 * @subpackage model.data
 *
 */
class kObjectIdField extends kStringField
{
	/* (non-PHPdoc)
	 * @see kStringField::getFieldValue()
	 */
	protected function getFieldValue(kScope $scope = null)
	{
		if(!$scope)
		{
			KalturaLog::info('No scope specified');
			return null;
		}
		
		if (!($scope instanceof kEventScope))
		{
			KalturaLog::info('Scope must be of type kEventScope, [' . get_class($scope) . '] given');
			return;
		}
		
		if (!($scope->getEvent()))
		{
			KalturaLog::info('$scope->getEvent() must return a value');
			return;
		}
		
		if ($scope->getEvent() && !($scope->getEvent() instanceof  IKalturaObjectRelatedEvent))
		{
			KalturaLog::info('Scope event must realize interface IKalturaObjectRelatedEvent');
			return;
		}
		
		if ($scope->getEvent() && !($scope->getEvent()->getObject()))
		{
			KalturaLog::info('Object not found on scope event');
			return;
		}
		
		if (!method_exists($scope->getEvent()->getObject(), 'getId'))
		{
			KalturaLog::info('Getter method for object id not found');
			return;
		}
		
		return $scope->getEvent()->getObject()->getId();
	}

	
}