<?php
if ((!defined("PARTNER_ID") || PARTNER_ID == null) && !isset($_SESSION['sampleimpl_config'])) 
{
    include ('../../../alpha/config/kConf.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html lang="en">
<head>
    <title>Sample Implementation - Configuration</title>
    <link rel="stylesheet" type="text/css" media="screen" href="css/layout.css" />
    <style>
    body { background:#ffffff; color:#000000; padding:20px;}
    table { margin-top:10px; }
    table tr td { background:#dfdfdf; padding:4px; }
    table tr.even td { background:#efefef; padding:4px; }
    table tr td.whitebg { background:#fff; }
    input { width:200px; }
    </style>
</head>
<body>
<div class="wrap">
    In order to try the sample implementation you have to define the following values config.php (<b><?php echo realpath("config.php") ?><b>):<br />
    <table>
        <tr>
            <td>PARTNER_ID</td><td>The id of the partner which all created media would belong to.</td>
        </tr>
        <tr class="even">
            <td>SECRET</td><td>The partner SECRET to create a session. </td>
        </tr>
        <tr>
            <td>ADMIN_SECRET</td><td>The partner ADMIN SECRET to create an admin session.</td>
        </tr>
        <tr class="even">
            <td>SERVER_URL</td><td>This is the value of www_host from {KALTURA_CE_PATH}/kaltura/alpha/config/kConf.php.</td>
        </tr>
        <tr>
            <td class="whitebg" colspan="2">* You should have recieved the SECRETS and PARTNER ID at the end of KalturaCE installation</td>
        </tr>
    </table>
    <br /><br />
    Alternatively, you can fill the following fields, and those settings will be saved in SESSION variables.<br />
    * Note that using the SESSION configuration, the sample implementation will work until you close your browser.<br /><br />
    <form action="set_session_config.php" method="POST">
      <label for="partner_id">Partner ID:<br />
      <input type="text" name="partner_id" value="" /></label><br />
      <label for="secret">Secret:<br />
      <input type="text" name="secret" value="" /></label><br />
      <label for="admin_secret">Admin Secret:<br />
      <input type="text" name="admin_secret" value="" /></label><br />
      <label for="server_url">Service URL:<br />
      <input type="text" name="server_url" value="<?php echo kConf::get('www_host'); ?>" /></label><br /><br />
      <input type="submit" name="save" value="Save" />
    </form>
</div>
</body>
</html>
<?php
    die('');
}
?>