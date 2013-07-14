<?php

/**
 * for future users of this script:
 * in case this script is run to change display in search values for specific partner,
 * remember that the sphinx needs to be synchronized to the DB.
 * after running this script (makePartnerEntriesPublic __PARTNER_ID__),
 * pleas run the updatePartnerEntries2Sphinx.php script (updatePartnerEntries2Sphinx __PARTNER_ID__),
 * in order to allow sphinx DB full synchronization with respect to __PARTNER_ID
 */

chdir(dirname(__FILE__));
require_once(dirname(__FILE__) . '/../bootstrap.php');

if (count($argv) !== 2)
{
	die('pleas provide partner id as input' . PHP_EOL . 
		'to run script: ' . basename(__FILE__) . ' X' . PHP_EOL . 
		'whereas X is partner id' . PHP_EOL);
}
$partner_id = @$argv[1];

$partner = PartnerPeer::retrieveByPK($partner_id);
if(!$partner)
{
        die('no such partner.'.PHP_EOL);
}
$partner->setAppearInSearch(mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK);
$partner->save();

$c = new Criteria();
$c->add(entryPeer::PARTNER_ID, $partner_id);
$c->add(entryPeer::DISPLAY_IN_SEARCH, mySearchUtils::DISPLAY_IN_SEARCH_PARTNER_ONLY);
$c->addOr(entryPeer::DISPLAY_IN_SEARCH, mySearchUtils::DISPLAY_IN_SEARCH_NONE);
$c->setLimit(200);

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
$entries = entryPeer::doSelect($c, $con);
$changedEntriesCounter = 0;
while(count($entries))
{
		$changedEntriesCounter += count($entries);
        foreach($entries as $entry)
        {
                echo "changed DISPLAY_IN_SEARCH for entry: ".$entry->getId()."\n";
                $entry->setDisplayInSearch(mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK);
                $entry->save();
        }
        entryPeer::clearInstancePool();
        $entries = entryPeer::doSelect($c, $con);
}

echo "Done. {$changedEntriesCounter} entries where changed";
