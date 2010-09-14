<?php use_helper('Javascript') ?>


<DIV style='overflow:hidden' id=_showEditorPage>
<table width="100%" height="100%" border="0" cellpadding="0"
	cellspacing="0">
	<tr height=100%>
		<td align="center" valign="top">
		<?php echo image_tag('edit/videoeditor.jpg', array( 'size'=>'400x310','onclick'=>"alert('this feature is coming soon...')")); ?>
		</td></tr></table>
	
</DIV>		
		
				
		

<script type="text/javascript">

showEditorPage = {

	initialize : function()
	{
		$('_showEditorPage').pageObj = this;
		
		var dialog = ModalDialog.prototype.getCurrentModalDialog();
		dialog.setContentObject(this);
		this.dialog = dialog;
		this.wizard = dialog.wizard;

	},
	
	onEnterPage : function()
	{	
		this.dialog.setTitle("Video Editor");
		this.dialog.setIcon("/images/wizard/editor_wiz_ico.png");	
		
		this.wizard.setButton(1, null);
		this.wizard.setButton(2, 'Metadata' );
		this.wizard.setButton(3, 'Cancel');
			
	},

	onClickButton2 : function()
	{
		this.wizard.gotoPage('showEditorPage2');
	},
	
	onClickButton3 : function()
	{
		showEditorPage.wizard.close();
	}
};

var dataTempName = "<?php echo myContentStorage::getFSContentRootPath().'content/uploads/' ?>" + "<?php echo $sf_user->getAttribute('id') ?>" + "_data";	
showEditorPage.initialize();

</script>

<DIV style='overflow:hidden' id=_showEditorPage2>
<table width="100%" height="100%" border="0" cellpadding="0"
	cellspacing="0">
	<tr height=100%>
		<td align="center" valign="top">
	<textarea cols="90" rows="35">
	<? echo $show_metadata ?>
	</textarea>
		</td>
	</tr>
</table>
</DIV>

<SCRIPT>
showEditorPage2 = {
	initialize : function()
	{
		$('_showEditorPage2').pageObj = this;
		var dialog = ModalDialog.prototype.getCurrentModalDialog();
		dialog.setContentObject(this);
		this.dialog = dialog;
		this.wizard = dialog.wizard;
	},

	onEnterPage : function()
	{	
		this.dialog.setTitle("Metadata");
		this.dialog.setIcon("/images/wizard/editor_wiz_ico.png");	
		
		this.wizard.setButton(1, 'Back');
		this.wizard.setButton(2, null );
		this.wizard.setButton(3, 'Cancel');
		
	} ,
	
	onClickButton3 : function()
	{
		showEditorPage.wizard.close();
	}
}
showEditorPage2.initialize();
</SCRIPT>

