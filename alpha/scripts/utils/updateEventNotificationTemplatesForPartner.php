<?php
if($argc < 4)
{
	echo "Arguments missing.\n\n";
	echo "Usage: php updateEventNotificationTemplates.php {filePath} {partnerId} {dryRun} {systemName - optional)\n";
	exit;
}

$filepath = $argv[1]; // file that contains key => value mapping between old code to new code
$partnerId = $argv[2];
$dryRun = $argv[3]; // dryRun = true , realRun = false
$systemName = $argv[4];

require_once(__DIR__ . '/../bootstrap.php');

/**
 * file format - each rule in a separate line: oldCodeValue => newCodeValue
 */
$file = file  ( $filepath ) or die ( 'Could not read file!' );

$replaceMap = array();

foreach ($file as $line)
{
	$arr = explode('=>',$line);
	$replaceMap[trim($arr[0])] = trim($arr[1]);
}

echo "loaded replacement mapping for contentParams and eventConditions:\n";
echo "[".print_r($replaceMap, true)."]";

function getTemplates($partnerId, $systemName = null)
{
	$criteria = new Criteria();
	EventNotificationTemplatePeer::setUseCriteriaFilter(false);
	$criteria->add ( EventNotificationTemplatePeer::PARTNER_ID, $partnerId);
	$criteria->add (EventNotificationTemplatePeer::STATUS, array(EventNotificationTemplateStatus::ACTIVE, EventNotificationTemplateStatus::DISABLED) , Criteria::IN);
	if($systemName)
		$criteria->add (EventNotificationTemplatePeer::SYSTEM_NAME, $systemName);
	return EventNotificationTemplatePeer::doSelect($criteria);
}

function updateTemplatesCode($templates, $replaceMap, $dryRun)
{
	if ($templates)
	{
		foreach ($templates as $template)
		{
			$templateId = $template->getId();
			echo "checking templateId [".$templateId."]\n";
			/**
			 * @var EventNotificationTemplate $template
			 */
			$contentParams = $template->getContentParameters();
			$templateChanged = false;
			if($contentParams)
			{
				echo "Checking template id ".$templateId." contentParams:\n";
				foreach ($contentParams as $param)
				{
					/**
					 * @var kEventNotificationParameter $param
					 */
					$paramValue = $param->getValue();
					if($paramValue && isset( $replaceMap[$paramValue->getCode()] ) )
					{
						$templateChanged = true;
						echo "old value for ". $param->getKey()." is [".print_r($paramValue,true)."]\n";
						$code = $replaceMap[$paramValue->getCode()];
						$param->getValue()->setCode($code);
						echo "new value for ". $param->getKey()." is [".print_r($param->getValue(),true)."]\n";
					}
					elseif($paramValue)
					{
						$count = 0;
						$code = $paramValue->getCode();
						$code = str_replace('$scope->getEvent()->getObject()', '$scope->getObject()', $code, $count);
						if($count)
						{
							echo "old value for ". $param->getKey()." is [".print_r($paramValue,true)."]\n";
							$param->getValue()->setCode($code);
							echo "new value for ". $param->getKey()." is [".print_r($param->getValue(),true)."]\n";
							$templateChanged =true;
						}
					}
				}
			}

			$eventConditions = $template->getEventConditions();
			if($eventConditions)
			{
				echo "Checking template id ".$templateId." eventConditions:\n";
				foreach ($eventConditions as $eventCondition)
				{
					if(!method_exists($eventCondition, 'getField'))
						continue;

					$field = $eventCondition->getField();
					$eventCondDescription = $eventCondition->getDescription();
					if($field && isset($replaceMap[$field->getCode()]))
					{
						$templateChanged = true;
						echo "old code value for ". $eventCondDescription." is [".print_r($field,true)."]\n";
						$field->setCode($replaceMap[$field->getCode()]);
						echo "new code value for ". $eventCondDescription." is [".print_r($field,true)."]\n";
					}
					elseif($field)
					{
						$count = 0;
						$code = $field->getCode();
						$code = str_replace('$scope->getEvent()->getObject()', '$scope->getObject()', $code, $count);
						if($count)
						{
							echo "old code value for ". $eventCondDescription." is [".print_r($field,true)."]\n";
							$field->setCode($code);
							echo "new code value for ". $eventCondDescription." is [".print_r($field,true)."]\n";
							$templateChanged = true;
						}
					}
				}
			}

			if($templateChanged && !$dryRun)
				$template->save();
		}
	}
}

$templates = getTemplates($partnerId, $systemName);
updateTemplatesCode($templates, $replaceMap, $dryRun);
