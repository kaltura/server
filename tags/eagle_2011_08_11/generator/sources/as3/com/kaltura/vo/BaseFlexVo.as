package com.kaltura.vo
{
	import mx.utils.ObjectProxy;
	
	public dynamic class BaseFlexVo extends ObjectProxy
	{
		private var _updatedFieldsOnly : Boolean = false;
		private var _insertedFields : Boolean = false;
		
		public function getUpdatedFieldsOnly() : Boolean
		{
			return _updatedFieldsOnly;
		}
		
		public function setUpdatedFieldsOnly( value : Boolean ) : void
		{
			_updatedFieldsOnly = value;
		}
		
		public function getInsertedFields() : Boolean
		{
			return _insertedFields;
		}
		
		public function setInsertedFields( value : Boolean ) : void
		{
			_insertedFields = value;
		}
	}
}