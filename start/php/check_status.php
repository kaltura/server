<?php


include_once(dirname(__FILE__).'/../../infra/general/ActKeyUtils.class.php');
include_once(dirname(__FILE__).'/../../infra/kConf.php');

$status_div = '';
$new_key_result = '';


$new_key = isset($_POST['new_key']) ? $_POST['new_key'] : false;

if ($new_key) {
	$new_key_result = ActKeyUtils::putNewKey($new_key);
	if (!$new_key_result) {
		$new_key_result = 'New key not accepted!';
	}
}


$installation_type = kConf::get('kaltura_installation_type');
if ($new_key_result === true) {
	$cur_key = $new_key;
	if (@kConf::get('replace_passwords') === true) {
		if (file_exists(dirname(__FILE__).'/replace.php')) {
			include_once(dirname(__FILE__).'/replace.php');
		}
	}
}
else {
	$cur_key = kConf::get('kaltura_activation_key');
}

if ($installation_type == 'CE') {
	$status_div = getUnlimited();
}
else if (!$cur_key) {
	$status_div = getNoKey();
}
else {
	$days_to_expire = ActKeyUtils::daysToExpire($cur_key);
	if ($days_to_expire === false) {
		$status_div = getExpired();
	}
	else if ($days_to_expire === true) {
		$status_div = getUnlimited();
	}
	else {
		$status_div = getNormal($days_to_expire);
	}
}


//-----FUNCTIONS --------------------------------------------------------------

function getNormal($days)
{
	return '<div id="status_normal class="passed">
				<h2 class="passed">
				<strong>You have '.$days.' days left on your Kaltura On-Prem&trade; evaluation period</strong>
				</h2>
				<p>
					For assistance or an upgrade to a commercial license, please
					<a href="http://corp.kaltura.com/about/contact"
					   target="_blank">
					   contact us</a>
				</p>
				</div>';

}


function getExpired()
{
	return '<div id="key_expired" class="failed">
				<h2><span class="status_error">
				Your Kaltura On-Prem&trade; evaluation license has expired
				</span></h2>
				<form name="input" action="index.php" method="post">
					<p>
					To upgrade to a commercial license or extend your evaluation period, please contact
					<a href="http://corp.kaltura.com/about/contact"
					   target="_blank">
					   Kaltura Sales
					</a>
					</p>
					<p>
					Please enter your extension key:
					<input type="text" name="new_key" />
					<input type="submit" value="Extend" />
					</p>
				</form>
			</div>';
}


function getNoKey()
{
	return '<div id="no_act_key" class="failed">
				<h2><span class="status_error">
					Your Kaltura On-Prem&trade; evaluation license has not been activated
				</span></h2>
				
				<form name="input" action="index.php" method="post"><p>
						To activate your license, please enter your activation key:
						<input type="text" name="new_key" />
						<input type="submit" value="Activate" />
				</p></form>
				
				<p>
					For further assistance, contact the
					<a href="http://corp.kaltura.com/about/contact"
					   target="_blank">
					   Kaltura presales engineering team
					</a>
					.
				</p>
			</div>';
}


function getUnlimited()
{
	return '';
}


function getInstallFailed()
{
	return '<div id="install_failed" class="failed">
				<h2><span class="status_error">The installation of your
				Kaltura Community Edition has not been completed.</span></h2>
				<p>You can check your installation-log.txt file, or upload it to the
				Kaltura CE <a
					href="http://www.kaltura.org/forums/server-side-programs-and-components/kaltura-server-community-edition"
					target="_blank">community forum</a> at <a href="http://www.kaltura.org"
					target="_blank">www.kaltura.org</a> for further assistance</p>
			</div>';
}	
			
?>