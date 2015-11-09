<?php 

/**
 * This engine is responsible to create a table grouped by entry ID from multiple sub engines 
 */
class LiveReportEntryBasedChunkerEngine extends LiveReportEngine {
	
	const ENTRY_CHUNK_SIZE = 100;

	/** Array of LiveReportEntryQueryEngine working as subengines */
	protected $subEngines;

	public function __construct(array $subEngines) {
		$this->subEngines = $subEngines;
	}

	public function run($fp, array $args = array()) {
		$this->checkParams($args, array( LiveReportConstants::ENTRY_IDS, LiveReportConstants::TIME_REFERENCE_PARAM));
		$entryIdStr = $args[LiveReportConstants::ENTRY_IDS];
		$entryIds = explode(",", $entryIdStr);
		$entryChunks = array_chunk($entryIds, self::ENTRY_CHUNK_SIZE);

		$headers = $this->printHeaders($fp, "Entry Id");
		$values = array();
		// Execute all engines
		foreach ( $entryChunks as $entryChunk ) {
			foreach ( $this->subEngines as $engine ) {
				$columnName = $engine->getTitle ();
				$engineArgs = $args;
				$engineArgs [LiveReportConstants::ENTRY_IDS] = implode ( ",", $entryChunk );
				$engineArgs ["TIME_REFERENCE_PARAM"] = $args [LiveReportConstants::TIME_REFERENCE_PARAM];
				
				if (empty ( $entryIdStr ))
					$values [$columnName] = array ();
				else
					$values [$columnName] = $engine->run ( $fp, $engineArgs );
			}
			
			// Join results
			$this->mergeColumnsToTable ( $fp, $values );
			$values = array();
		}
		
	}
	
	protected function printHeaders($fp, $groupBy) {
		// retrieve all keys
		$headers = array();
		foreach ( $this->subEngines as $engine ) {
			$headers[] = $engine->getTitle ();
		}
		
		$header = $groupBy . LiveReportConstants::CELLS_SEPARATOR . implode(LiveReportConstants::CELLS_SEPARATOR, $headers);
		fwrite($fp, $header . "\n");
		return $headers;
	}

	protected function mergeColumnsToTable($fp, array $miniTables) {

		$keys = array();
		foreach ($miniTables as $miniTable) {
			$keys = array_unique(array_merge($keys, array_keys($miniTable)));
		}
			
		foreach($keys as $key) {
			$values = array();
			$values[] = $key;
			foreach ($miniTables as $miniTable) {
				$values[] = isset($miniTable[$key]) ? $miniTable[$key] : 0;
			}
			fwrite($fp, implode(LiveReportConstants::CELLS_SEPARATOR, $values) . "\n");
		}
	}
}
