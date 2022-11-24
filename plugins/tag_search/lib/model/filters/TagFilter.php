<?php
/**
 * @package Core
 * @subpackage model.filters
 */ 
class TagFilter extends baseObjectFilter
{
	/* (non-PHPdoc)
     * @see baseObjectFilter::getFieldNameFromPeer()
     */
    protected function getFieldNameFromPeer ($field_name)
    {
        $res = TagPeer::translateFieldName( $field_name , $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		return $res;
        
    }

	/* (non-PHPdoc)
     * @see baseObjectFilter::getIdFromPeer()
     */
    protected function getIdFromPeer ()
    {
        return TagPeer::ID;
        
    }

	/* (non-PHPdoc)
     * @see myBaseObject::init()
     */
    protected function init ()
    {
        $this->fields = kArray::makeAssociativeDefaultValue ( array (
			"_eq_object_type",
			"_eq_tag",
			"_likex_tag",
            "_eq_instance_count",
        	"_in_instance_count",
			) , NULL );

		$this->allowed_order_fields = array ("instance_count");
        
    }

    
}