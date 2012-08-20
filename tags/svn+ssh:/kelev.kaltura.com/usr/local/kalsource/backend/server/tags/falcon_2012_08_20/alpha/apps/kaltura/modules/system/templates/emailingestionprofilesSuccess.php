<script type="text/javascript">
<?php if ($editEmailIngestionProfile && $editEmailIngestionProfile->getPartnerId() == 0 && !$advanced): ?>
	jQuery(function(){
		//jQuery("button").attr('disabled', true);
	});
<?php endif; ?>
</script>
<a href="<?php echo url_for("system/emailingestionprofiles?editing=add"); ?>">Add New</a><br /><br />
<?php if ($editEmailIngestionProfile): ?>
<form method="post" <?php echo ($formaction)? 'action="'.url_for($formaction).'"': ''; ?>>
	<fieldset>
		<legend>Email Ingestion Profile (<?php echo $editEmailIngestionProfile->getId(); ?>)</legend>
		<label for="partner-id">Partner Id: </label><br />
		<input type="text" id="partner-id" name="partner-id" value="<?php echo $editEmailIngestionProfile->getPartnerId(); ?>" size="5" />
		<br />
		<label for="name">Name: </label><br />
		<input type="text" id="name" name="name" value="<?php echo $editEmailIngestionProfile->getName(); ?>" size="30" />
		<br />
		<label for="name">Description: </label><br />
		<textarea id="description" name="description" cols="30" rows="2"><?php echo $editEmailIngestionProfile->getDescription(); ?></textarea>
		<br />
		<label for="email-address">Email Address: </label><br />
		<input type="text" id="email-address" name="email-address" value="<?php echo $editEmailIngestionProfile->getEmailAddress(); ?>" size="50" />
		<br />
		<label for="mailbox-id">Mailbox ID: </label><br />
		<input type="text" id="mailbox-id" name="mailbox-id" value="<?php echo $editEmailIngestionProfile->getMailboxId(); ?>" size="50" />

		<br />
		<label for="default-tags">Default Tags: </label><br />
		<input type="text" id="default-tags" name="default-tags" value="<?php echo $editEmailIngestionProfile->getDefaultTags(); ?>" size="40" />
		<br />
		<label for="default-admintags">Default Admin-Tags: </label><br />
		<input type="text" id="default-admintags" name="default-admintags" value="<?php echo $editEmailIngestionProfile->getDefaultAdminTags(); ?>" size="40" />
		
		<br />
		<label for="conversion-profile2-id">Default conversion profile: </label><br />
		<input type="text" id="conversion-profile2-id" name="conversion-profile2-id" value="<?php echo $editEmailIngestionProfile->getConversionProfile2Id(); ?>" size="50" />

		<br />
		<label for="moderation-status">Moderation Status: </label><br />
		<select id="moderation-status" name="moderation-status">
			<?php foreach($entryModerationStatuses as $name => $status): ?>
			<option value="<?php echo $status; ?>" <?php echo ($editEmailIngestionProfile->getModerationStatus() == $status) ? "selected=\"selected\"" : "" ?>><?php echo $name; ?></option>
			<?php endforeach; ?>
		</select>		
		<br />
		<label for="default-category">Default Category: </label><br />
		<input type="text" id="default-category" name="default-category" value="<?php echo $editEmailIngestionProfile->getDefaultCategory(); ?>" size="50" />
		<br />
		<label for="default-userid">Default UserId: </label><br />
		<input type="text" id="default-userid" name="default-userid" value="<?php echo $editEmailIngestionProfile->getDefaultUserId(); ?>" size="50" />
		<br />
		<label for="max-attachment-size-kbytes">Maximum file size for attachments (in kilobytes): </label><br />
		<input type="text" id="max-attachment-size-kbytes" name="max-attachment-size-kbytes" value="<?php echo $editEmailIngestionProfile->getMaxAttachmentSizeKbytes(); ?>" size="50" />
		<br />
		<label for="max-attachments-per-mail">Maximum attachments per mail: </label><br />
		<input type="text" id="max-attachments-per-mail" name="max-attachments-per-mail" value="<?php echo $editEmailIngestionProfile->getMaxAttachmentsPerMail(); ?>" size="50" />
		<br />
		<?php if($editEmailIngestionProfile->isNew()): ?>
		<input type="hidden" name="addingnew" value="true" />
		<?php endif; ?>
		<button type="submit">Submit</button>
		<a href="<?php echo url_for("system/emailingestionprofiles?pid=".$pid); ?>">Close</a>
	</fieldset>
</form>
<?php else: ?>
<form action="<?php echo url_for("system/emailingestionprofiles"); ?>" method="get">
	<input type="text" id="pid" name="pid" value="<?php echo $pid; ?>" size="5" />
	<button type="submit">Change</button>
	<a href="<?php echo url_for("system/emailingestionprofiles"); ?>">reset</a>
</form>
<?php endif; ?>
<?php 
if(is_null($editEmailIngestionProfile) || !$editEmailIngestionProfile->isNew()): ?>
<br />
<table border="1">
	<thead>
		<tr>
			<th>ID</th>
			<th>Partner ID</th>
			<th>Name</th>
			<th>Email Address</th>
			<th>Mailbox ID</th>
			<th>Default Tags</th>
			<th>Default Admin Tags</th>
			<th>Default Category</th>
			<th>Default UserId</th>
			<th>Conversion Profile ID</th>
			<th>Moderation Status</th>
			<th>Maximum Attachment Size (KBytes)</th>
			<th>Maximum Attachments Per Mail</th>
			<th colspan="2">Actions</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($EmailIngestionProfiles as $eip): ?>
		<tr <?php echo ($editEmailIngestionProfile && $eip->getId() === $editEmailIngestionProfile->getId()) ? "style='background-color: silver;'" : "" ?>>
			<td align="center">
				<?php echo ($eip->getPartnerId() == 0) ? "*" : ""; ?>
				<?php if ($eip->getPartnerId() == $pid):?>
					<a href="<?php echo url_for("system/emailingestionprofiles?id=".$eip->getId()."&pid=".$pid); ?>"><?php echo $eip->getId(); ?></a>
				<?php else: ?>
					<?php echo $eip->getId(); ?>
				<?php endif; ?>
				<?php echo ($eip->getPartnerId() == 0) ? "*" : ""; ?>
			</td>
			<td><?php echo $eip->getPartnerId(); ?></td>
			<td><?php echo $eip->getName(); ?></td>
			<td><?php echo $eip->getEmailAddress(); ?></td>
			<td><?php echo $eip->getMailboxId(); ?></td>
			<td><?php echo $eip->getDefaultTags(); ?></td>
			<td><?php echo $eip->getDefaultAdminTags(); ?></td>
			<td><?php echo $eip->getDefaultCategory(); ?></td>
			<td><?php echo $eip->getDefaultUserId(); ?></td>
			<td><?php echo $eip->getConversionProfile2Id(); ?></td>
			<td><?php echo $eip->getModerationStatus().' ('.@array_search($eip->getModerationStatus(), $entryModerationStatuses).')'; ?></td>
			<td><?php echo $eip->getMaxAttachmentSizeKBytes(); ?></td>
			<td><?php echo $eip->getMaxAttachmentsPerMail(); ?></td>
			<td></td>
			<td>
				<?php if (((int)$pid !== 0 && $eip->getPartnerId() === (int)$pid) || ((int)$pid === 0 && $advanced)): ?>
					<a href="<?php echo url_for("system/flavorParams?pid=".$pid."&id=".$eip->getId()."&delete=1&advanced=".$advanced); ?>" onclick="return confirm('Are you sure?');">Delete</a>
				<?php endif; ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php endif; ?>