<?php
/**
 * @package plugins.vendor
 * @subpackage zoom.data
 */
class kZoomPanelists implements iZoomObject
{
	const PANELISTS = 'panelists';

	public $panelists;

	public function parseData($data)
	{
		$this->panelists = array();
		if(isset($data[self::PANELISTS]))
		{
			foreach ($data[self::PANELISTS] as $panelistsData)
			{
				$panelist = new kZoomPanelist();
				$panelist->parseData($panelistsData);
				$this->panelists[] = $panelist;
			}
		}
	}

	public function getPanelistsEmails()
	{
		$emails = array();
		if($this->panelists)
		{
			foreach ($this->panelists as $panelist)
			{
				/* @var kZoomPanelist $panelist */
				$emails[] = $panelist->email;
			}
		}

		return $emails;
	}
}
