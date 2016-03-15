<?php
/**
 * @package plugins.document
 * @subpackage lib
 */
class KDLTranscoderThumbAssetsGenerator extends KDLOperatorBase{
	
	public function __construct($id, $name=null, $sourceBlacklist=null, $targetBlacklist=null) {
    	
		parent::__construct($id,$name,$sourceBlacklist,$targetBlacklist);
    }
	
	/* (non-PHPdoc)
	 * @see KDLOperatorBase::GenerateCommandLine()
	 */
	public function GenerateCommandLine(KDLFlavor $design, KDLFlavor $target, $extra = null) {
		return null;
	}
}
