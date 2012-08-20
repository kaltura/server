<?php 
	$kcwEditorVersion = "v1.2.0"; 
	$kcwBaseUrl = "http://".kConf::get("www_host")."/flash/kcweditor/";
?>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/swfobject/2.1/swfobject.js"></script>

<div id="kcwEditor"></div>
<script type="text/javascript">
	var flashvars = {
		baseXmlUrl: "<?php echo $kcwBaseUrl . $kcwEditorVersion ?>/assets/base.xml"
	};
	var params = {};
	params.quality = "high";
	params.bgcolor = "#ffffff";
	params.allowscriptaccess = "sameDomain";
	params.allowfullscreen = "true";
	var attributes = {};
	attributes.id = "KCWEditor";
	attributes.name = "KCWEditor";
	attributes.align = "middle";

    // div "#wrap" comes from the template
	swfobject.embedSWF("<?php echo $kcwBaseUrl . $kcwEditorVersion ?>/KCWEditor.swf", "kcwEditor", "100%", "100%", "9.0", false, flashvars, params, attributes);

	var editXML = "<?php echo rawurlencode($uiConf->getConfFile()); ?>";
	var shouldSave = false;
	  	
	function KCWEditor_ReadyHandler()
	{
		if (editXML != "")
			setConf();
	}
	
	function setConf() 
	{
		var f = document.getElementById("KCWEditor");
		f.setConfXML(editXML);
	}
	
	function KCWEditor_publishXML(xml)
	{
		document.getElementById("confFile").value = unescape(xml);
		shouldSave = true;
	}
	
	function KCWEditor_close()
	{
		if (shouldSave)
			document.getElementById("submit").click();
		else
			window.location="<?php echo url_for("system/editUiconf?id=".$uiConf->getId());?>";
	}

	jQuery("#wrap").css("height", "100%");
	jQuery("#wrap").css("width", "100%");
	jQuery("#wrap").css("padding", "0");	
</script>

<form method="post" style="display: none">
<textarea rows="5" cols="20" id="confFile" name="confFile" style="visibility: hidden"></textarea>
<input id="submit" type="submit" style="visibility: hidden">
</form>