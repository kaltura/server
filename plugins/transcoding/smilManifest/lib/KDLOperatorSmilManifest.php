<?php
/**
 * @package plugins.smilManifest
 * @subpackage lib
 * 
 */
class KDLOperatorSmilManifest extends KDLOperatorBase
{
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
	