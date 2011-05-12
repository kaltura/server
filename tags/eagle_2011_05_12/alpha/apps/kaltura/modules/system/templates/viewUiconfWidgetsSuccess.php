<script type="text/javascript">
jQuery(function() {
	jQuery("#media_pager li").click(function() {
		page = jQuery(this).val();
		currentPage = Number(jQuery(this).parent().find("ul[@class=active]").text());

		if (jQuery(this).attr("class").indexOf("disabled") > -1)
			return;
		
		if (page) {
			window.location.href = "<?php echo url_for("system/viewUiconfWidgets?partnerId=".$partnerId."&uiConfId=".$uiConfId); ?>?page=" + page;
		}
	});
});
</script>

<div class="mykaltura_viewAll mykaltura_media" style="width: 80%;">
	<div>
		
	</div>
	<div class="content">
		<div class="top">
			<div class="clearfix" style="margin:10px 0;">
				<form action="<?php echo url_for("system/viewUiconfWidgets"); ?>" method="get">
					<?php if ($partner): ?>
						<b><?php echo $partner->getName(); ?> (<?php echo $partner->getId(); ?>)</b>
						<input type="submit" value="Change" />
						<?php if ($uiConfId): ?>
							* Displaying Widgets for UI Conf Id <b><?php echo $uiConfId; ?></b>
						<?php endif; ?>
					<?php else: ?>
						Partner ID: <input type="text" name="partnerId" value="<?php echo $partnerId; ?>" size="6" />
						<input type="submit" value="Go" />
					<?php endif; ?>
				</form>
				<ul class="pager" id="media_pager" style="float:right; margin:0;">
					<?php echo mySmartPagerRenderer::createHtmlPager($lastPage, $page); ?>
				</ul>
			</div>
		</div><!-- end top-->
		<div class="middle">	
				<table cellspacing="0" cellpadding="0" style="table-layout: auto;">
					<thead>
						<tr>
							<td class="type" style="width: 40px;" ><span>Id</span></td>
							<td class="type" ><span>Partner Id</span></td>
							<td class="type" ><span>UI Conf Id</span></td>
							<td class="type" ><span>Entry Id</span></td>
							<td class="type" ><span>Root Id</span></td>
							<td class="type" ><span>Source Id</span></td>
							<td class="type" ><span>Security Type</span></td>
							<td class="type" ><span>Security Policy</span></td>
							<td class="type" ><span>Custom Data</span></td>
							<td class="type" ><span>Partner Data</span></td>
						</tr>
					</thead>
					<tbody id="media_content">
						<?php $even_row = false; ?>
						<?php foreach($widgets as $widget): ?>
						<tr <?php $even_row ? 'class="even"' : ''; ?>>
							<td><?php echo $widget->getId(); ?></td>
							<td><?php echo $widget->getPartnerId(); ?></td>
							<td><a href="<?php echo url_for("system/editUiconf?id=".$widget->getUiConfId()); ?>"><?php echo $widget->getUiConfId(); ?></a></td>
							<td><?php echo $widget->getEntryId(); ?></td>
							<td><?php echo $widget->getRootWidgetId(); ?></td>
							<td><?php echo $widget->getSourceWidgetId(); ?></td>
							<td><?php echo $widget->getSecurityType(); ?></td>
							<td><?php echo $widget->getSecurityPolicy(); ?></td>
							<td><?php echo $widget->getCustomData(); ?></td>
							<td><?php echo $widget->getPartnerData(); ?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
		</div><!-- end middle-->
	</div><!-- end content-->
	<div class="bgB"></div>
</div><!-- end media-->
