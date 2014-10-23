<?php
/**
 * @package plugins.document
 * @subpackage lib
 */
class KDLTranscoderPpt2Img extends KDLOperatorBase{
	
	public function __construct($id, $name=null, $sourceBlacklist=null, $targetBlacklist=null) {
    	
		parent::__construct($id,$name,$sourceBlacklist,$targetBlacklist);
    }
	
	/* (non-PHPdoc)
	 * @see KDLOperatorBase::GenerateCommandLine()
	 */
	public function GenerateCommandLine(KDLFlavor $design, KDLFlavor $target, $extra = null) {
		$cmdStr = '';
		$cmdStr .= $extra ;
		
		$cmdStr .= ' -inputFile=' . KDLCmdlinePlaceholders::InFileName;
		$cmdStr .= ' -imagesfolder=' . KDLCmdlinePlaceholders::OutFileName;
		$cmdStr .= ' -xmlFile=' .  KDLCmdlinePlaceholders::OutFileName . DIRECTORY_SEPARATOR . "metadata.xml";
		$cmdStr .= ' -slideWidth=' . $target->_image->_sizeWidth;
		return $cmdStr;
	}
}
