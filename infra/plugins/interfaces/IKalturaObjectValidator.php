<?php
/**
 * Enable the plugin to validate an operation on the object
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaObjectValidator extends IKalturaBase
{
    const OPERATION_COPY = 1;
    
    /**
     * Function validates an object and throws exception if it fails.
     * @param BaseObject $object object to validate.
     * @param int $operation operation in which context the validation is performed.
     * 
     */
    public static function validateObject (BaseObject $object, $operation);
}