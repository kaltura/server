<?php
$serviceMap = KalturaServicesMap::getMap();
$serviceReflector = $serviceMap[strtolower($service)];

if (!$serviceReflector)
{
    die('Service "'.$service.'" not found');
}
/* @var $serviceReflector KalturaServiceActionItem */
$actions = $serviceReflector->actionMap;
$serviceInfo = $serviceReflector->serviceInfo;
?>
<h2>Kaltura API</h2>
<table id="serviceInfo">
  <tr>
    <td class="title">Service Name</td>
    <td class="odd"><?php echo  $serviceInfo->serviceName; ?></td>
  </tr>
  <?php $plugin = extractPluginNameFromPackage($serviceInfo->package);
  	if ($plugin)
  	{ ?>
  <tr>
    <td class="title">Plugin</td>
    <td class="odd"><?php echo $plugin ?></td>
  </tr>
  <?php 
  	}?>
  <tr>
    <td class="title">Description</td>
    <td><?php echo  nl2br($serviceInfo->description); ?></td>
  </tr>
  <tr>
    <td class="title">Actions</td>
    <td class="odd">
      <table cellspacing="0" class="service_actions">
        <tr>
          <th>Name</th>
          <th>Description</th>
        </tr>
      <?php
      foreach($actions as $actionId=>$actionCallback)
      {
        $actionReflector = new KalturaActionReflector($service, $actionId, $actionCallback);
        $actionInfo = $actionReflector->getActionInfo();
        echo '<tr><td><a href="?service='.$service.'&action='.$actionId.'">'.$actionReflector->getActionName().'</td><td>'.nl2br($actionInfo->description).'</td></tr>';
      }
      ?>
      </table>
    </td>
  </tr>
</table>
  