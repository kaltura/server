<?php
/**
 * @package plugins.webexNbrplayer
 * @subpackage lib
 */
class KDLOperatorWebexNbrplayer extends KDLOperatorBase {
	
    public function GenerateConfigData(KDLFlavor $design, KDLFlavor $target)
	{
		return null;
	}

    public function GenerateCommandLine(KDLFlavor $design, KDLFlavor $target, $extra=null)
    {
    	return '-Convert ' . KDLCmdlinePlaceholders::ConfigFileName;
    }
    
	/* ---------------------------
	 * CheckConstraints
	 */
	public function CheckConstraints(KDLMediaDataSet $source, KDLFlavor $target, array &$errors=null, array &$warnings=null)
	{
	    return parent::CheckConstraints($source, $target, $errors, $warnings);
	}

}

