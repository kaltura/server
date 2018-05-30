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
	 * @param string $csv
	 */
	public function __construct($csvPath)
	{
		$all_rows = array();
		$f = fopen($csvPath,'r');
		$header = fgetcsv($f);
		while ($row = fgetcsv($f))
		{
			$all_rows[] = array_combine($header, $row);
		}

		$this->rows = $all_rows;
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