<?php
/**
 * @package plugins.document
 * @subpackage lib
 */
class KDLTranscoderImageMagick extends KDLOperatorBase{
	
	public function __construct($id, $name=null, $sourceBlacklist=null, $targetBlacklist=null) {
    	
		parent::__construct($id,$name,$sourceBlacklist,$targetBlacklist);
    }
	
	/* (non-PHPdoc)
	 * @see KDLOperatorBase::GenerateCommandLine()
	 */
	public function GenerateCommandLine(KDLFlavor $design, KDLFlavor $target, $extra = null) {
		$cmdStr = '';
		if($target->_image) {
			$cmdStr.= $this->getTwoDimensionsParams('density', $target->_image->_densityWidth, $target->_image->_densityHeight);
			$cmdStr.= $this->getTwoDimensionsParams('geometry', $target->_image->_sizeWidth , $target->_image->_sizeHeight);
			$cmdStr.= $this->getSimpleParam('depth', $target->_image->_depth);
			$cmdStr.= '-colorspace RGB -limit memory 1000MB -limit map 200 ';
		}
		$cmdStr .= $extra . KDLCmdlinePlaceholders::InFileName.' '.KDLCmdlinePlaceholders::OutFileName;
		return $cmdStr;
		
	}
	
	
	private function getTwoDimensionsParams($flag,$widthParam,$heightParam) {
		$cmdStr = '';
		if (!is_null($widthParam) || !is_null($heightParam))
			$cmdStr .= '-'.$flag.' ';
		if (!is_null ($widthParam))
			$cmdStr .= $widthParam;
		if (!is_null ($heightParam))
			$cmdStr .= 'x'.$heightParam;
		return $cmdStr.' ';
	}
	
	private function getSimpleParam($flag,$param){
		$cmdStr = '';
		if(!is_null($param)){
			$cmdStr.='-'.$flag.' '.$param;
		}
		return $cmdStr.' ';
	}

}
