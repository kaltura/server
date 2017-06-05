<?php
/**
 * @package plugins.schedule_task
 * @subpackage Admin
 */
class Form_MediaRepurposingLogs extends ConfigureForm
{
	protected $partnerId;
	protected $mediaRepurposingId;
	protected $startDate;
	protected $endDate;
	
	const DEFAULT_RUNS = 20;


	public function __construct($partnerId, $mrId, $startDate, $endDate)
	{
		$this->partnerId = $partnerId;
		$this->mediaRepurposingId = $mrId;
		$this->startDate = $startDate;
		$this->endDate = $endDate;
		parent::__construct();
	}


	public function init()
	{
		$this->setAttrib('id', 'frmMediaRepurposingLogs');

		$this->addTitle('General', 'generalTitle');
		$this->addText('Publisher ID:', 'logsPartnerId');
		$this->addText('MR profile ID:', 'logsMrId');
		$this->addText('Total Number of Runs:', 'logsRunNum');
		$this->addText('Total of Identify Entries:', 'logsEntryNum');

	}

	private function addText($label, $tag) {
		$this->addTextElement($label, $tag, array('required'=> true, 'readonly'=> true));
	}



	public function populateLogData($runsLogs)
	{
		KalturaLog::info("asdf - in populateLogData");
		$this->setDefault('logsPartnerId', $this->partnerId);
		$this->setDefault('logsMrId', $this->mediaRepurposingId);
		KalturaLog::info(print_r($runsLogs, true));

		//$this->auditTrails = $runsLogs;


	}

}