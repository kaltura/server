<script>
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
jQuery(function () {
	jQuery("input[@name=partnerId]").change(updatePartnerName);
});
</script>

<div class="mykaltura_viewAll mykaltura_media" style="width: 80%;">
	<div class="content">
		<div class="middle">
			<div id="wraper">
				<?php if($partner_error): ?>
				<div  style="text-align: center; color:#ff0000;"><?php echo $partner_error; ?></div>
				<?php endif; ?>
				<form method="post" name="addUiconf" class="clearfix">
					Partner ID: <input type="text" name="partnerId" value="" /> <span id="partnerName"></span><br />
					<input type="submit" value="Add UIConf" />
				</form>
			</div>
		</div>
	</div>
</div>