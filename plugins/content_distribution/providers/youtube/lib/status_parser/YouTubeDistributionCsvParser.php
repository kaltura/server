<?php
/**
 * @package plugins.youTubeDistribution
 * @subpackage lib
 */
class YouTubeDistributionCsvParser
{
	/**
	 * @var array
	 */
	protected $rows;

	/**
	 * YouTubeDistributionCsvParser constructor.
	 * @param $csvStr
	 */
	public function __construct($csvStr)
	{
		$all_rows = array();
		$csvArray = explode("\n", $csvStr);
		$header = explode(",", array_shift($csvArray));
		foreach ($csvArray as $row)
		{
			if (empty($row))
				continue;
			$rowArray = explode(",", $row);
			$all_rows[] = array_combine($header, $rowArray);
		}

		$this->rows = $all_rows;
		KalturaLog::debug("Parsed Csv Result:" . print_r($this->rows, true));

	}

	/**
	 * @param string $command
	 * @return string
	 */
	public function getStatusForAction($command)
	{
		if ($this->rows && isset($this->rows[0]) && isset($this->rows[0]["$command"]))
			return $this->rows[0]["$command"];
		else
			return null;
	}

	public function getErrorsSummary()
	{
		$errors = array();

		foreach ($this->rows as $row)
		{
			$message = '';
			if (isset($row['Error code']))
				$message = 'Error Code [ ' . $row['Error code'] . ']';
			if (isset($row['Severity']))
				$message .= ' - Severity [ ' . $row['Severity'] . ']';
			if (isset($row['Error message']))
				$message .= ' - Severity [ ' . $row['Error message'] . ']';

			if ($message)
				$errors[] = $message;
		}
		return $errors;
	}
	
	public function getReferenceId()
	{
		if ($this->rows && isset($this->rows[0]) && isset($this->rows[0]['Reference ID']))
			return $this->rows[0]['Custom ID'];
		else
			return null;
	}

	public function getAssetId()
	{
		if ($this->rows && isset($this->rows[0]) && isset($this->rows[0]['Asset ID']))
			return $this->rows[0]['Asset ID'];
		else
			return null;
	}

	public function getVideoId()
	{
		if ($this->rows && isset($this->rows[0]) && isset($this->rows[0]['Video ID']))
			return $this->rows[0]['Video ID'];
		else
			return null;
	}
}