<?php
/**
 * @package infra
 * @subpackage Exceptions
 */
class kXsdException extends kException
{
	const CAN_NOT_CHANGE_ELEMENT_ID = "CAN_NOT_CHANGE_ELEMENT_ID,Different ids [%s != %s] in path [%s]";
	const CAN_NOT_CHANGE_ELEMENT_NAME = "CAN_NOT_CHANGE_ELEMENT_NAME,Different names [%s != %s] in path [%s]";
	const CAN_NOT_CHANGE_ELEMENT_TYPE = "CAN_NOT_CHANGE_ELEMENT_TYPE,Different types [%s != %s] in path [%s]";
	const CAN_NOT_REDUCE_ELEMENT_MAX_OCCURS = "CAN_NOT_REDUCE_ELEMENT_MAX_OCCURS,Different max occurs [%s > %s] in path [%s]";
	const CAN_NOT_INCREASE_ELEMENT_MIN_OCCURS = "CAN_NOT_INCREASE_ELEMENT_MIN_OCCURS,Different min occurs [%s < %s] in path [%s] with no default";
	const CAN_NOT_ADD_REQUIRED_ELEMENT = "CAN_NOT_ADD_REQUIRED_ELEMENT,Required element added with min occurs [%s] in path [%s] with no default";
	
	const CAN_NOT_CHANGE_ATTRIBUTE = "CAN_NOT_CHANGE_ATTRIBUTE,Attribute changed in path [%s]";
	
	const CAN_NOT_CHANGE_NODE = "CAN_NOT_CHANGE_NODE,Different nodes [%s != %s] in path [%s]";
}