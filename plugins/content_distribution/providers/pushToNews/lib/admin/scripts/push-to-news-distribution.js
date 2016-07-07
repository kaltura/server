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

    function httpsMode() {
        // do nothing
    }

    function hideFormDtDd(elementId) {
        jQuery('#frmDistributionProfileConfig').find('#'+elementId).hide();
        jQuery('#frmDistributionProfileConfig').find('#'+elementId+'-element').hide();
        jQuery('#frmDistributionProfileConfig').find('#'+elementId+'-label').hide();
    }

    function showFormDtDd(elementId) {
        jQuery('#frmDistributionProfileConfig').find('#'+elementId).show();
        jQuery('#frmDistributionProfileConfig').find('#'+elementId+'-element').show();
        jQuery('#frmDistributionProfileConfig').find('#'+elementId+'-label').show();
    }
		
	jQuery('#enable_id').change(toggleXsltTextarea);
	jQuery('#enable_publishdat').change(toggleXsltTextarea);
	jQuery('#enable_creationat').change(toggleXsltTextarea);
	jQuery('#enable_titlelanguagedat').change(toggleXsltTextarea);
	jQuery('#enable_title').change(toggleXsltTextarea);
	jQuery('#enable_mimetype').change(toggleXsltTextarea);
	jQuery('#enable_language').change(toggleXsltTextarea);
	jQuery('#enable_body').change(toggleXsltTextarea);
	jQuery('#enable_author_name').change(toggleXsltTextarea);
	jQuery('#enable_author_email').change(toggleXsltTextarea);
	jQuery('#enable_rightsinfo_copyrightholder').change(toggleXsltTextarea);
	jQuery('#enable_rightsinfo_name').change(toggleXsltTextarea);
	jQuery('#enable_rightsinfo_copyrightnotice').change(toggleXsltTextarea);
	jQuery('#enable_productcode').change(toggleXsltTextarea);
	jQuery('#enable_attribution').change(toggleXsltTextarea);
	jQuery('#enable_metadata_organizations').change(toggleXsltTextarea);
	jQuery('#enable_metadata_subjects').change(toggleXsltTextarea);

	
	jQuery(function() {
		if (jQuery('#id_xslt').val())
			jQuery('#enable_id').attr('checked', true).change();
	
		if (jQuery('#publishdat_xslt').val())
			jQuery('#enable_publishdat').attr('checked', true).change();
		
		if (jQuery('#creationat_xslt').val())
			jQuery('#enable_creationat').attr('checked', true).change();

		if (jQuery('#titlelanguagedat_xslt').val())
			jQuery('#enable_titlelanguagedat').attr('checked', true).change();
	
		if (jQuery('#title_xslt').val())
			jQuery('#enable_title').attr('checked', true).change();
			
		if (jQuery('#mimetype_xslt').val())
			jQuery('#enable_mimetype').attr('checked', true).change();	
		
		if (jQuery('#language_xslt').val())
			jQuery('#enable_language').attr('checked', true).change();
			
		if (jQuery('#body_xslt').val())
			jQuery('#enable_body').attr('checked', true).change();	
			
		if (jQuery('#author_name_xslt').val())
			jQuery('#enable_author_name').attr('checked', true).change();
			
		if (jQuery('#author_email_xslt').val())
			jQuery('#enable_author_email').attr('checked', true).change();
		
		if (jQuery('#rightsinfo_copyrightholder_xslt').val())
			jQuery('#enable_rightsinfo_copyrightholder').attr('checked', true).change();
		
		if (jQuery('#rightsinfo_name_xslt').val())
			jQuery('#enable_rightsinfo_name').attr('checked', true).change();
		
		if (jQuery('#rightsinfo_copyrightnotice_xslt').val())
			jQuery('#enable_rightsinfo_copyrightnotice').attr('checked', true).change();
			
		if (jQuery('#productcode_xslt').val())
			jQuery('#enable_productcode').attr('checked', true).change();
			
		if (jQuery('#attribution_xslt').val())
			jQuery('#enable_attribution').attr('checked', true).change();
			
		if (jQuery('#metadata_organizations_xslt').val())
			jQuery('#enable_metadata_organizations').attr('checked', true).change();

		if (jQuery('#metadata_subjects_xslt').val())
			jQuery('#enable_metadata_subjects').attr('checked', true).change();
		
	});

	jQuery('#frmDistributionProfileConfig').submit(function() {
		clearIfNotEnabled('#enable_id');
		clearIfNotEnabled('#enable_publishdat');
		clearIfNotEnabled('#enable_creationat');
		clearIfNotEnabled('#enable_titlelanguagedat');
		clearIfNotEnabled('#enable_title');
		clearIfNotEnabled('#enable_mimetype');
		clearIfNotEnabled('#enable_language');
		clearIfNotEnabled('#enable_body');
		clearIfNotEnabled('#enable_author_name');
		clearIfNotEnabled('#enable_author_email');
		clearIfNotEnabled('#enable_rightsinfo_copyrightholder');
		clearIfNotEnabled('#enable_rightsinfo_name');
		clearIfNotEnabled('#enable_rightsinfo_copyrightnotice');
		clearIfNotEnabled('#enable_productcode');
		clearIfNotEnabled('#enable_attribution');
		clearIfNotEnabled('#enable_metadata_organizations');
		clearIfNotEnabled('#enable_metadata_subjects');
	});

        jQuery('#protocol').change(function() {
           switch(Number(jQuery(this).val())) {
          	 case 5: // https
                   httpsMode();
                   break;
           }
       });
        jQuery('#protocol').change();
})();