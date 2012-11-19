<?php
/**
 * Calculate value of an object ID based on a specific context.
 * 
 * @package Core
 * @subpackage model.data
 *
 */
class kObjectIdStringField extends kStringValue
{
	/* (non-PHPdoc)
	 * @see kIntegerField::getFieldValue()
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
		
		if ($scope->getEvent() && !($scope instanceof IKalturaObjectRelatedEvent))
		{
			KalturaLog::info('Scope event must realize interface IKalturaObjectRelatedEvent');
			return;
		}
		
		if ($scope->getEvent() && !($scope->getEvent()->getObject()))
		{
			KalturaLog::info('Object not found on scope event');
			return;
		}
			
		return $scope->getEvent()->getObjct()->getId();
	}

	
}