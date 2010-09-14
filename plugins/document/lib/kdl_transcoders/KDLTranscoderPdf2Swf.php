<?php
 
class KDLTranscoderPdf2Swf extends KDLOperatorBase
{
    
	
	public function __construct($id, $name=null, $sourceBlacklist=null, $targetBlacklist=null) {
    	
		parent::__construct($id,$name,$sourceBlacklist,$targetBlacklist);
    }
    	
	
    public function GenerateCommandLine(KDLFlavor $design, KDLFlavor $target, $extra=null)
	{
		/* PDF to SWF - will use SWFTOOLS */
		
		$cmdStr = '';
		if ($target->_swf->_flashVersion) {
			$cmdStr .= '--set flashversion='.$target->_swf->_flashVersion.' ';
		}
		if ($target->_swf->_zoom) {
			$cmdStr .= '--set zoom='.$target->_swf->_zoom.' ';
		}
		if ($target->_swf->_zlib) {
			$cmdStr .= '--zlib ';
		}
		if ($target->_swf->_jpegQuality) {
			$cmdStr .= '--jpegquality '.$target->_swf->_jpegQuality.' ';
		}
		if ($target->_swf->_sameWindow) {
			$cmdStr .= '--samewindow ';
		}
		if ($target->_swf->_insertStop) {
			$cmdStr .= '--stop ';
		}
		/* TODO: implement!
		if ($target->_swf->_preloader) {
			$cmdStr .= '--preloader ';
		}
		*/
		if ($target->_swf->_useShapes) {
			$cmdStr .= '--shapes ';
		}
		if ($target->_swf->_storeFonts) {
			$cmdStr .= '--fonts ';
		}
		if ($target->_swf->_flatten) {
			$cmdStr .= '--flatten ';
		}
		
		$cmdStr .= KDLCmdlinePlaceholders::InFileName.
		           ' -o '.KDLCmdlinePlaceholders::OutFileName;
		
		return trim($cmdStr);
	}
		

}

