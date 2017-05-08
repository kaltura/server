<?php

$override = $argv[1];
if ($override == '--help')
	return help();

if ($override)
	printInGreen("OVERRIDE mode\n");

require_once(__DIR__ . '/../../bootstrap.php');

if ($override == 'renew')
	deleteAllMRTemplate();

$defaultFilterValue = 1167609601; // 01/01/2007

$md = MetadataProfilePeer::retrieveBySystemName('MRP', '-2');
if (!$md)
	$md = createMDPTemplateForMR(getXsd());
if ($override)
	$md->setXsdData(getXsd());
$md->save();

addDeleteAfterXTemplate();
addDeleteAfterXWithExportTemplate();
addArchiveAndDeleteTemplate();
addArchiveExportAndDeleteTemplate();
addDeletePrivateContentTemplate();
addDeleteFlavorTemplate();
addExportAndDeleteLocalContentTemplate();
addExportTemplate();


printInGreen("DONE\n");
exit;




function exitIfNull($val, $error) {if (!$val) exit("\n$error\n");}
function printInGreen($str) {
	echo "\033[32m$str\033[0m";
}

function help() {
	echo "This script is adding template metadata profile for Media Repurposing feature for partner -2\n";
	echo "It also add all MR template profile for partner -2\n";
	echo "if you want to override template of MDP run with first args as 1\n";
	echo "if you want to delete all exist MR template and insert new ones run with first args as 'renew' \n";
	return 0;
}

function createMDPTemplateForMR($xsd) {
	echo "Creating new MDP template for MR in partner -2\n";
	$newMDP = new MetadataProfile();
	$newMDP->setName('MRP');
	$newMDP->setSystemName('MRP');
	$newMDP->setStatus(1);
	$newMDP->setCreateMode(1);
	$newMDP->setObjectType(1);
	$newMDP->setXsdData($xsd);
	$newMDP->setPartnerId(-2);
	return $newMDP;
}

function deleteAllMRTemplate()
{
	echo 'DELETE ALL TEMPLATE....';
	$criteria = new Criteria();
	$criteria->add(ScheduledTaskProfilePeer::PARTNER_ID, -2);
	ScheduledTaskProfilePeer::doDelete($criteria);
}

function createST($name, $filter, $task)
{
	$st = new ScheduledTaskProfile();
	$st->setName($name);
	$st->setPartnerId(-2);
	$st->setStatus(ScheduledTaskProfileStatus::DISABLED);
	$st->setObjectFilterEngineType(ObjectFilterEngineType::ENTRY);
	$st->setObjectFilterApiType('KalturaMediaEntryFilter');
	$st->setObjectFilter($filter);
	$st->setObjectTasks(array($task));
	$st->setMaxTotalCountAllowed(500);
	return $st;
}

function addTask($type, $name, $filter, $description, $isFirst = false)
{
	$task = new kObjectTask();
	$task->setType($type);
	$scheduleTask = createST($name, $filter, $task);
	$scheduleTask->setDescription($description);
	if ($isFirst)
		$scheduleTask->setSystemName(MediaRepurposingUtils::MEDIA_REPURPOSING_SYSTEM_NAME);
	$scheduleTask->save();
	return $scheduleTask->getId();
}

function getFilter($names)
{
	global $defaultFilterValue;
	$filter = new entryFilter();
	foreach ($names as $name)
		$filter->fields[$name] = $defaultFilterValue;
	return $filter;
}

function addDeleteAfterXTemplate()
{
	echo "Add Template of MR for Delete with notification\n";
	$name = 'Delete with Notification';
	$filter = getFilter(array('_lte_created_at'));

	//create the sub-task
	$timeToNext = "30";
	$task1Id = addTask(ObjectTaskType::DELETE_ENTRY, "MR-" . $name . "-1", $filter, $timeToNext );

	$description = $task1Id . "[$timeToNext]";
	addTask(ObjectTaskType::MAIL_NOTIFICATION, $name, $filter, $description, true);
}

function addDeleteAfterXWithExportTemplate()
{
	echo "Add Template of MR for Delete after Export with notification\n";
	$name = 'Export and Delete';
	$filter = getFilter(array('_lte_created_at'));

	//create the sub-task
	$timeToNext2 = "7";
	$task2Id = addTask(ObjectTaskType::DELETE_ENTRY, "MR-" . $name . "-2", $filter, $timeToNext2 );
	$timeToNext1 = "30";
	$task1Id = addTask(ObjectTaskType::STORAGE_EXPORT, "MR-" . $name . "-1", $filter, $timeToNext1 );

	$description = $task1Id . "[$timeToNext1]," . $task2Id . "[$timeToNext2]";
	addTask(ObjectTaskType::MAIL_NOTIFICATION, $name, $filter, $description, true);
}

function addArchiveAndDeleteTemplate()
{
	echo "Add Template of MR for Archive and Delete with notification\n";
	$name = 'Archive and Delete';
	$filter = getFilter(array('_lte_created_at'));

	//create the sub-task
	$timeToNext2 = "7";
	$task2Id = addTask(ObjectTaskType::DELETE_ENTRY, "MR-" . $name . "-2", $filter, $timeToNext2 );
	$timeToNext1 = "30";
	$task1Id = addTask(ObjectTaskType::MODIFY_CATEGORIES, "MR-" . $name . "-1", $filter, $timeToNext1 );

	$description = $task1Id . "[$timeToNext1]," . $task2Id . "[$timeToNext2]";
	addTask(ObjectTaskType::MAIL_NOTIFICATION, $name, $filter, $description, true);
}

function addArchiveExportAndDeleteTemplate()
{
	echo "Add Template of MR for Archive, Export and Delete with notification\n";
	$name = 'Archive, Export and Delete';
	$filter = getFilter(array('_lte_created_at'));

	//create the sub-task
	$timeToNext3 = "7";
	$task3Id = addTask(ObjectTaskType::DELETE_ENTRY, "MR-" . $name . "-3", $filter, $timeToNext3 );
	$timeToNext2 = "7";
	$task2Id = addTask(ObjectTaskType::STORAGE_EXPORT, "MR-" . $name . "-2", $filter, $timeToNext2 );
	$timeToNext1 = "30";
	$task1Id = addTask(ObjectTaskType::MODIFY_CATEGORIES, "MR-" . $name . "-1", $filter, $timeToNext1 );

	$description = $task1Id . "[$timeToNext1]," . $task2Id . "[$timeToNext2]," . $task3Id . "[$timeToNext3]";
	addTask(ObjectTaskType::MAIL_NOTIFICATION, $name, $filter, $description, true);
}

function addDeletePrivateContentTemplate()
{
	echo "Add Template of MR for Delete Private content with notification\n";
	$name = 'Delete Private Content';
	$filter = getFilter(array('_lte_created_at', '_lte_last_played_at'));
	$filter->fields['_empty_categories_ids'] = 1;

	//create the sub-task
	$timeToNext = "30";
	$task1Id = addTask(ObjectTaskType::DELETE_ENTRY, "MR-" . $name . "-1", $filter, $timeToNext );

	$description = $task1Id . "[$timeToNext]";
	addTask(ObjectTaskType::MAIL_NOTIFICATION, $name, $filter, $description, true);
}

function addDeleteFlavorTemplate()
{
	echo "Add Template of MR for Delete Flavor with notification\n";
	$name = 'Delete Flavors';
	$filter = getFilter(array('_lte_created_at', '_lte_last_played_at'));

	//create the sub-task
	$timeToNext = "30";
	$task1Id = addTask(ObjectTaskType::DELETE_ENTRY_FLAVORS, "MR-" . $name . "-1", $filter, $timeToNext );

	$description = $task1Id . "[$timeToNext]";
	addTask(ObjectTaskType::MAIL_NOTIFICATION, $name, $filter, $description, true);
}

function addExportTemplate()
{
	echo "Add Template of MR for Export with notification\n";
	$name = 'Export';
	$filter = getFilter(array('_lte_created_at'));

	//create the sub-task
	$timeToNext = "30";
	$task1Id = addTask(ObjectTaskType::STORAGE_EXPORT, "MR-" . $name . "-1", $filter, $timeToNext );

	$description = $task1Id . "[$timeToNext]";
	addTask(ObjectTaskType::MAIL_NOTIFICATION, $name, $filter, $description, true);
}

function addExportAndDeleteLocalContentTemplate()
{
	echo "Add Template of MR for Export and Delete Local Content with notification\n";
	$name = 'Export and Delete Local Content';
	$filter = getFilter(array('_lte_created_at', '_lte_last_played_at'));

	//create the sub-task
	$timeToNext2 = "7";
	$task2Id = addTask(ObjectTaskType::DELETE_LOCAL_CONTENT, "MR-" . $name . "-2", $filter, $timeToNext2 );
	$timeToNext1 = "30";
	$task1Id = addTask(ObjectTaskType::STORAGE_EXPORT, "MR-" . $name . "-1", $filter, $timeToNext1 );

	$description = $task1Id . "[$timeToNext1]," . $task2Id . "[$timeToNext2]";
	addTask(ObjectTaskType::MAIL_NOTIFICATION, $name, $filter, $description, true);
}


function getXsd() {
	return '
<xsd:schema xmlns:xsd="http://www.w3.org/2001/XMLSchema">
  <xsd:element name="metadata">
    <xsd:complexType>
      <xsd:sequence>
        <xsd:element id="md_B42C26E2-0DE5-3055-FDD7-8A484307E301" name="Status" minOccurs="0" maxOccurs="1" type="textType">
          <xsd:annotation>
            <xsd:documentation></xsd:documentation>
            <xsd:appinfo>
              <label>Status</label>
              <key>Status</key>
              <searchable>true</searchable>
              <timeControl>false</timeControl>
              <description></description>
            </xsd:appinfo>
          </xsd:annotation>
        </xsd:element>
        <xsd:element id="md_3DD74962-4AE0-8CE9-B6DD-8A48C4B86853" name="MRPsOnEntry" minOccurs="0" maxOccurs="unbounded" type="textType">
          <xsd:annotation>
            <xsd:documentation></xsd:documentation>
            <xsd:appinfo>
              <label>MRPs on Entry</label>
              <key>MRPs on Entry</key>
              <searchable>true</searchable>
              <timeControl>false</timeControl>
              <description></description>
            </xsd:appinfo>
          </xsd:annotation>
        </xsd:element>
        <xsd:element id="md_2DF4CBBA-00DE-AC82-058D-8A48F350C9D1" name="MRPData" minOccurs="0" maxOccurs="unbounded" type="textType">
          <xsd:annotation>
            <xsd:documentation></xsd:documentation>
            <xsd:appinfo>
              <label>MRP data</label>
              <key>MRP data</key>
              <searchable>true</searchable>
              <timeControl>false</timeControl>
              <description></description>
            </xsd:appinfo>
          </xsd:annotation>
        </xsd:element>
      </xsd:sequence>
    </xsd:complexType>
  </xsd:element>
  <xsd:complexType name="textType">
    <xsd:simpleContent>
      <xsd:extension base="xsd:string"/>
    </xsd:simpleContent>
  </xsd:complexType>
  <xsd:complexType name="dateType">
    <xsd:simpleContent>
      <xsd:extension base="xsd:long"/>
    </xsd:simpleContent>
  </xsd:complexType>
  <xsd:complexType name="objectType">
    <xsd:simpleContent>
      <xsd:extension base="xsd:string"/>
    </xsd:simpleContent>
  </xsd:complexType>
  <xsd:simpleType name="listType">
    <xsd:restriction base="xsd:string"/>
  </xsd:simpleType>
</xsd:schema>';
}

