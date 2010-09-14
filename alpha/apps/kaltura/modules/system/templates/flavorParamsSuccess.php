<script type="text/javascript">
<?php if ($editFlavorParam && $editFlavorParam->getPartnerId() == 0 && !$advanced): ?>
	jQuery(function(){
		jQuery("button").attr('disabled', true);
	});
<?php endif; ?>
</script>
<?php if ($editFlavorParam): ?>
<form method="post">
	<fieldset>
		<legend>Flavor Params (<?php echo $editFlavorParam->getId(); ?>)</legend>
		<label for="partner-id">Partner Id: </label><br />
		<input type="text" id="partner-id" name="partner-id" value="<?php echo $editFlavorParam->getPartnerId(); ?>" size="5" />
		<br />
		<label for="name">Name: </label><br />
		<input type="text" id="name" name="name" value="<?php echo $editFlavorParam->getName(); ?>" size="30" />
		<br />
		<label for="name">Description: </label><br />
		<textarea id="description" name="description" cols="30" rows="2"><?php echo $editFlavorParam->getDescription(); ?></textarea>
		<br />
		<label for="tags">Tags: </label><br />
		<input type="text" id="tags" name="tags" value="<?php echo $editFlavorParam->getTags(); ?>" size="40" />
		(<?php echo implode(", ", flavorParams::getValidTags()); ?>)
		<br />
		<label for="format">Format: </label><br />
		<select id="format" name="format">
			<?php foreach($formats as $name => $format): ?>
			<option value="<?php echo $format; ?>" <?php echo ($editFlavorParam->getFormat() == $format) ? "selected=\"selected\"" : "" ?>><?php echo $name; ?></option>
			<?php endforeach; ?>
		</select>
		<br />
		Dimensions (W x H): 
		<input type="text" id="width" name="width" value="<?php echo $editFlavorParam->getWidth(); ?>" size="5" /> X  
		<input type="text" id="height" name="height" value="<?php echo $editFlavorParam->getHeight(); ?>" size="5"  /> pixels
		<br />
		<label for="two-pass">Two Pass:</label><input type="checkbox" id="two-pass" name="two-pass" value="1" <?php echo ($editFlavorParam->getTwoPass()) ? 'checked="checked"' : ''; ?> />
		<br />
		<label for="video-codec">Video codec: </label>
		<select id="video-codec" name="video-codec">
			<?php foreach($videoCodecs as $name => $videoCodec): ?>
			<option value="<?php echo $videoCodec; ?>" <?php echo ($editFlavorParam->getVideoCodec() == $videoCodec) ? "selected=\"selected\"" : "" ?>><?php echo $name; ?></option>
			<?php endforeach; ?>
		</select>,
		<label for="video-bitrate">bitrate </label>
		<input type="text" id="video-bitrate" name="video-bitrate" value="<?php echo $editFlavorParam->getVideoBitrate(); ?>" size="5" />kbps,
		<label for="frame-rate">frame rate </label>
		<input type="text" id="frame-rate" name="frame-rate" value="<?php echo $editFlavorParam->getFrameRate(); ?>" size="5" />fps,
		<label for="gop-size">gop size</label>
		<input type="text" id="gop-size" name="gop-size" value="<?php echo $editFlavorParam->getGopSize(); ?>" size="5" />frames
		<br />
		<label for="audio-codec">Audio codec </label>
		<select id="audio-codec" name="audio-codec">
			<?php foreach($audioCodecs as $name => $audioCodec): ?>
			<option value="<?php echo $audioCodec; ?>" <?php echo ($editFlavorParam->getAudioCodec() == $audioCodec) ? "selected=\"selected\"" : "" ?>><?php echo $name; ?></option>
			<?php endforeach; ?>
		</select>,
		<label for="audio-bitrate">bitrate </label>
		<input type="text" id="audio-bitrate" name="audio-bitrate" value="<?php echo $editFlavorParam->getAudioBitrate(); ?>" size="5" />kbps,
		<label for="audio-channels">channels </label>
		<input type="text" id="audio-channels" name="audio-channels" value="<?php echo $editFlavorParam->getAudioChannels(); ?>" size="2" />,
		<label for="audio-sample-rate">sample rate </label>
		<input type="text" id="audio-sample-rate" name="audio-sample-rate" value="<?php echo $editFlavorParam->getAudioSampleRate(); ?>" size="5" />,
		<label for="audio-resolution">resolution </label>
		<input type="text" id="audio-resolution" name="audio-resolution" value="<?php echo $editFlavorParam->getAudioResolution(); ?>" size="5" />
		<br />
		<label for="conversion-engines">Conversion engines:     (Delimited with ",") </label>
		<label for="conversion-engines-extra-params">Extra params:(Delimited with "|") </label><br /> 
		 
		<input type="text" id="conversion-engines" name="conversion-engines" value="<?php echo $editFlavorParam->getConversionEngines(); ?>" size="60" /> 
		<input type="text" id="conversion-engines-extra-params" name="conversion-engines-extra-params" value="<?php echo $editFlavorParam->getConversionEnginesExtraParams(); ?>" size="60" /> 
		<br /> 
		<label for="operators">Operators:</label><br /> 
		<textarea id="operators" name="operators" cols="47" rows="2"><?php echo $editFlavorParam->getOperators(); ?></textarea><br/>
		<label for="is-default">Is Default: </label><input type="checkbox" id="is-default" name="is-default" value="1" <?php echo ($editFlavorParam->getIsDefault()) ? 'checked="checked"' : ''; ?> />
		<label for="ready-behavior">Ready Behavior:</label> 
		<select id="ready-behavior" name="ready-behavior">
			<?php foreach($readyBehaviors as $name => $type): ?>
			<option value="<?php echo $type; ?>" <?php echo ($editFlavorParam->getReadyBehavior() == $type) ? "selected=\"selected\"" : "" ?>><?php echo $name; ?></option>
			<?php endforeach; ?>
		</select>

		<label for="version">Engine version </label>
		<input type="text" id="engine-version" name="engine-version" value="<?php echo $editFlavorParam->getEngineVersion(); ?>" size="1" />

		<br />
		<button type="submit">Submit</button>
		<a href="<?php echo url_for("system/flavorParams?pid=".$pid); ?>">Close</a>
	</fieldset>
</form>
<?php else: ?>
<form action="<?php echo url_for("system/flavorParams"); ?>" method="get">
	<input type="text" id="pid" name="pid" value="<?php echo $pid; ?>" size="5" />
	<button type="submit">Change</button>
</form>
<?php endif; ?>
<br />
<table border="1">
	<thead>
		<tr>
			<th>ID</th>
			<th>Partner ID</th>
			<th>Name</th>
			<th>Tags</th>
			<th>Format</th>
			<th>Dimensions</th>
			<th>Video</th>
			<th>Audio</th>
			<th>Engines</th>
			<th>Extra</th>
			<th colspan="2">Actions</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($flavorParams as $flavorParam): ?>
		<tr <?php echo ($editFlavorParam && $flavorParam->getId() === $editFlavorParam->getId()) ? "style='background-color: silver;'" : "" ?>>
			<td align="center">
				<?php echo ($flavorParam->getPartnerId() == 0) ? "*" : ""; ?>
				<?php if ($flavorParam->getPartnerId() == $pid):?>
					<a href="<?php echo url_for("system/flavorParams?id=".$flavorParam->getId()."&pid=".$pid); ?>"><?php echo $flavorParam->getId(); ?></a>
				<?php else: ?>
					<?php echo $flavorParam->getId(); ?>
				<?php endif; ?>
				<?php echo ($flavorParam->getPartnerId() == 0) ? "*" : ""; ?>
			</td>
			<td><?php echo $flavorParam->getPartnerId(); ?></td>
			<td><?php echo $flavorParam->getName(); ?></td>
			<td><?php echo $flavorParam->getTags(); ?></td>
			<td><?php echo $flavorParam->getFormat(); ?></td>
			<td><?php echo $flavorParam->getWidth(); ?>x<?php echo $flavorParam->getHeight(); ?></td>
			<td><?php echo $flavorParam->getVideoCodec(); ?>@<?php echo $flavorParam->getVideoBitrate(); ?>kbps, <?php echo $flavorParam->getFrameRate(); ?>fps, gop <?php echo $flavorParam->getGopSize(); ?></td>
			<td><?php echo $flavorParam->getAudioCodec(); ?>@<?php echo $flavorParam->getAudioBitrate(); ?>kbps, <?php echo $flavorParam->getAudioSampleRate(); ?>hz, <?php echo $flavorParam->getAudioResolution(); ?>bit</td>
			<td><?php echo $flavorParam->getConversionEngines(); ?></td>
			<td>
				<?php echo ($flavorParam->getIsDefault()) ? 'Default,' : ''; ?>
				<?php echo flavorParamsAction::getEnumValue('flavorParamsConversionProfile', 'READY_BEHAVIOR', $flavorParam->getReadyBehavior()); ?>
			</td>
			<td><a href="<?php echo url_for("system/flavorParams?pid=".$pid."&id=".$flavorParam->getId()."&clone=1"); ?>">Clone</a></td>
			<td>
				<?php if (((int)$pid !== 0 && $flavorParam->getPartnerId() === (int)$pid) || ((int)$pid === 0 && $advanced)): ?>
					<a href="<?php echo url_for("system/flavorParams?pid=".$pid."&id=".$flavorParam->getId()."&delete=1&advanced=".$advanced); ?>" onclick="return confirm('Are you sure?');">Delete</a>
				<?php endif; ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>