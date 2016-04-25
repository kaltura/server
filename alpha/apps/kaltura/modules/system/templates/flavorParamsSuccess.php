<?php
$disabledSave = false;
$disabled = '';
if ($editFlavorParam)
{
	if ($editFlavorParam->getPartnerId() == 0 && $advanced != 2)
	{
		$disabledSave = true;
		$disabled = 'disabled="disabled"';
	}
		
	if ($advanced == 0)
		$disabled = 'disabled="disabled"';
?>
	<form method="post">
		<fieldset>
			<legend><b>Flavor Params (<?php echo $editFlavorParam->getId(); ?>)</b></legend>
			<label for="partner-id">Partner Id: </label><br />
			<input type="text" id="partner-id" name="partner-id" value="<?php echo $editFlavorParam->getPartnerId(); ?>" size="5" />
			<br />
			<label for="name">Name: </label>
			<span style="color: white; background:white;><label for="name">..............................................</label><span style="color: black; background:white;>
			<label for="name">SystemName: </label><br />
			<input type="text" id="name" name="name" value="<?php echo $editFlavorParam->getName(); ?>" size="30" <?php echo $disabled; ?>/>
			<input type="text" id="systemName" name="systemName" value="<?php echo $editFlavorParam->getSystemName(); ?>" size="30" <?php echo $disabled; ?>/>
			<br />
			<label for="name">Description: </label><br />
			<textarea id="description" name="description" cols="30" rows="2" <?php echo $disabled; ?>><?php echo $editFlavorParam->getDescription(); ?></textarea>
			<br />
			<label for="tags">Tags: </label><br />
			<input type="text" id="tags" name="tags" value="<?php echo $editFlavorParam->getTags(); ?>" size="40" <?php echo $disabled; ?>/>
			(<?php echo implode(", ", flavorParams::getValidTags()); ?>)
			<br />
			<label for="format"><b>Format: </b></label><br />
			<select id="format" name="format" <?php echo $disabled; ?>>
				<?php foreach($formats as $name => $format): ?>
				<option value="<?php echo $format; ?>" <?php echo ($editFlavorParam->getFormat() == $format) ? "selected=\"selected\"" : "" ?>><?php echo $name; ?></option>
				<?php endforeach; ?>
			</select>
			<br />
			<label for="video-codec"><b>Video codec: </b></label>
			<select id="video-codec" name="video-codec" <?php echo $disabled; ?>>
				<?php foreach($videoCodecs as $name => $videoCodec): ?>
				<option value="<?php echo $videoCodec; ?>" <?php echo ($editFlavorParam->getVideoCodec() == $videoCodec) ? "selected=\"selected\"" : "" ?>><?php echo $name; ?></option>
				<?php endforeach; ?>
			</select>,
			<label for="video-bitrate">Bitrate </label>
			<input type="text" id="video-bitrate" name="video-bitrate" value="<?php echo $editFlavorParam->getVideoBitrate(); ?>" size="5" <?php echo $disabled; ?>/>kbps,
			Dimensions (W x H):
			<input type="text" id="width" name="width" value="<?php echo $editFlavorParam->getWidth(); ?>" size="5" <?php echo $disabled; ?>/> X
			<input type="text" id="height" name="height" value="<?php echo $editFlavorParam->getHeight(); ?>" size="5"  <?php echo $disabled; ?>/> pixels
			<br />
			<label for="frame-rate">frame rate </label>
			<input type="text" id="frame-rate" name="frame-rate" value="<?php echo $editFlavorParam->getFrameRate(); ?>" size="5" <?php echo $disabled; ?>/>fps,
			<label for="gop-size">gop size</label>
			<input type="text" id="gop-size" name="gop-size" value="<?php echo $editFlavorParam->getGopSize(); ?>" size="5" <?php echo $disabled; ?>/>frames,
			<label for="max-frame-rate">max frame rate </label>
			<input type="text" id="max-frame-rate" name="max-frame-rate" value="<?php echo $editFlavorParam->getMaxFrameRate(); ?>" size="5" <?php echo $disabled; ?>/>fps
			<br />
			<label for="two-pass">Two Pass:</label><input type="checkbox" id="two-pass" name="two-pass" value="1" <?php echo ($editFlavorParam->getTwoPass()) ? 'checked="checked"' : ''; ?> <?php echo $disabled; ?>/>
			<label for="rotate">Rotate:</label><input type="checkbox" id="rotate" name="rotate" value="1" <?php echo ($editFlavorParam->getRotate()) ? 'checked="checked"' : ''; ?> <?php echo $disabled; ?>/>
			<label for="aspectRatioProcessingMode">ARmode: </label>
			<input type="text" id="aspectRatioProcessingMode" name="aspectRatioProcessingMode" value="<?php echo $editFlavorParam->getAspectRatioProcessingMode(); ?>" size="1" <?php echo $disabled; ?>/>
			<label for="forceFrameToMultiplication16">ForceMod16:</label><input type="checkbox" id="forceFrameToMultiplication16" name="forceFrameToMultiplication16" value="1" <?php echo ($editFlavorParam->getForceFrameToMultiplication16()) ? 'checked="checked"' : ''; ?> <?php echo $disabled; ?>/>
			<label for="isGopInSec">IsGopInSec:</label><input type="checkbox" id="isGopInSec" name="isGopInSec" value="1" <?php echo ($editFlavorParam->getIsGopInSec()) ? 'checked="checked"' : ''; ?> <?php echo $disabled; ?>/>
			<br />
			<label for="isAvoidVideoShrinkFramesizeToSource">NoFrameSizeShrink:</label><input type="checkbox" id="isAvoidVideoShrinkFramesizeToSource" name="isAvoidVideoShrinkFramesizeToSource" value="1" <?php echo ($editFlavorParam->getIsAvoidVideoShrinkFramesizeToSource()) ? 'checked="checked"' : ''; ?> <?php echo $disabled; ?>/>
			<label for="isAvoidVideoShrinkBitrateToSource">NoBitRateShrink:</label><input type="checkbox" id="isAvoidVideoShrinkBitrateToSource" name="isAvoidVideoShrinkBitrateToSource" value="1" <?php echo ($editFlavorParam->getIsAvoidVideoShrinkBitrateToSource()) ? 'checked="checked"' : ''; ?> <?php echo $disabled; ?>/>
			<label for="isAvoidForcedKeyFrames">NoForcedKeyFrames:</label><input type="checkbox" id="isAvoidForcedKeyFrames" name="isAvoidForcedKeyFrames" value="1" <?php echo ($editFlavorParam->getIsAvoidForcedKeyFrames()) ? 'checked="checked"' : ''; ?> <?php echo $disabled; ?>/>
			<br />
			<label for="isVideoFrameRateForLowBrAppleHls">LowBrAppleHls:</label><input type="checkbox" id="isVideoFrameRateForLowBrAppleHls" name="isVideoFrameRateForLowBrAppleHls" value="1" <?php echo ($editFlavorParam->getIsVideoFrameRateForLowBrAppleHls()) ? 'checked="checked"' : ''; ?> <?php echo $disabled; ?>/>
			<label for="text">AnamorphicPixels </label>
			<input type="text" id="anamorphicPixels" name="anamorphicPixels" value="<?php echo $editFlavorParam->getAnamorphicPixels(); ?>" size="5" <?php echo $disabled; ?>/>
			<br />
			<label for="watermarkData">Watermark:</label>
			<span style="color: white; background:white;><label for="name">............................................................</label><span style="color: black; background:white;>
			<label for="subtitleData">Subtitle:</label><br />
			<textarea id="watermarkData" name="watermarkData" cols="40" rows="1" <?php echo $disabled; ?>><?php echo $editFlavorParam->getWatermarkData(); ?></textarea>
			<textarea id="subtitlesData" name="subtitlesData" cols="40" rows="1" <?php echo $disabled; ?>><?php echo $editFlavorParam->getSubtitlesData(); ?></textarea>
			<br/>
			<br />


			<label for="audio-codec"><b>Audio codec </b></label>
			<select id="audio-codec" name="audio-codec" <?php echo $disabled; ?>>
				<?php foreach($audioCodecs as $name => $audioCodec): ?>
				<option value="<?php echo $audioCodec; ?>" <?php echo ($editFlavorParam->getAudioCodec() == $audioCodec) ? "selected=\"selected\"" : "" ?>><?php echo $name; ?></option>
				<?php endforeach; ?>
			</select>,
			<label for="audio-bitrate">bitrate </label>
			<input type="text" id="audio-bitrate" name="audio-bitrate" value="<?php echo $editFlavorParam->getAudioBitrate(); ?>" size="5" <?php echo $disabled; ?>/>kbps,
			<label for="audio-channels">channels </label>
			<input type="text" id="audio-channels" name="audio-channels" value="<?php echo $editFlavorParam->getAudioChannels(); ?>" size="2" <?php echo $disabled; ?>/>,
			<label for="audio-sample-rate">sample rate </label>
			<input type="text" id="audio-sample-rate" name="audio-sample-rate" value="<?php echo $editFlavorParam->getAudioSampleRate(); ?>" size="5" <?php echo $disabled; ?>/>,
			<label for="audio-resolution">resolution </label>
			<input type="text" id="audio-resolution" name="audio-resolution" value="<?php echo $editFlavorParam->getAudioResolution(); ?>" size="5" <?php echo $disabled; ?>/>
			<br />
			<br />
			<label for="conversion-engines"><b>Conversion engines:</b></label> <label>(Delimited with ",")</label>
			<span style="color: white; background:white;><label for="name">.......................</label></span>
			<label for="conversion-engines-extra-params"><b>Extra params:(Delimited with "|") </b></label>
			<br />
			 
			<input type="text" id="conversion-engines" name="conversion-engines" value="<?php echo $editFlavorParam->getConversionEngines(); ?>" size="60" <?php echo $disabled; ?>/>

			<textarea id="conversion-engines-extra-params" name="conversion-engines-extra-params" cols="47" rows="2" <?php echo $disabled; ?>><?php echo $editFlavorParam->getConversionEnginesExtraParams(); ?></textarea><br/>

			<label for="operators"><b>Operators:</b></label>
			<span style="color: white; background:white;><label for="name">......................................................................</label></span>
			<label for="operators"><b>MultiStream:</b></label>
			<br />
			<textarea id="operators" name="operators" cols="47" rows="2" <?php echo $disabled; ?>><?php echo $editFlavorParam->getOperators(); ?></textarea>
<!--
To activate the 'multiStream' remove the remarks from that portion
and "" that are placed arround editFlavorParam->getMultiStream
			<label for="multiStream">MultiStream:</label><br />
-->
			<textarea id="multiStream" name="multiStream" cols="47" rows="2" <?php echo $disabled; ?>><?php echo $editFlavorParam->getMultiStream(); ?></textarea><br/>


			<label for="sourceAssetParamsIds"><b>SourceAssetParamsIds: </b></label>
			<input type="text" id="sourceAssetParamsIds" name="sourceAssetParamsIds" value="<?php echo $editFlavorParam->getSourceAssetParamsIds(); ?>" size="40" <?php echo $disabled; ?>/>	
			<br />
			<label for="is-default">Is Default: </label><input type="checkbox" id="is-default" name="is-default" value="1" <?php echo ($editFlavorParam->getIsDefault()) ? 'checked="checked"' : ''; ?> <?php echo $disabled; ?>/>
			<label for="ready-behavior">Ready Behavior:</label>
			<select id="ready-behavior" name="ready-behavior" <?php echo $disabled; ?>>
				<?php foreach($readyBehaviors as $name => $type): ?>
				<option value="<?php echo $type; ?>" <?php echo ($editFlavorParam->getReadyBehavior() == $type) ? "selected=\"selected\"" : "" ?>><?php echo $name; ?></option>
				<?php endforeach; ?>
			</select>

			<label for="version">Engine version </label>
			<input type="text" id="engine-version" name="engine-version" value="<?php echo $editFlavorParam->getEngineVersion(); ?>" size="1" <?php echo $disabled; ?>/>

			<label for="type">Type </label>
			<input type="text" id="type" name="type" value="<?php echo $editFlavorParam->getType(); ?>" size="5" <?php echo $disabled; ?>/>

			<br />
			<?php if (!$disabledSave): ?>
			<button type="submit">Submit</button>
			<?php endif; ?>
			<a href="<?php echo url_for("system/flavorParams?pid=".$pid); ?>">Close</a>
		</fieldset>
	</form>
<?php
}
else
{
?>
<form action="<?php echo url_for("system/flavorParams"); ?>" method="get">
	<input type="text" id="pid" name="pid" value="<?php echo $pid; ?>" size="5" />
	<button type="submit">Change</button>
</form>
<?php
}
?>
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
				<?php if ($flavorParam->getPartnerId() == $pid && $advanced == 1):?>
					<a href="<?php echo url_for("system/flavorParams?id=".$flavorParam->getId()."&pid=".$pid); ?>"><?php echo $flavorParam->getId(); ?></a>
				<?php elseif ($advanced == 2): ?>
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
				<?php if ($advanced == 1): ?>
					<?php if (((int)$pid !== 0 && $flavorParam->getPartnerId() === (int)$pid)): ?>
						<a href="<?php echo url_for("system/flavorParams?pid=".$pid."&id=".$flavorParam->getId()."&delete=1&advanced=".$advanced); ?>" onclick="return confirm('Are you sure?');">Delete</a>
					<?php endif; ?>
				<?php elseif ($advanced == 2): ?>
					<a href="<?php echo url_for("system/flavorParams?pid=".$pid."&id=".$flavorParam->getId()."&delete=1&advanced=".$advanced); ?>" onclick="return confirm('Are you sure?');">Delete</a>
				<?php endif; ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>