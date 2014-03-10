<?php
/**
 * @package plugins.smoothProtect
 * @subpackage lib
 * 
 * SmoothProtect plugin 
 */
class KDLOperatorSmoothProtect extends KDLOperatorBase {
    public function __construct($id="smoothProtect.SmoothProtect", $name=null, $sourceBlacklist=null, $targetBlacklist=null) {
    	parent::__construct($id,$name,$sourceBlacklist,$targetBlacklist);
    }

	/* ---------------------------
	 * GenerateCommandLine
	 */
    public function GenerateCommandLine(KDLFlavor $design, KDLFlavor $target, $extra=null)
	{
		$cmdStr = " ".SmoothProtectPlugin::PARAMS_STUB;
		return $cmdStr;
	}
	
	/* ---------------------------
	 * CheckConstraints
	 */
	public function CheckConstraints(KDLMediaDataSet $source, KDLFlavor $target, array &$errors=null, array &$warnings=null)
	{
	    return parent::CheckConstraints($source, $target, $errors, $warnings);
	}
}
	