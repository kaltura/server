package com.kaltura.vo
{
	import mx.utils.ObjectProxy;
	
	public dynamic class BaseFlexVo extends ObjectProxy
	{
		private var _updatedFieldsOnly : Boolean = false;
		
		public function getUpdatedFieldsOnly() : Boolean
		{
			return _updatedFieldsOnly;
		}
		
		public function setUpdatedFieldsOnly( value : Boolean ) : void
		{
			_updatedFieldsOnly = value;
		}
	}
}