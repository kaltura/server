<?php
/**
 * @package plugins.schedule_task
 * @subpackage Admin
 */
class Form_MediaRepurposingLogs extends ConfigureForm
{

	public function init()
	{
		$this->setAttrib('id', 'frmMediaRepurposingLogs');
		$this->addTitle('Logs', 'generalTitle');
	}


	public function populateDryRun($dryRunId, $cnt)
	{
		$this->addComment('dryRunTitle', "Dry Run Result for $dryRunId has [$cnt] objects");
	}



	
}