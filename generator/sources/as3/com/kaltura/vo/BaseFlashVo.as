package com.kaltura.vo
{
	public dynamic class BaseFlashVo
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