<?php 

interface LiveReportConstants { 

	// Report Format
	const DATE_FORMAT = "Ymd_His";
	const CELLS_SEPARATOR = ",";
	const ROWS_SEPARATOR = "------------------";
	
	// Parameter Constants
	const ENTRY_IDS = "ENTRY_IDS";
	const TIME_REFERENCE_PARAM = "TIME_REFERENCE_PARAM";
	const IS_LIVE = "IS_LIVE";
	
	// Time Constants
	const SECONDS_36_HOURS = 129600;
	const SECONDS_10 = 10;
	const SECONDS_60 = 60;
	
	// Other constants
	const MAX_ENTRIES = 500;
}