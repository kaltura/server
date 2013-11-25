<?php

class SphinxCuePointCriteria extends SphinxCriteria
{
	public function getIndexObjectName() {
		return "CuePointIndex";
	}
	
	public function hasPeerFieldName($fieldName)
	{
		if(strpos($fieldName, '.') === false)
		{
			$fieldName = strtoupper($fieldName);
			$fieldName = "cue_point.$fieldName";
		}
		
		$cuePointFields = CuePointPeer::getFieldNames(BasePeer::TYPE_COLNAME);
		
		return in_array($fieldName, $cuePointFields);
	}
}