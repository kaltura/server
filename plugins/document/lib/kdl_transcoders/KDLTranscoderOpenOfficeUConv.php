<?php
 
//TODO: NOT COMPLETLY IMPLEMENTED - not yet used
class KDLTranscoderOpenOfficeUConv extends KDLOperatorBase
{
    	
    public function GenerateCommandLine(KDLFlavor $design, KDLFlavor $target, $extra=null)
	{
		/* Document to PDF using UCONV */
		
		$cmdStr = /*unoconv.py */'-f ' . KDLCmdlinePlaceholders::OutDir.DIRECTORY_SEPARATOR.KDLCmdlinePlaceholders::OutFileName .
					             ' ' . KDLCmdlinePlaceholders::InFileName;
		
		return $cmdStr;
	}
	

}

