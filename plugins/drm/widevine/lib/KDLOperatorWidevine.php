<?php
/**
 * @package plugins.widevine
 * @subpackage lib
 * 
 */
class KDLOperatorWidevine extends KDLOperatorBase 
{
    public function __construct($id, $name=null, $sourceBlacklist=null, $targetBlacklist=null) 
    {
    	parent::__construct($id,$name,$sourceBlacklist,$targetBlacklist);
    }

	/* ---------------------------
	 * GenerateCommandLine
	 */
    public function GenerateCommandLine(KDLFlavor $design, KDLFlavor $target, $extra=null)
	{
		return '';
	}
	
	public function CheckConstraints(KDLMediaDataSet $source, KDLFlavor $target, array &$errors=null, array &$warnings=null)
	{
	    return false;
	}
	
}
	