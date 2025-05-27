<?php
/**
 * This script checks if partners have EventNotificationTemplates with specific
 * system names: ENTRYCATEGORY_ADDED_FIREBASE_ANDROID, ENTRYCATEGORY_ADDED_FIREBASE_IOS
 *
 *
 * Examples:
 * php migrateFirebaseToV2.php
 * php migrateFirebaseToV2.php realrun
 */

$dryRun = true;
if (in_array('realrun', $argv))
{
	$dryRun = false;
}

$countLimitEachLoop = 500;
$offset = $countLimitEachLoop;

const FIREBASE_ANDROID = 'ENTRYCATEGORY_ADDED_FIREBASE_ANDROID';
const FIREBASE_IOS = 'ENTRYCATEGORY_ADDED_FIREBASE_IOS';
const FIREBASE_ANDROID_V2 = 'ENTRYCATEGORY_ADDED_FIREBASE_ANDROID_V2';
const FIREBASE_IOS_V2 = 'ENTRYCATEGORY_ADDED_FIREBASE_IOS_V2';

$searchTemplates = array(FIREBASE_ANDROID, FIREBASE_IOS);

//------------------------------------------------------

require_once(__DIR__ . '/../bootstrap.php');

$con = myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2);
KalturaStatement::setDryRun($dryRun);

$androidTemplate = getSystemTemplate(FIREBASE_ANDROID_V2);
$iosTemplate = getSystemTemplate(FIREBASE_IOS_V2);

if (!$androidTemplate || !$iosTemplate)
{
	print("System templates not found. Exiting.\n");
	exit(1);
}

$c = new Criteria();
$c->addAscendingOrderByColumn(PartnerPeer::ID);
$c->addAnd(PartnerPeer::ID, 99, Criteria::GREATER_EQUAL);
$c->addAnd(PartnerPeer::STATUS, 1, Criteria::EQUAL);
$c->setLimit($countLimitEachLoop);

$partners = PartnerPeer::doSelect($c, $con);

while (count($partners)) 
{
    foreach ($partners as $partner) 
    {
        $partnerId = $partner->getId();
        
        $androidTemplateCriteria = new Criteria();
        $androidTemplateCriteria->add(EventNotificationTemplatePeer::PARTNER_ID, $partnerId);
        $androidTemplateCriteria->add(EventNotificationTemplatePeer::SYSTEM_NAME, $searchTemplates, Criteria::IN);
		$androidTemplateCriteria->add(EventNotificationTemplatePeer::STATUS, EventNotificationTemplateStatus::ACTIVE);
        $templates = EventNotificationTemplatePeer::doSelect($androidTemplateCriteria);
        
        foreach ($templates as $template) 
        {
			/* @var $template EventNotificationTemplate */
            if (in_array($template->getSystemName(), $searchTemplates))
			{
				print("Partner [$partnerId]: found template of {$template->getSystemName()}\n");

				if ($template->getSystemName() == FIREBASE_ANDROID)
				{
					copyTemplate($androidTemplate, $partnerId);
				}
				elseif ($template->getSystemName() == FIREBASE_IOS)
				{
					copyTemplate($iosTemplate, $partnerId);
				}

                $template->setStatus(EventNotificationTemplateStatus::DELETED);
				$template->save();
            }
        }
    }

    kMemoryManager::clearMemory();

    $c = new Criteria();
    $c->addAscendingOrderByColumn(PartnerPeer::ID);
    $c->addAnd(PartnerPeer::STATUS, 1, Criteria::EQUAL);
    $c->setLimit($countLimitEachLoop);
    $c->setOffset($offset);
    
    $partners = PartnerPeer::doSelect($c, $con);
	$offset += $countLimitEachLoop;
}

print("Done checking all partners for notification templates\n");


function getSystemTemplate($systemName)
{
	$criteria = new Criteria();
	$criteria->add(EventNotificationTemplatePeer::PARTNER_ID, 0);
	$criteria->add(EventNotificationTemplatePeer::SYSTEM_NAME, $systemName);
	return EventNotificationTemplatePeer::doSelectOne($criteria);
}

function copyTemplate($template, $partnerId)
{
	$newTemplate = $template->copy();
	$newTemplate->setPartnerId($partnerId);
	$newTemplate->save();
}
