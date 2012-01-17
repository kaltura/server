<?php
/**
 * @package plugins.expressionEncoder
 * @subpackage lib
 */
class KDLOperatorExpressionEncoder extends KDLOperatorBase {
    public function __construct($id, $name=null, $sourceBlacklist=null, $targetBlacklist=null) {
    	parent::__construct($id,$name,$sourceBlacklist,$targetBlacklist);
    }

    public function GenerateConfigData(KDLFlavor $design, KDLFlavor $target)
	{
		$ee3 = new KDLExpressionEncoder3();
				// Remove slaches that were added to solve
				// JSON serialization issue
		$xmlStr=str_replace ('"' , '\"' ,  $ee3->GeneratePresetFile($target));
		return $xmlStr;
	}

    public function GenerateCommandLine(KDLFlavor $design, KDLFlavor $target, $extra=null)
    {
    	return KDLCmdlinePlaceholders::InFileName . ' ' . KDLCmdlinePlaceholders::ConfigFileName;
    }
    
	/* ---------------------------
	 * CheckConstraints
	 */
	public function CheckConstraints(KDLMediaDataSet $source, KDLFlavor $target, array &$errors=null, array &$warnings=null)
	{
	    return parent::CheckConstraints($source, $target, $errors, $warnings);
	}
}

