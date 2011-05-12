<?php
try
{
	$serviceReflector = new KalturaServiceReflector($service);
}
catch(Exception $ex)
{
	die('Service "'.$service.'" not found');
}
$actions = array_keys($serviceReflector->getActions());
$serviceInfo = $serviceReflector->getServiceInfo();
?>
<h2>Kaltura API</h2>
<table id="serviceInfo">
  <tr>
    <td class="title">Service Name</td>
    <td class="odd"><?php echo  $serviceInfo->serviceName; ?></td>
  </tr>
<!--  <tr>
    <th>Package</th>
    <td><?php echo  $serviceInfo->package; ?></td>
  </tr>-->
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
      foreach($actions as $action)
      {
        $actionInfo = $serviceReflector->getActionInfo($action);
        echo '<tr><td><a href="?service='.$service.'&action='.$action.'">'.$actionInfo->action.'</td><td>'.nl2br($actionInfo->description).'</td></tr>';
      }
      ?>
      </table>
    </td>
  </tr>
</table>
  