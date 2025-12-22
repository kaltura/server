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
	$report = removeExternalIDFromuser($usersList, $dryRun);
	$userUpdateReportFile = prepareAndWriteUserUpdateReport($report, $partnerId, $dryRun);
	KalturaLog::log('Done Running for partner [' . $partnerId . ']. Report file: ' . $userUpdateReportFile);
} catch (Exception $e) {
	KalturaLog::log('Error writing report: ' . $e->getMessage());
}

/**
 * Removes external IDs for the provided users and tracks the outcome per user.
 *
 * @param array $usersWithEmail
 * @param bool $dryRun
 * @return array List of user update results.
 * @throws PropelException
 */
function removeExternalIDFromuser(array $usersWithEmail, bool $dryRun): array {
	KalturaLog::log('Processing users to remove external ID.');

	$report = [];

	if (sizeof($usersWithEmail) > 0) {

		/* @var $user kuser */
		foreach($usersWithEmail as $user) {
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


function writeReportoCsv($header, $rows, $partnerId, $dryRun): string {

	$filename = ($dryRun ? 'DryRun' : 'RealRun') . "-$partnerId-external_user_update_report-" . date('Y-m-d_H-i-s') . '.csv';

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

	return writeReportoCsv($headers, $reportRows, $partnerId, $dryRun);
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

	$emailCriteria = new Criteria();
	$emailCriteria->add(kuserPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
	$emailCriteria->add(kuserPeer::STATUS, KuserStatus::DELETED, Criteria::NOT_EQUAL);
	$emailCriteria->add(kuserPeer::TYPE, KuserType::USER);
	$emailCriteria->add(kuserPeer::PUSER_ID, $puserIds, Criteria::IN);

	return kuserPeer::doSelect($emailCriteria);
}
