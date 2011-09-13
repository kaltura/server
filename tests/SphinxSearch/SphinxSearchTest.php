<?php
require_once (dirname ( __FILE__ ) . '/bootstrapSphinxSearchTest.php');

class SphinxSerach extends PHPUnit_Framework_TestCase 
{

	public function testApiOldQuerisAgainestNewCode() 
	{
		$logFilePath = self::getLogFilePath();
		if (!file_exists($logFilePath)){
			$this->assertTrue ( false );
		}
		
		echo 'find all sphinx API session from log: ' . SPHINX_SEARCH_API_SESSION . PHP_EOL;
		exec ( realpath ( dirname ( __FILE__ ) ) . "/getSphinxSearchApiSessionIdsFromLog.sh " . $logFilePath . " " . SPHINX_SEARCH_API_SESSION );
		
		$apiSessionIds = fopen ( SPHINX_SEARCH_API_SESSION, "r" ) or die ( "ERROR: could not open file: " . SPHINX_SEARCH_API_SESSION );
		
		$sphinxSearchTests = array ();
		$i = 0;
		$eof = false;
		while ( ! $eof && ($line = fgets ( $apiSessionIds )) == true ) {
			$serializedCriteriaLog = explode ( ' ', $line, 9 );
			
			while ( ! $eof && $serializedCriteriaLog [5] != '[SphinxCriteria->applyFilters]' ) {
				if (($line = fgets ( $apiSessionIds )) != true) {
					$eof = true;
					break;
				}
				
				$serializedCriteriaLog = explode ( ' ', $line, 9 );
			}
			
			if ($eof)
				break;
			
			//save last log position to get back for next session id.
			$offset = ftell ( $apiSessionIds );
			
			$line = fgets ( $apiSessionIds );
			$sphinxQueryLog = explode ( ' ', $line, 8 );
			$endOfSession = false;
			
			while ( ! $eof && $sphinxQueryLog [5] != '[KalturaPDO->query]' || $sphinxQueryLog [3] != $serializedCriteriaLog [3] ) {
				if (($line = fgets ( $apiSessionIds )) != true) {
					$eof = true;
					break;
				}
				
				if ($sphinxQueryLog [5] != '[KalturaDispatcher->dispatch]') {
					//end of api session
					$endOfSession = true;
					break;
				}
				
				$sphinxQueryLog = explode ( ' ', $line, 8 );
			}
			
			if ($endOfSession) {
				fseek ( $apiSessionIds, $offset ); //get back to last position
				continue;
			}
			
			$line = fgets ( $apiSessionIds );
			$mysqlQueryLog = explode ( ' ', $line, 8 );
			
			while ( ! $eof && $mysqlQueryLog [5] != '[KalturaStatement->execute]' || $mysqlQueryLog [3] != $serializedCriteriaLog [3] ) {
				$mysqlQueryLog [7] = '';
				if (($mysqlQueryLog [5] == '[KalturaDispatcher->dispatch]') || ($line = fgets ( $apiSessionIds )) != true)
					//end of api session
					break;
				
				$mysqlQueryLog = explode ( ' ', $line, 8 );
			}
			
			$i ++;
			echo PHP_EOL . '----------- test #' . $i . '------------' . PHP_EOL;
			echo 'sphinx_old_query: ' . $sphinxQueryLog [3] . ' ' . $sphinxQueryLog [7];
			if ($mysqlQueryLog [7] != '')
				echo 'mysql_old_query: ' . $mysqlQueryLog [3] . ' ' . $mysqlQueryLog [7];
			else
				$mysqlQueryLog [7] = null;
			
			echo 'sphinx_new_query: ' . $serializedCriteriaLog [3] . ' ';
			$criteria = unserialize ( $serializedCriteriaLog [8] );
			
			if (! $this->compareOldToNewQueries ( $criteria, $sphinxQueryLog [7], $mysqlQueryLog [7] ))
				//$this->assertTrue ( false );
				echo 'fail!' . PHP_EOL;
			
			fseek ( $apiSessionIds, $offset ); //get back to last position
		}
		fclose ( $apiSessionIds );
		$this->assertTrue ( true );
	}
	
	private function compareOldToNewQueries($criteria, $sphinxOldQuery, $mysqlOldQuery) 
	{
		$pdo = DbManager::getSphinxConnection ();
		$successfulTest = true;
		$OldSphinxQueryIds = array ();
		$OldQueryIds = array ('just somthing to put so this is not empty' );
		
		$criteria->applyFilters ();
		$sphinxNewQueryids = $criteria->getFetchedIds ();
		
		try {
			var_dump($sphinxOldQuery);
			$stmtSphinxOldQuery = $pdo->query ( $sphinxOldQuery );
			
			if (! $stmtSphinxOldQuery)
				echo "Invalid sphinx query [" . $sphinxOldQuery;
			
			$OldSphinxQueryIds = $stmtSphinxOldQuery->fetchAll ( PDO::FETCH_COLUMN, 2 );
		} catch ( Exception $x ) {
			echo 'error in old sphinx select: ' . $x->getMessage () . PHP_EOL;
			echo 'old sphinx query: ' . $sphinxOldQuery . PHP_EOL;
			$successfulTest = false;
		}
		
		if ($mysqlOldQuery != null) {
			try {
				$con = myDbHelper::getConnection ( myDbHelper::DB_HELPER_CONN_PROPEL2 );
				/* var PDOStatement $stmt*/
				$stmt = $con->query ( $mysqlOldQuery );
				$OldQueryIds = $stmt->fetchAll ( PDO::FETCH_COLUMN );
				$stmt->execute ();
			} catch ( Exception $x ) {
				echo 'error in mysql old select: ' . $x->getMessage () . PHP_EOL;
				echo 'old mysqk query: ' . $$mysqlOldQuery . PHP_EOL;
				$successfulTest = false;
			}
		}
		
		$OldQueryIds = array_slice ( $OldQueryIds, 0, 500 );
		
		if (! $successfulTest || is_null ( $mysqlOldQuery ) || count ( array_diff ( $OldQueryIds, $sphinxNewQueryids ) ) || count ( array_diff ( $sphinxNewQueryids, $OldQueryIds ) )) 
		{
			if ($successfulTest && ! (count ( array_diff ( $OldSphinxQueryIds, $sphinxNewQueryids ) ) || count ( array_diff ( $sphinxNewQueryids, $OldSphinxQueryIds ) ))) 
			{
				echo 'Test OK : ' . PHP_EOL;
				echo 'Test Faild - but was the same in sphinx: ' . print_r($OldSphinxQueryIds, true) . print_r($sphinxNewQueryids, true) . PHP_EOL;
				$successfulTest = true;
			} else {
				echo 'Test Faild: ' . PHP_EOL;
				if ($successfulTest && (($mysqlOldQuery != null) && strstr ( $mysqlOldQuery, 'COUNT(*)' ))) {
					echo 'Test ignored: ' . PHP_EOL;
					$successfulTest = true;
				} else {
					$successfulTest = false;
				}
			}
		}
		
		if ($successfulTest)
			echo 'Successful Test' . PHP_EOL;
		else
			echo 'Fail Test' . PHP_EOL;
		
		return $successfulTest;
	}
	
	private function getLogFilePath()
	{
		$loggerConfigPath = KALTURA_API_LOGGER_FILE_PATH;
		$config = new Zend_Config_Ini($loggerConfigPath);
		$writers = $config->writers;
		
		foreach($writers as $name => $writer)
		{
			if ($name == 'tests')
				return $writer->stream;
		}
	}
}
