<?php


/**
 * @package Core
 * @subpackage model
 */
class Delivery extends BaseDelivery {

	public function __construct()
	{
		parent::__construct();
	}
	
	public function cloneToNew ( $newObject )
	{
		// TODO @_!! Asd T about fillObjectFromObject Usage
		$newObject->setCopiedFrom($this);
	
		$all_fields = DeliveryPeer::getFieldNames ();
		$ignore_list = array ( "Id" , "ParentId");
		
		// clone from current
		baseObjectUtils::fillObjectFromObject( $all_fields ,
				$this ,
				$newObject ,
				baseObjectUtils::CLONE_POLICY_PREFER_NEW , $ignore_list , BasePeer::TYPE_PHPNAME );
	
		$newObject->save(null, true);
		return $newObject;
	}
	
	
	// -------------------------------------
	// -- Override base methods ------------
	// -------------------------------------
	
	/**
	 * @return kUrlRecognizer
	 */
	public function getRecognizer()
	{
		$serializedObject = parent::getRecognizer();
		try {
			$object = unserialize($serializedObject);
		}
		catch (Exception $e) {
			KalturaLog::err('Error unserializing recognizer for delivery id ['.$this->getId().']');
			$object = null;
		}
		if ($object instanceof kUrlRecognizer) {
			return $object;
		}
		return null;
	}
	
	/**
	 * @param kUrlRecognizer $newObject
	 */
	public function setRecognizer($newObject)
	{
		if ($newObject instanceof kUrlRecognizer)
		{
			$serializedObject = serialize($newObject);
			parent::setRecognizer($serializedObject);
		}
		else
		{
			KalturaLog::err('Given input is not an instance of kUrlRecognizer - ignoring');
		}
	}	
	
	/**
	 * @return kUrlRecognizer
	 */
	public function getTokenizer()
	{
		$serializedObject = parent::getTokenizer();
		
		try {
			$object = unserialize($serializedObject);
		}
		catch (Exception $e) {
			KalturaLog::err('Error unserializing tokenizer for delivery id ['.$this->getId().']');
			$object = null;
		}
		
		if ($object instanceof kUrlTokenizer) {
			return $object;
		}
		return null;
	}
	
	/**
	 * @param kUrlTokenizer $newObject
	 */
	public function setTokenizer($newObject)
	{
		if ($newObject instanceof kUrlTokenizer)
		{
			$serializedObject = serialize($newObject);
			parent::setTokenizer($serializedObject);
		}
		else
		{
			KalturaLog::err('Given input is not an instance of kUrlTokenizer - ignoring');
		}
	}
} 
