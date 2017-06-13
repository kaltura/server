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


	public function populateDryRun($dryRunId, $objects)
	{
		$cnt = count($objects);
		$this->addComment('dryRunTitle', "Dry Run Result for $dryRunId has [$cnt] objects");
		$this->addElementByStrType('textarea', 'Entries in Dry Run Reports:', 'dryRunLog', array());
		$displayList = $this->buildEntriesListForDisplay($objects);
		$this->setDefault('dryRunLog', print_r($displayList,true));
	}

	private function buildEntriesListForDisplay($entriesList)
	{
		$displayList = array();
		$displayFields = array("name", "userId", "view", "createdAt", "lastPlayedAt");
		foreach($entriesList as $entry)
		{
			$displayEntry = array();
			foreach($displayFields as $field)
				$displayEntry[$field] = $entry->$field;
			$displayList[$entry->id] = $displayEntry;
		}
		return $displayList;
	}

	
}