<script type="text/javascript">

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

function beforeSubmit() {
	jQuery("input[@name=numOfWidgets]").css("border-color", '');
	jQuery("input[@name=partnerId]").css("border-color", '');
	
	var numOfWidgets = jQuery("input[@name=numOfWidgets]").val();
	var partnerId = jQuery("input[@name=partnerId]").val();
	var uiConfId = "<?php echo $uiConf->getId(); ?>";
	var startIndex = jQuery("input[@name=startIndex]").val();
	
	numOfWidgets = Number(numOfWidgets);
	startIndex = Number(startIndex);

	if (!partnerId) {
		jQuery("input[@name=partnerId]").css("border-color", "red");
		return false;
	}
	
	if (!numOfWidgets) {
		jQuery("input[@name=numOfWidgets]").css("border-color", "red");
		return false;
	}

	var widgetIds = new Array();
	var prefix = "";  
	if (jQuery("input[@name=prefix]").attr("checked")) 
		prefix = "_";
	
	for(var i = startIndex; i < startIndex + numOfWidgets; i++) {
		if (i != 0)
			widgetIds.push(prefix + partnerId + "_" + uiConfId + "_" + i);
		else
			widgetIds.push(prefix + partnerId + "_" + uiConfId);
	}

	var widgetIdsStr = "";
	var count = 0;
	for(var i = 0; i < numOfWidgets; i++) {
		if (count == 5) {
			widgetIdsStr += "\n";
			count = 0;
		}
		widgetIdsStr += widgetIds[i] + ", ";
		count++;
	}

	widgetIdsStr = widgetIdsStr.substring(0, widgetIdsStr.length - 2);
	
	
	if (confirm("This will create the following widget ids: \n" + widgetIdsStr))
	{
		// validate widget ids
		jQuery.ajax({
			"dataType": "json",
			"url": "<?php echo url_for("system/createWidgetsFromUiConf?ajax=validateWidgetIds"); ?>",
			"data": { "widgetIds": widgetIds.toString() },
			"type": "POST",
			"success": function (data) {

				if (data && data.length > 0) {
					alert("The following widgets already exist: " + data);
					return;
				}
				
				jQuery("form").get(0).submit();
			}
		});
	}
	
	return false;
}

jQuery(function () {
	// bind callbacks
	jQuery("input[@name=partnerId]").change(updatePartnerName);
	jQuery("input[@type=submit]").click(beforeSubmit);
});
</script>
<div class="mykaltura_viewAll mykaltura_media" style="width: 80%;">
	<div class="content">
		<div class="middle">
			<div id="wraper">
				<form method="post" action="" class="clearfix" enctype="multipart/form-data" >
					<div class="item">
						<label>UI Conf ID</label>
						<span><h3><?php echo $uiConf->getId(); ?></h3></span>
					</div>
					<div class="item">
						<label>For Partner ID</label>
						<input name="partnerId" type="text" value="<?php echo $uiConf->getPartnerId(); ?>" size="6" style="width: auto;" />
						<?php if ($partner): ?>
							<h2 id="partnerName" style="float: left; margin-left: 5px;"><?php echo $partner->getName(); ?></h2>
						<?php else: ?>
							<h2 id="partnerName" style="float: left; margin-left: 5px;">Unknown Partner</h2>
						<?php endif; ?>
					</div>
					<div class="item">
						<label>How many widgets?</label>
						<input name="numOfWidgets" type="text" size="6" style="width: auto;" />
						<label style="width: auto; margin-left: 5px; margin-right: 5px;">start index </label>
						<input name="startIndex" type="text" size="1" style="width: auto;" />
					</div>
					<div class="item">
						<label></label>
					</div>
					<div class="item">
						<label><u>Advanced Stuff</u></label>
					</div>
					<div class="item">
						<label>Prefix "_"?</label>
						<input name="prefix" type="checkbox" value="1" /> (Use for drupal)
					</div>
					<div class="item">
						<label>Entry Id</label>
						<input name="entryId" type="text" size="6" style="width: auto;" />
					</div>
					<div class="item">
						<label>Security Type</label>
						<input name="securityType" type="text" size="6" style="width: auto;" />
					</div>
					<div class="item">
						<label>Security Policy</label>
						<input name="securityPolicy" type="text" size="6" style="width: auto;" />
					</div>
					<input type="submit" value="Create" />
				</form>
			</div><!-- end $wraper -->
		</div><!-- end middle-->
	</div><!-- end content-->
	<div class="bgB"></div>
</div><!-- end media-->
