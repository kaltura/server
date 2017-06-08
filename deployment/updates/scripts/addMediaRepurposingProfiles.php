<?php

require_once(__DIR__ . '/../../bootstrap.php');
$firstArg = count($argv) > 1 ? $argv[1] : false;
if ($firstArg == '--help')
	return help();

if ($firstArg == 'delete')
{
	if (count($argv) > 2) {
		$ids = explode(',', $argv[2]);
		foreach ($ids as $id) {
			$profile = ScheduledTaskProfilePeer::retrieveByPK($id);
			deleteScheduleTaskProfile($profile);
		}
	} else {
		$templateProfiles = getAllTemplate();
		printInGreen('No delete id had been given. Those are the the template profiles Ids on the admin partner:' . PHP_EOL);
		echo implode(',',array_map(function($profile){return $profile->getId();},$templateProfiles)) . PHP_EOL;
	}
	return;

}

$defaultFilterValue = 1167609601; // 01/01/2007

$md = MetadataProfilePeer::retrieveBySystemName('MRP', Partner::ADMIN_CONSOLE_PARTNER_ID);
if (!$md)
	$md = createMDPTemplateForMR(getXsd());
if ($firstArg == 'setNewXsd')
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


printInGreen("DONE" . PHP_EOL);
exit;



function exitIfNull($val, $error) {if (!$val) exit("\n$error\n");}
function printInGreen($str) {echo "\033[32m$str\033[0m";}

function help() {
	$adminPartnerId = Partner::ADMIN_CONSOLE_PARTNER_ID;
	echo "This script is adding template metadata profile for Media Repurposing feature for partner $adminPartnerId\n";
	echo "It also add all MR template profile for partner $adminPartnerId\n";
	echo "if you want to override template of MDP run with first args as 'setNewXsd'\n";
	echo "if you want to delete exist MR template run with first argument as delete and second argument with the IDs to delete \n";
	return 0;
}

function createMDPTemplateForMR($xsd) {
	echo "Creating new MDP template for MR in partner " . Partner::ADMIN_CONSOLE_PARTNER_ID . PHP_EOL;
	$newMDP = new MetadataProfile();
	$newMDP->setName('Media Repurposing Profiles');
	$newMDP->setSystemName('MRP');
	$newMDP->setStatus(1);
	$newMDP->setCreateMode(1);
	$newMDP->setObjectType(1);
	$newMDP->setXsdData($xsd);
	$newMDP->setPartnerId(Partner::ADMIN_CONSOLE_PARTNER_ID);
	return $newMDP;
}

function getAllTemplate()
{
	$criteria = new Criteria();
	$criteria->add(ScheduledTaskProfilePeer::PARTNER_ID, Partner::ADMIN_CONSOLE_PARTNER_ID);
	return ScheduledTaskProfilePeer::doSelect($criteria);
}

function deleteScheduleTaskProfile($profile)
{
	/* @var $profile ScheduledTaskProfile */
	echo 'Deleting schedule task profile [' . $profile->getId() . ']' . PHP_EOL;
	$profile->setStatus(ScheduledTaskProfileStatus::DELETED);
	$profile->save();
}

function createScheduleTask($name, $filter, $task)
{
	$scheduleTask = new ScheduledTaskProfile();
	$scheduleTask->setName($name);
	$scheduleTask->setPartnerId(Partner::ADMIN_CONSOLE_PARTNER_ID);
	$scheduleTask->setStatus(ScheduledTaskProfileStatus::DISABLED);
	$scheduleTask->setObjectFilterEngineType(ObjectFilterEngineType::ENTRY);
	$scheduleTask->setObjectFilterApiType('KalturaMediaEntryFilter');
	$scheduleTask->setObjectFilter($filter);
	$scheduleTask->setObjectTasks(array($task));
	$scheduleTask->setMaxTotalCountAllowed(500);
	return $scheduleTask;
}

function addTask($type, $name, $filter, $description, $isFirst = false)
{
	$task = new kObjectTask();
	$task->setType($type);
	$scheduleTask = createScheduleTask($name, $filter, $task);
	$scheduleTask->setDescription($description);
	if ($isFirst)
		$scheduleTask->setSystemName(MediaRepurposingUtils::MEDIA_REPURPOSING_SYSTEM_NAME);
	$scheduleTask->save();
	return $scheduleTask->getId();
}

function addMailTask($name, $filter, $description, $isFirst = true)
{
	$task = new kObjectTask();
	$task->setType(ObjectTaskType::MAIL_NOTIFICATION);
	$task->setDataValue('message', "You have been identified as the owner of Media which is approaching its media retention deadline. 
		Unless you take action in the next [notification interval] days, this entry will be [Action].  ");
	$scheduleTask = createScheduleTask($name, $filter, $task);
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

function buildSubTaskName($name, $index)
{
	return "MR_$name" . "_$index";
}

function addDeleteAfterXTemplate()
{
	echo "Add Template of MR for Delete with notification\n";
	$name = 'Delete';
	$filter = getFilter(array('_lte_created_at'));

	//create the sub-task
	$timeToNext = "30";
	$task1Id = addTask(ObjectTaskType::DELETE_ENTRY, buildSubTaskName($name, "1"), $filter, $timeToNext );

	$description = $task1Id . "[$timeToNext]";
	addMailTask($name, $filter, $description);
}

function addDeleteAfterXWithExportTemplate()
{
	echo "Add Template of MR for Delete after Export with notification\n";
	$name = 'Export and Delete';
	$filter = getFilter(array('_lte_created_at'));

	//create the sub-task
	$timeToNext2 = "7";
	$task2Id = addTask(ObjectTaskType::DELETE_ENTRY, buildSubTaskName($name, "2"), $filter, $timeToNext2 );
	$timeToNext1 = "30";
	$task1Id = addTask(ObjectTaskType::STORAGE_EXPORT, buildSubTaskName($name, "1"), $filter, $timeToNext1 );

	$description = $task1Id . "[$timeToNext1]," . $task2Id . "[$timeToNext2]";
	addMailTask($name, $filter, $description);
}

function addArchiveAndDeleteTemplate()
{
	echo "Add Template of MR for Archive and Delete with notification\n";
	$name = 'Archive and Delete';
	$filter = getFilter(array('_lte_created_at'));

	//create the sub-task
	$timeToNext3 = "7";
	$task3Id = addTask(ObjectTaskType::DELETE_ENTRY, buildSubTaskName($name, "3"), $filter, $timeToNext3 );
	$timeToNext2 = "0";
	$task2Id = addTask(ObjectTaskType::MODIFY_ENTRY, buildSubTaskName($name, "2"), $filter, $timeToNext2 );
	$timeToNext1 = "30";
	$task1Id = addTask(ObjectTaskType::MODIFY_CATEGORIES, buildSubTaskName($name, "1"), $filter, $timeToNext1 );

	$description = $task1Id . "[$timeToNext1]," . $task2Id . "[$timeToNext2]," . $task3Id . "[$timeToNext3]";
	addMailTask($name, $filter, $description);}

function addArchiveExportAndDeleteTemplate()
{
	echo "Add Template of MR for Archive, Export and Delete with notification\n";
	$name = 'Archive, Export and Delete';
	$filter = getFilter(array('_lte_created_at'));

	//create the sub-task
	$timeToNext4 = "7";
	$task4Id = addTask(ObjectTaskType::DELETE_ENTRY, buildSubTaskName($name, "3"), $filter, $timeToNext4 );
	$timeToNext3 = "7";
	$task3Id = addTask(ObjectTaskType::STORAGE_EXPORT, buildSubTaskName($name, "2"), $filter, $timeToNext3 );
	$timeToNext2 = "0";
	$task2Id = addTask(ObjectTaskType::MODIFY_ENTRY, buildSubTaskName($name, "2"), $filter, $timeToNext2 );
	$timeToNext1 = "30";
	$task1Id = addTask(ObjectTaskType::MODIFY_CATEGORIES, buildSubTaskName($name, "1"), $filter, $timeToNext1 );

	$description = $task1Id . "[$timeToNext1]," . $task2Id . "[$timeToNext2]," . $task3Id . "[$timeToNext3]," . $task4Id . "[$timeToNext4]";
	addMailTask($name, $filter, $description);}

function addDeletePrivateContentTemplate()
{
	echo "Add Template of MR for Delete Private content with notification\n";
	$name = 'Delete Private Content';
	$filter = getFilter(array('_lte_created_at', '_lte_last_played_at'));
	$filter->fields['_empty_categories_ids'] = 1;

	//create the sub-task
	$timeToNext = "30";
	$task1Id = addTask(ObjectTaskType::DELETE_ENTRY, buildSubTaskName($name, "1"), $filter, $timeToNext );

	$description = $task1Id . "[$timeToNext]";
	addMailTask($name, $filter, $description);}

function addDeleteFlavorTemplate()
{
	echo "Add Template of MR for Delete Flavor with notification\n";
	$name = 'Delete Flavors';
	$filter = getFilter(array('_lte_created_at', '_lte_last_played_at'));

	//create the sub-task
	$timeToNext = "30";
	$task1Id = addTask(ObjectTaskType::DELETE_ENTRY_FLAVORS, buildSubTaskName($name, "1"), $filter, $timeToNext );

	$description = $task1Id . "[$timeToNext]";
	addMailTask($name, $filter, $description);}

function addExportTemplate()
{
	echo "Add Template of MR for Export with notification\n";
	$name = 'Export';
	$filter = getFilter(array('_lte_created_at'));

	//create the sub-task
	$timeToNext = "30";
	$task1Id = addTask(ObjectTaskType::STORAGE_EXPORT, buildSubTaskName($name, "1"), $filter, $timeToNext );

	$description = $task1Id . "[$timeToNext]";
	addMailTask($name, $filter, $description);}

function addExportAndDeleteLocalContentTemplate()
{
	echo "Add Template of MR for Export and Delete Local Content with notification\n";
	$name = 'Export and Delete Local Content';
	$filter = getFilter(array('_lte_created_at', '_lte_last_played_at'));

	//create the sub-task
	$timeToNext2 = "7";
	$task2Id = addTask(ObjectTaskType::DELETE_LOCAL_CONTENT, buildSubTaskName($name, "2"), $filter, $timeToNext2 );
	$timeToNext1 = "30";
	$task1Id = addTask(ObjectTaskType::STORAGE_EXPORT, buildSubTaskName($name, "1"), $filter, $timeToNext1 );

	$description = $task1Id . "[$timeToNext1]," . $task2Id . "[$timeToNext2]";
	addMailTask($name, $filter, $description);}


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

