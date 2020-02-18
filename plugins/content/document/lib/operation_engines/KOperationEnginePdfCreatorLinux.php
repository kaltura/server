<?php

/**
 * @package plugins.document
 * @subpackage lib
 */
class KOperationEnginePdfCreatorLinux extends KOperationEnginePdfCreator
{
	// List of supported file types
	protected $SUPPORTED_FILE_TYPES = array(
		'Microsoft Word',
		'Microsoft PowerPoint',
		'Microsoft Excel',
		'OpenDocument Text',
		'PDF document',
		'Rich Text Format data',
	);

	protected function getCmdLine()
	{
        if(isset($this->configFilePath))
		{
			$xml = file_get_contents($this->configFilePath);
			$xml = str_replace(
				array(KDLCmdlinePlaceholders::OutDir,KDLCmdlinePlaceholders::OutFileName),
				array($this->outDir,$this->outFilePath),
				$xml);
			file_put_contents($this->configFilePath, $xml);
        }

		$outDir = dirname($this->outFilePath);
		$command = "HOME=/tmp && lowriter --headless --convert-to pdf $this->inFilePath --outdir $outDir";

		return "$command >> \"{$this->logFilePath}\" 2>&1";
	}

	protected function getKillPopupsPath()
	{
		return NULL;
	}
}