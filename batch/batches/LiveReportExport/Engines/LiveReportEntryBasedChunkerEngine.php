<?php 

/**
 * This engine is responsible to create a table grouped by entry ID from multiple sub engines 
 */
class LiveReportEntryBasedChunkerEngine extends LiveReportEngine {
	
	const ENTRY_CHUNK_SIZE = 10;

	/** Array of LiveReportEntryQueryEngine working as subengines */
	protected $subEngines;

	public function LiveReportEntryBasedChunkerEngine(array $subEngines) {
		$this->subEngines = $subEngines;
	}

	public function run($fp, array $args = array()) {
		$this->checkParams($args, array( LiveReportConstants::ENTRY_IDS, LiveReportConstants::TIME_REFERENCE_PARAM));
		$entryIds = explode(",", $args[LiveReportConstants::ENTRY_IDS]);
		$entryChunks = array_chunk($entryIds, self::ENTRY_CHUNK_SIZE);

		$values = array();
		// Execute all engines
		foreach ($entryChunks as $entryChunk) {
			foreach($this->subEngines as  $engine) {
				$columnName = $engine->getTitle();
				$engineArgs = $args;
				$engineArgs[LiveReportConstants::ENTRY_IDS] = implode(",", $entryChunk);
				$engineArgs["TIME_REFERENCE_PARAM"] = $args[LiveReportConstants::TIME_REFERENCE_PARAM];
				
				$values[$columnName] = $engine->run($fp, $engineArgs);
			}
		}

		// Join results
		$this->mergeColumnsToTable($fp, "ENTRY ID", $values);
	}

	protected function mergeColumnsToTable($fp, $groupBy, array $miniTables) {

		// retrieve all keys
		$keys = array();
		foreach ($miniTables as $miniTable) {
			$keys = array_unique(array_merge($keys, array_keys($miniTable)));
		}

		$header = $groupBy . LiveReportConstants::CELLS_SEPARATOR . implode(LiveReportConstants::CELLS_SEPARATOR, array_keys($miniTables));
		fwrite($fp, $header . "\n");
		
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
