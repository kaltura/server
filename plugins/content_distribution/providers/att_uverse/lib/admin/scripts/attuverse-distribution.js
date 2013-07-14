(function(){
	function toggleXsltTextarea(e) {
		if (jQuery(e.target).is(':checked'))
			jQuery(e.target).parent().nextAll('dd').find('textarea').first().show();
		else
			jQuery(e.target).parent().nextAll('dd').find('textarea').first().hide();	
	}
	
	function clearIfNotEnabled(selector) {
		if (!jQuery(selector).is(':checked')){
			jQuery(selector).parent().nextAll('dd').find('textarea').first().val('');
		}
	}
	
	jQuery('#enable_flavor_asset_filename').change(toggleXsltTextarea);
	jQuery('#enable_thumbnail_asset_filename').change(toggleXsltTextarea);
	jQuery('#enable_asset_filename').change(toggleXsltTextarea);
	
	jQuery(function() {
		
		if (jQuery('#flavor_asset_filename_xslt').val())
			jQuery('#enable_flavor_asset_filename').attr('checked', true).change();
		
		if (jQuery('#thumbnail_asset_filename_xslt').val())
			jQuery('#enable_thumbnail_asset_filename').attr('checked', true).change();

		if (jQuery('#asset_filename_xslt').val())
			jQuery('#enable_asset_filename').attr('checked', true).change();
		
		jQuery('#frmDistributionProfileConfig').submit(function() {
			clearIfNotEnabled('#enable_flavor_asset_filename');
			clearIfNotEnabled('#enable_thumbnail_asset_filename');
			clearIfNotEnabled('#enable_asset_filename');
		});

	});

})();