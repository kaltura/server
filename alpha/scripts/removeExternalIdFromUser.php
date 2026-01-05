<?php

require_once(__DIR__ . '/bootstrap.php');

// parse the command line
$script = basename($argv[0]);
$help = <<<HELP
Usage:
  php $script --partner-id=<partnerId> --user-list-csv=<path> [--real-run]... [--help]

Required options:
  --partner-id Partner ID to analyze.
  --user-list-csv=<path> CSV file containing a 'puserId' column with the users to process.

Runtime options:
  --real-run Persist changes (default is dry run).

Utility options:
  --help Display this help message.

Examples:
  php $script --partner-id=12345 --user-list-csv=users.csv
  php $script --partner-id=12345 --user-list-csv=users.csv --real-run

HELP;

$longOptions = [
	'partner-id:',
	'real-run',
	'user-list-csv:',
	'help',
];

$options = getopt('', $longOptions, $optind);

$nonOptionArgs = array_slice($argv, $optind);
$shouldShowHelp = isset($options['help']);
$partnerIdOption = $options['partner-id'] ?? null;

if ($shouldShowHelp || $partnerIdOption === null) {
	fwrite(STDERR, $help);
	exit($shouldShowHelp ? 0 : 1);
}

if (!empty($nonOptionArgs)) {
	fwrite(STDERR, 'Unexpected arguments provided: ' . implode(' ', $nonOptionArgs) . PHP_EOL . PHP_EOL);
	fwrite(STDERR, $help);
	exit(1);
}

$partnerId = $partnerIdOption;
$dryRun = !isset($options['real-run']);
$userListCsv = $options['user-list-csv'] ?? '';

if (!$partner = PartnerPeer::retrieveByPK($partnerId)) {
	die("Please enter a valid partner Id!\n");
}

if (empty($userListCsv)) {
	die("User list CSV is required.\n");
}

if (!is_readable($userListCsv)) {
	die("User list file not found or not readable: $userListCsv\n");
}

$runModeLabel = $dryRun ? 'dryRun' : 'realRun';
KalturaLog::log('Starting to analyze users for partner [' . $partnerId . '] Run is a [' . $runModeLabel . ']. Using user list from file [' . $userListCsv . '].');

try {
	$usersList = getUsersByCsv($partnerId, $userListCsv);
	$report = removeExternalIdFromUser($usersList, $dryRun);
	$userUpdateReportFile = prepareAndWriteUserUpdateReport($report, $partnerId, $dryRun);
	KalturaLog::log('Done Running for partner [' . $partnerId . ']. Report file: ' . $userUpdateReportFile);
} catch (Exception $e) {
	KalturaLog::log('Error writing report: ' . $e->getMessage());
}

/**
 * Removes the external ID from a list of user objects. If the dryRun flag is set to true, no actual changes are made, and it simulates the removal process.
 *
 * @param array $users An array of user objects containing the external IDs to be removed.
 * @param bool $dryRun If true, simulates the removal of external IDs without persisting any changes.
 *
 * @return array A report detailing the processing of each user, including information about whether the external ID was removed and the current external ID.
 */
function removeExternalIdFromUser(array $users, bool $dryRun): array {
	KalturaLog::log('Processing users to remove external ID.');

	$report = [];

	if (sizeof($users) > 0) {

		/* @var $user kuser */
		foreach($users as $user) {
			$userId = $user->getPuserId();
			KalturaLog::log('Processing user [' . $userId . ']');
			$currentExternalId = $user->getExternalId();
			$updated = false;

			if (!$dryRun) {
				KalturaLog::log('Removing user external ID for puser|kuser [' . $user->getPuserId() . ' | ' . $user->getId() . ']');
				$user->setExternalId(null);
				$user->save();
				kEventsManager::flushEvents();
				KalturaLog::log('Removed user external ID for puser|kuser [' . $user->getPuserId() . ' | ' . $user->getId() . ']');
				$updated = true;
			} else {
				KalturaLog::log('Dry RUN - would remove external ID for puser|kuser [' . $user->getPuserId() . ' | ' . $user->getId() . ']');
			}

			$report[] = [
				'kuserId' => $user->getId(),
				'puserId' => $user->getPuserId(),
				'updated' => $updated,
				'currentExternalId' => $dryRun ? $currentExternalId : $user->getExternalId(),
			];
		}
	}


	return $report;
}


/**
 * Writes a report to a CSV file. The file includes the specified header and rows, and its name is generated dynamically based on the partner ID and whether the run is a dry run or a real run.
 *
 * @param array $header An array of header columns to include as the first row of the CSV file.
 * @param array $rows An array of rows to be written to the CSV file, with each row being an array of values.
 * @param int $partnerId The ID of the partner, used to generate the filename.
 * @param bool $dryRun If true, indicates a dry run, and the filename will reflect this as part of its name.
 *
 * @return string The name of the generated CSV file.
 *
 * @throws Exception If the file cannot be opened for writing.
 */
function writeReportToCsv(array $header, array $rows, int $partnerId, bool $dryRun): string {

	$filename = ($dryRun ? 'DryRun' : 'RealRun') . "-$partnerId-external_user_remove_report-" . date('Y-m-d_H-i-s') . '.csv';

	$fp = fopen($filename, 'a+');

	if ($fp === false) {
		throw new Exception("Cannot open file $filename for writing");
	}

	fputcsv($fp, $header);

	foreach($rows as $row) {
		fputcsv($fp, $row);
	}

	fclose($fp);
	KalturaLog::log('Report file saved to ' . __DIR__ . '/' . $filename);

	return $filename;
}

/**
 * Builds report rows for updated users.
 *
 * @param array $updatedUsers
 * @return array{rows: array<int,array>, processedUserIds: array<int,bool>}
 */
function buildRowsForUsers(array $updatedUsers): array
{
	$rows = [];
	$processedUserIds = [];

	foreach ($updatedUsers as $user) {
		$kuserId = $user['kuserId'];
		$processedUserIds[$kuserId] = true;
		$rows[] = [
			$kuserId,
			$user['puserId'],
			$user['updated'] ? 'yes' : 'no',
			$user['currentExternalId'],
		];
	}

	return ['rows' => $rows, 'processedUserIds' => $processedUserIds];
}

/**
 * Prepares and writes a user update report to a CSV file.
 *
 * @param array $updatedUsers List of update results.
 * @param int $partnerId Partner ID used when fetching metadata roles.
 * @return string The filename of the generated report.
 * @throws Exception
 */
function prepareAndWriteUserUpdateReport(array $updatedUsers, int $partnerId, $dryRun): string
{

	$headers = ['kuserId', 'puserId', 'updated', 'currentExternalId'];

	$users = buildRowsForUsers($updatedUsers);
	$reportRows = $users['rows'];

	return writeReportToCsv($headers, $reportRows, $partnerId, $dryRun);
}

/**
 * Retrieves users based on partner ID and a CSV file containing user IDs.
 *
 * @param int $partnerId
 * @param string $userListCsv Path to the CSV file containing a list of user IDs.
 * @return array List of users scoped to the provided CSV.
 * @throws Exception
 */
function getUsersByCsv(int $partnerId, string $userListCsv = ''): array {

	$puserIds = parsePuserIdsFromCsv($userListCsv);
	$userListChunk = array_chunk($puserIds, 100);
	$usersList = [];

	foreach ($userListChunk as $puserIdsChunk) {
		KalturaLog::log('Processing user IDs in chunk: [' . implode(',', $puserIdsChunk) . ']');
		$usersChunk = getPUsersIn($partnerId, $puserIdsChunk);

		if (!empty($usersChunk)) {
			$usersList = array_merge($usersList, $usersChunk);
		}
	}

	return $usersList;
}

/**
 * Parses a CSV file to extract unique puserId values. The method assumes the CSV may include a header row
 * with a column named "puserId". If no header is present, the first column of each row is used as the source of puserId values.
 *
 * @param string $userListCsv Path to the CSV file containing the user list.
 *
 * @return array An array of unique puserId values extracted from the CSV file.
 *
 * @throws Exception If the CSV file cannot be opened, is empty, or contains no valid puserId values.
 */
function parsePuserIdsFromCsv(string $userListCsv): array {
	$handle = fopen($userListCsv, 'r');

	if ($handle === false) {
		throw new Exception("Failed to open user list CSV: $userListCsv");
	}

	$header = fgetcsv($handle);

	if ($header === false) {
		fclose($handle);
		throw new Exception("User list CSV is empty: $userListCsv");
	}

	$trimmedHeader = array_map('trim', $header);
	$lowerHeader = array_map('strtolower', $trimmedHeader);
	$puserIdIndex = array_search('puserid', $lowerHeader, true);
	$hasHeader = $puserIdIndex !== false;

	if ($hasHeader === false) {
		// treat first row as data and reset pointer
		rewind($handle);
	}

	$puserIds = [];

	while (($row = fgetcsv($handle)) !== false) {
		if ($hasHeader) {
			if (!array_key_exists($puserIdIndex, $row)) {
				continue;
			}
			$puserId = trim((string) $row[$puserIdIndex]);
		} else {
			$puserId = trim((string) ($row[0] ?? ''));
		}

		if ($puserId !== '') {
			$puserIds[] = $puserId;
		}
	}

	fclose($handle);

	$puserIds = array_values(array_unique($puserIds));

	if (empty($puserIds)) {
		throw new Exception("No puserId values found in CSV: $userListCsv");
	}

	return $puserIds;
}

function getPUsersIn($partnerId, array $puserIds = []): array {
	if (empty($puserIds)) {
		return [];
	}

	$puserCriteria = new Criteria();
	$puserCriteria->add(kuserPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
	$puserCriteria->add(kuserPeer::STATUS, KuserStatus::DELETED, Criteria::NOT_EQUAL);
	$puserCriteria->add(kuserPeer::TYPE, KuserType::USER);
	$puserCriteria->add(kuserPeer::PUSER_ID, $puserIds, Criteria::IN);

	return kuserPeer::doSelect($puserCriteria);
}
