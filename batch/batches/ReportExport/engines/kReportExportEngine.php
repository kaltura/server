<?php
/**
 * @package Scheduler
 * @subpackage ReportExport
 */
abstract class kReportExportEngine
{
	const DEFAULT_TITLE = 'default';

	protected $reportItem;
	protected $fp;
	protected $fileName;
	protected $fromDate;
	protected $toDate;

	protected static $headersMapping = array(
		'first_name' => 'First Name',
		'last_name' => 'Last Name',
		'title' => 'Job Title',
		'company' => 'Company Name',
		'email' => 'Email Address ',
		'country' => 'Country',
		'role' => 'Role',
		'industry' => 'Industry',
		'vod_view_time' => 'VOD Minutes Viewed (Interval-Based)',
		'live_view_time' => 'Live Minutes Viewed (Interval-Based)',
		'total_view_time' => 'Total Minutes Viewed (Interval-Based)',
		'avg_completion_rate' => 'Average Completion Rate (Interval-Based)',
		'count_reaction_clicked' => 'Reaction Count',
		'count_raise_hand_clicked' => 'Hand Raise Count (Meetings)',
		'combined_live_engaged_users_play_time_ratio' => 'Live Engagement Rate',
		'count_group_chat_messages_sent' => 'Group Chat Messages Count',
		'count_poll_answered' => 'Poll Responses Count',
		'count_q_and_a_threads' => 'Q&A Threads Count',
		'count_download_attachment_clicked' => 'Attachment Downloads Count',
		'id' => 'Date',
		'count_plays' => 'Total Plays Count',
		'sum_time_viewed' => 'Total Minutes Viewed (Quartile Based)',
		'avg_time_viewed' => 'Average Minutes Viewed (Quartile Based)',
		'count_loads' => 'Player Impressions Count',
		'unique_known_users' => 'Unique Users Count',
		'avg_view_drop_off' => 'Average Drop-off Rate (Quartile Based)',
		'count_viral' => 'Entry Shares Count',
		'unique_viewers' => 'Unique Viewers Count',
		'name' => 'Name',
		'full_name' => 'Full Name',
		'unique_videos' => 'Unique Videos Viewed Count',
		'load_play_ratio' => 'Play-to-Impression Ratio',
		'total_completion_rate' => 'Total Completion Rate (Interval-Based)',
		'object_id' => 'Object ID',
		'entry_name' => 'Entry Title',
		'creator_name' => 'Entry Creator Name',
		'created_at' => 'Creation Time',
		'status' => 'Entry Status',
		'media_type' => 'Media Type',
		'duration_msecs' => 'Entry Duration (Milliseconds)',
		'entry_source' => 'Entry Source',
		'engagement_ranking' => 'Engagement Ranking (1-10)',
		'sum_view_period' => 'Total Minutes Viewed (Interval-Based)',
		'avg_view_period_time' => 'Average Minutes Viewed (Interval-Based)',
		'user_id' => 'User ID',
		'user_name' => 'User Name',
		'registered' => 'Registration Status',
		'sum_live_view_period' => 'Live Minutes Viewed (Interval-Based)',
		'avg_live_buffer_time' => 'Average Live Stream Buffering Rate',
		'live_engaged_users_play_time_ratio' => 'Live Engagement Rate',
	);

	public function __construct($reportItem, $outputPath)
	{
		$this->reportItem = $reportItem;
		$this->filename = $this->createFileName($outputPath);
		$this->fp = fopen($this->filename, 'w');
		if (!$this->fp)
		{
			throw new KOperationEngineException("Failed to open report file : " . $this->filename);
		}
	}

	public function getEmailFileName()
	{
		$emailFile = trim($this->reportItem->reportTitle);
		if ($emailFile && preg_match('/^\w[\w\s]*$/', $emailFile))
		{
			return $emailFile;
		}

		return null;
	}

	abstract public function createReport();
	abstract protected function buildCsv($res);

	protected function getDelimiter()
	{
		if ($this->reportItem->responseOptions && $this->reportItem->responseOptions->delimiter)
		{
			return $this->reportItem->responseOptions->delimiter;
		}
		return ',';
	}

	protected function getTitle()
	{
		if ($this->reportItem->reportTitle)
		{
			return $this->reportItem->reportTitle;
		}
		return self::DEFAULT_TITLE;
	}

	protected function writeReportTitle()
	{
		$this->writeRow("# ------------------------------------");
		$title = $this->getTitle();
		$this->writeRow("Report: $title");
		$this->writeFilterData();
		$this->writeRow("# ------------------------------------");
	}

	protected function writeFilterData()
	{
		$filter = $this->reportItem->filter;
		if (!$filter)
		{
			return;
		}

		$disclaimerMessage = KBatchBase::$taskConfig->params->disclaimerMessage;
		if ($disclaimerMessage)
		{
			$this->writeRow($disclaimerMessage);
		}

		if ($filter->toDay && $filter->fromDay)
		{
			$fromDate = date('Y-m-d 00:00:00', strtotime($filter->fromDay));
			$toDate = date('Y-m-d 23:59:59', strtotime($filter->toDay));
			$this->writeRow("Filtered dates: $fromDate - $toDate (GMT)");
		}
		else if ($filter->toDate && $filter->fromDate)
		{
			$fromDate = gmdate('Y-m-d H:i:s', $filter->fromDate);
			$toDate = gmdate('Y-m-d H:i:s', $filter->toDate);
			$this->writeRow("Filtered dates: $fromDate - $toDate (GMT)");
		}

		if ($filter->entryIdIn)
		{
			$entryIds = $filter->entryIdIn;
			$this->writeRow("Filtered entries: $entryIds");
		}

		if ($filter->categoriesIdsIn)
		{	
			$categoriesIds = $filter->categoriesIdsIn;
			$this->writeRow("Filtered categories: $categoriesIds");
		}

		if ($filter->userIds)
		{
			$userIds = $filter->userIds;
			$this->writeRow("Filtered users: $userIds");
		}

		if (isset($filter->playbackContext))
		{
			$playbackContextIds = $filter->playbackContext;
			$this->writeRow("Filtered categories pages: $playbackContextIds");
		}
	}

	protected function writeDelimitedRow($row)
	{
		$rowArr = explode($this->getDelimiter(), $row);
		$this->writeRow($rowArr);
	}

	protected function writeRow($row)
	{
		if (!is_array($row))
		{
			$row = array($row);
		}
		KCsvWrapper::sanitizedFputCsv($this->fp, $row);
	}

	protected function createFileName($outputPath)
	{
		$fileName = 'Report_export_' . uniqid();

		return $outputPath.DIRECTORY_SEPARATOR.$fileName;
	}

	protected function mapHeadersNames($headers)
	{
		$friendlyHeaders = [];
		foreach ($headers as $header)
		{
			if (isset(self::$headersMapping[$header]))
			{
				$friendlyHeaders[] = self::$headersMapping[$header];
			}
			else
			{
				$friendlyHeaders[] = $header; // Keep original if no mapping
			}
		}
		return $friendlyHeaders;
	}

}
