<script type="text/javascript">
<?php
	$swfVersion = null;
	$version = array();
	$swfUrl = $uiConf->getSwfUrl();
	if (preg_match('|/flash/(\w*)/(.*)/|', $swfUrl, $version)) 
	{
		$swfVersion = $version[2];
		if (strpos($swfVersion, "/") !== false) // for sub directories
		{
			$swfVersion = substr($swfVersion, strpos($swfVersion, "/") + 1);
		}
	}
?>
var currentVersion = "<?php echo $swfVersion; ?>";

function updateSwfVersions()
{
	var type = jQuery("select[@name=type] option[@selected]").val();
	
	jQuery("select[@name=version]").empty();
	var jq = new jQuery("<option>Loading...</option>");
	jQuery("select[@name=version]").append(jq);
	
	jQuery.ajax({
			"dataType": "json",
			"url": "<?php echo url_for("system/ajaxGetSwfVersions?type="); ?>/" + type,
			"success": function (data, status) {
				jQuery("select[@name=version]").empty();
				for(var i = 0; i < data.length; i++)
				{
					var version = data[i];
					var jq = new jQuery("<option value=\""+version+"\">"+version+"</option>");
					jQuery("select[@name=version]").append(jq);
				}

				var jqCurrentVersion = jQuery("select[@name=version] option[@value="+currentVersion+"]");
				jqCurrentVersion.attr("selected", "selected");
				if (!jqCurrentVersion.size())
				{
					var jq = new jQuery("<option value=\""+currentVersion+"\">"+currentVersion+" (MISSING)</option>");
					jQuery("select[@name=version]").append(jq);
				}
				updateSwfUrl();
			}
	});
	
}

function updateSwfUrl()
{
	jQuery("input[@name=swfUrlDisabled]").attr("disabled", "disabled");
	jQuery("select[@name=version]").attr("disabled", "");
	
	var swfUrl = "/flash";
	var type = jQuery("select[@name=type] option[@selected]").val();
	
	switch(type)
	{
<?php foreach($directoryMap as $type => $dir): ?>
	case "<?php echo $type; ?>":
		swfUrl += "/<?php echo $dir; ?>";
		swfUrl += "/" + jQuery("select[@name=version] option[@selected]").val();
<?php if (@$swfNames[$type]): ?>
		swfUrl += "/<?php echo $swfNames[$type] ?>";
<?php endif; ?> 
		break;
<?php endforeach; ?>
	default:
		jQuery("select[@name=version]").empty().attr("disabled", "disabled");
		jQuery("input[@name=swfUrlDisabled]").attr("disabled", ""); 
	}
	currentVersion = jQuery("select[@name=version] option[@selected]").val();
	if (type != 0)
		jQuery("input[@name=swfUrlDisabled]").val(swfUrl);
}

function beforeSubmit() {
	jQuery("input[@name=confFilePath]").val(jQuery("input[@name=confFilePathDisabled]").val());
	jQuery("input[@name=swfUrl]").val(jQuery("input[@name=swfUrlDisabled]").val());
	return true;
}

function updatePartnerName() {
	jQuery("#partnerName").text("Loading...");
	jQuery.ajax({
		"dataType": "json",
		"url": "<?php echo url_for("system/createWidgetsFromUiConf?ajax=getPartnerName"); ?>",
		"data": { "id":this.value },
		"success": function (data) {
			jQuery("#partnerName").text(data);
		}
	});
}

function fixFileSync() {
	jQuery.ajax({
		"dataType": "html",
		"url": "<?php echo url_for("system/fixUiConfFileSync?id=".$uiConf->getId()); ?>",
		"success": function (data) {
			alert(data);
			window.location.reload();
		},
		"error": function () {
			alert('Something went wrong...');
		}
	});
}

function toggleFilesdata() {
	jQuery("#uiConfData").toggle();
}

function onCreationModeChange()
{
	if (this.value == <?php echo uiConf::UI_CONF_CREATION_MODE_MANUAL; ?>)
	{
		jQuery("#enableConfFilePath").show();
	}
	else
	{
		jQuery("#enableConfFilePath").hide();
		jQuery("input[@name=confFilePathDisabled]").attr("disabled", true);
	}
}

function isKdp3Swf() {
	return (jQuery("input[@name=swfUrlDisabled]").val().indexOf('kdp3.swf') != -1);
}

jQuery(function () {
	if (isKdp3Swf()) // because some of the kdp3 uiconfs are marked as kdp
		jQuery("select[@name=type]").val("<?php echo uiConf::UI_CONF_TYPE_KDP3; ?>");
	
	// bind callbacks
	jQuery("select[@name=type]").change(updateSwfVersions);
	jQuery("select[@name=version]").change(updateSwfUrl);
	jQuery("select[@name=creationMode]").change(onCreationModeChange);
	jQuery("#enableSwfUrl").click(function (e) {
			jQuery("input[@name=swfUrlDisabled]").attr("disabled", "");
			return false;
	});
	jQuery("#enableConfFilePath").click(function (e) {
		jQuery("input[@name=confFilePathDisabled]").attr("disabled", "");
		return false;
	});
	jQuery("#createWidgets").click(function (e) {
		window.location.href = "<?php echo url_for("system/createWidgetsFromUiConf?uiConfId=".$uiConf->getId()); ?>";
		return false;
	});
	jQuery("#editUiConf").click(function (e) {
		window.location.href = "<?php echo url_for("system/kcwUiConfEditor?id=".$uiConf->getId()); ?>";
		return false;
	});
	jQuery("#previewUiConf").click(function (e) {
		alert("Not implemented yet.");
		return false;
	});
	jQuery("input[@name=partnerId]").change(updatePartnerName);
	jQuery("input[@type=submit]").click(beforeSubmit);
	updateSwfVersions();
	onCreationModeChange();
});
</script>
<div class="mykaltura_viewAll mykaltura_media" style="width: 80%;">
	<div class="content">
		<div class="middle">
			<div id="wraper">
				<?php if ($saved): ?>
				<div style="text-align: center; ">Saved!</div>
				<?php endif; ?>
				<?php if (!$uiConf->getId()): ?>
				<div style="text-align: center; ">UI Conf not found!</div>
				<?php else: ?>
				<form method="post" action="" class="clearfix" enctype="multipart/form-data" >
					<div class="item">
						<label>UI Conf ID</label>
						<span><h2><?php echo $uiConf->getId(); ?></h2></span>
					</div>
					<div class="item">
						<label>Partner ID</label>
						<input name="partnerId" type="text" value="<?php echo $uiConf->getPartnerId(); ?>" size="6" style="width: auto;" />
						<?php if ($partner): ?>
							<h2 id="partnerName">&nbsp;<?php echo $partner->getName(); ?></h2>
						<?php else: ?>
							<h2 id="partnerName">&nbsp;Unknown Partner</h2>
						<?php endif; ?>
					</div>
					<div class="item">
						<label>Name</label>
						<input name="name" type="text" value="<?php echo $uiConf->getName(); ?>" />
					</div>
					<div class="item">
						<label>Width</label>
						<input name="width" type="text" value="<?php echo $uiConf->getWidth(); ?>" size="6" style="width: auto;" />
					</div>
					<div class="item">
						<label>Height</label>
						<input name="height" type="text" value="<?php echo $uiConf->getHeight(); ?>" size="6" style="width: auto;" />
					</div>
					<div class="item">
						<label>Conf File Path</label>
						<input name="confFilePathDisabled" type="text" value="<?php echo $uiConf->getConfFilePath(); ?>" disabled="disabled" />
						<input name="confFilePath" type="hidden" />
						&nbsp;<button id="enableConfFilePath" style="color: #000; height: 25px;">!</button>
					</div>
					<div class="item">
						<label>Creation Mode</label>
						<select name="creationMode">
							<option value="<?php echo uiConf::UI_CONF_CREATION_MODE_MANUAL; ?>" <?php echo ($uiConf->getCreationMode() == uiConf::UI_CONF_CREATION_MODE_MANUAL) ? "selected=\"selected\"" : ""; ?>>Manual</option>
							<option value="<?php echo uiConf::UI_CONF_CREATION_MODE_WIZARD; ?>" <?php echo ($uiConf->getCreationMode() == uiConf::UI_CONF_CREATION_MODE_WIZARD) ? "selected=\"selected\"" : ""; ?>>Wizard</option>
							<option value="<?php echo uiConf::UI_CONF_CREATION_MODE_ADVANCED; ?>" <?php echo ($uiConf->getCreationMode() == uiConf::UI_CONF_CREATION_MODE_ADVANCED) ? "selected=\"selected\"" : ""; ?>>Advanced</option>
						</select>
					</div>
					<div class="item">
						<label>Module Type</label>
						<select name="type">
							<?php foreach($types as $value => $name):?>
							<?php $selected = ($uiConf->getObjType() == $value) ? " selected=\"selected\"" : "" ?>
								<option value="<?php echo $value; ?>"<?php echo $selected; ?>><?php echo $name; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="item">
						<label>Version</label>
						<select name="version"></select>
					</div>
					<div class="item">
						<label>Swf Url</label>
						<input name="swfUrlDisabled" type="text" value="<?php echo $uiConf->getSwfUrl(); ?>" disabled="disabled" />
						<input name="swfUrl" type="hidden" />
						&nbsp;<button id="enableSwfUrl" style="color: #000; height: 25px;">!</button>
					</div>
					<div class="item">
						<label>Created At</label>
						<input type="text" value="<?php echo $uiConf->getCreatedAt(); ?>" disabled="disabled" />
					</div>
					<div class="item">
						<label>Updated At</label>
						<input type="text" value="<?php echo $uiConf->getUpdatedAt(); ?>" disabled="disabled" />
					</div>
					<div class="item">
						<label>Conf Vars</label>
						<input name="confVars" type="text" value="<?php echo $uiConf->getConfVars(); ?>" />
					</div>
					<div class="item">
						<label>Use CDN</label>
						<input name="useCdn" type="checkbox" <?php echo ($uiConf->getUseCdn()) ? "checked=\"checked\"" : "style=\"border: solid 1px red;\""; ?> value="1" />
					</div>
					<div class="item">
						<label>Display In Search</label>
						<select name="displayInSearch">
							<option value="0" <?php echo ($uiConf->getDisplayInSearch() == 0) ? "selected=\"selected\"" : "" ?>>None</option>
							<option value="1" <?php echo ($uiConf->getDisplayInSearch() == 1) ? "selected=\"selected\"" : "" ?>>Partner Only</option>
							<option value="2" <?php echo ($uiConf->getDisplayInSearch() == 2) ? "selected=\"selected\"" : "" ?>>Kaltura Network</option>
						</select>
					</div>
					<div class="item">
						<label>Tags</label>
						<input name="tags" type="text" value="<?php echo $uiConf->getTags(); ?>" />
					</div>
					<div class="item">
						<label>Custom Data</label>
						<textarea disabled="disabled" cols="80"><?php echo $uiConf->getCustomData(); ?></textarea>
					</div>
					<div class="item">
						<label>Files Contents</label>
						<?php if ($uiConf->getCreationMode() == uiConf::UI_CONF_CREATION_MODE_MANUAL): ?>
						This is a manual UIConf
						<?php else: ?>
						<a href="#" onclick="toggleFilesdata();return false;">Click to view/change</a>
						<?php endif; ?>
					</div>
					<div id="uiConfData" style="display:none;">
						<div class="item">
							<label>Conf File:</label>
							<textarea name="uiconf_confFile" cols="80" rows="10"><?php echo htmlspecialchars($uiConf->getConfFile(false, false)); ?></textarea>
						</div>
						<div class="item">
							<label>Features File:</label>
							<textarea name="uiconf_confFileFeatures" cols="80" rows="10"><?php echo htmlspecialchars($uiConf->getConfFileFeatures(false)); ?></textarea>
						</div>
					</div>
					<input type="submit" value="Save" />
					<?php if ($uiConf->getObjType() == uiConf::UI_CONF_TYPE_WIDGET): ?>
					<input type="button" id="createWidgets" value="Create Widgets" style="float: right; margin: 20px 10px;"/>
					<?php endif; ?>
					<?php if ($uiConf->getObjType() == uiConf::UI_CONF_TYPE_CW): ?>
					<input type="button" id="editUiConf" value="Edit UIConf" style="float: right; margin: 20px 10px;"/>
					<?php endif; ?>
					<input type="button" id="previewUiConf" value="Preview" style="float: right; margin: 20px 10px;"/>
				</form>
				<?php endif; ?>
			</div><!-- end #wraper -->
			<?php if ($uiConf->getId()): ?>
			<h2 style="text-align: center;">File sync status:</h2><br />
			<table style="float: none; table-layout: auto; width: auto; margin: 0 auto;">
				<thead>
				<tr>
					<td style="text-align: left; ">
						<?php foreach($fileSyncs as $fileSyncWrap): ?>
							<b><?php echo ($fileSyncWrap["key"]->getObjectSubType() == uiConf::FILE_SYNC_UICONF_SUB_TYPE_DATA) ? "Data" : ""; ?></b>
							<b><?php echo ($fileSyncWrap["key"]->getObjectSubType() == uiConf::FILE_SYNC_UICONF_SUB_TYPE_FEATURES) ? "Features" : ""; ?></b> 
							(<?php echo $fileSyncWrap["key"]; ?>)<br />
							<?php if (count($fileSyncWrap["fileSyncs"]) == 0): ?>
							<font color="red">Not file syncs found</font>
							<?php endif;?>
							<?php foreach($fileSyncWrap["fileSyncs"] as $fileSync):?>
								DC: <?php echo $fileSync->getDc(); ?>,
								Status: <?php echo $fileSync->getStatusAsString(); ?>
								(<?php echo $fileSync->getFullPath(); ?>)
								<br />
							<?php endforeach; ?>
							<br />
						<?php endforeach; ?>
					</td>
				</tr>
				<tr>
					<td><input type="button" value="Fix me..." onclick="fixFileSync();" /></td>
				</tr>
				</thead>
			</table>
			<h2 style="text-align: center;">Widgets per partner that are using this ui conf:</h2> <br />
			<table style="float: none; table-layout: auto; width: auto; margin: 0 auto;">
				<thead>
					<tr>
						<td>Partner ID</td>
						<td>Number of Widgets</td>
						<td></td>
					</tr>
				</thead>
				<?php if ($tooMuchWidgets): ?>
					<tr>
        					<td colspan="3"><h2>To much widgets! (More than 1000)</h2></td>
					</tr>
				<?php else: ?>
        			<?php foreach($widgetsPerPartner as $pid => $count): ?>
        				<tr>
        					<td><?php echo $pid; ?></td>
        					<td><?php echo $count; ?></td>
        					<td><a href="<?php echo url_for("system/viewUiconfWidgets?partnerId=".$pid."&uiConfId=".$uiConf->getId()); ?>">View Widgets</a></td>
        				</tr>
        			<?php endforeach; ?>
    			<?php endif; ?>
			</table>
			<?php endif; ?>
		</div><!-- end middle-->
	</div><!-- end content-->
	<div class="bgB"></div>
</div><!-- end media-->
