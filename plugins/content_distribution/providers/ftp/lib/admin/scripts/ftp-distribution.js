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

    function sftpMode() {
        showFormDtDd('passphrase');
        showFormDtDd('sftp_public_key_readonly');
        showFormDtDd('sftp_private_key_readonly');
        showFormDtDd('sftp_public_key');
        showFormDtDd('sftp_private_key');
        hideFormDtDd('aspera_public_key_readonly');
        hideFormDtDd('aspera_private_key_readonly');
        hideFormDtDd('aspera_public_key');
        hideFormDtDd('aspera_private_key');
    }
    
    function asperaMode() {
    	hideFormDtDd('sftp_public_key_readonly');
        hideFormDtDd('sftp_private_key_readonly');
        hideFormDtDd('sftp_public_key');
        hideFormDtDd('sftp_private_key');
        showFormDtDd('passphrase');
        showFormDtDd('aspera_public_key_readonly');
        showFormDtDd('aspera_private_key_readonly');
        showFormDtDd('aspera_public_key');
        showFormDtDd('aspera_private_key');
    }

    function sftpSecLibMode() {
    	hideFormDtDd('passphrase');
        showFormDtDd('sftp_public_key_readonly');
        showFormDtDd('sftp_private_key_readonly');
        hideFormDtDd('sftp_public_key');
        hideFormDtDd('aspera_public_key_readonly');
        hideFormDtDd('aspera_private_key_readonly');
        hideFormDtDd('aspera_public_key');
        hideFormDtDd('aspera_private_key');
    }

    function ftpMode() {
        hideFormDtDd('passphrase');
        hideFormDtDd('sftp_public_key_readonly');
        hideFormDtDd('sftp_private_key_readonly');
        hideFormDtDd('sftp_public_key');
        hideFormDtDd('sftp_private_key');
        hideFormDtDd('aspera_public_key_readonly');
        hideFormDtDd('aspera_private_key_readonly');
        hideFormDtDd('aspera_public_key');
        hideFormDtDd('aspera_private_key');
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
	
	jQuery('#enable_metadata_xslt').change(toggleXsltTextarea);
	jQuery('#enable_metadata_filename').change(toggleXsltTextarea);
	jQuery('#enable_flavor_asset_filename').change(toggleXsltTextarea);
	jQuery('#enable_thumbnail_asset_filename').change(toggleXsltTextarea);
	jQuery('#enable_asset_filename').change(toggleXsltTextarea);
	
	jQuery(function() {
		if (jQuery('#metadata_xslt').val())
			jQuery('#enable_metadata_xslt').attr('checked', true).change();
		
		if (jQuery('#metadata_filename_xslt').val())
			jQuery('#enable_metadata_filename').attr('checked', true).change();
		
		if (jQuery('#flavor_asset_filename_xslt').val())
			jQuery('#enable_flavor_asset_filename').attr('checked', true).change();
		
		if (jQuery('#thumbnail_asset_filename_xslt').val())
			jQuery('#enable_thumbnail_asset_filename').attr('checked', true).change();

		if (jQuery('#asset_filename_xslt').val())
			jQuery('#enable_asset_filename').attr('checked', true).change();
		
		jQuery('#frmDistributionProfileConfig').submit(function() {
			clearIfNotEnabled('#enable_metadata_xslt');
			clearIfNotEnabled('#enable_metadata_filename');
			clearIfNotEnabled('#enable_flavor_asset_filename');
			clearIfNotEnabled('#enable_thumbnail_asset_filename');
			clearIfNotEnabled('#enable_asset_filename');
		});

        jQuery('#protocol').change(function() {
           switch(Number(jQuery(this).val())) {
               case 1: // ftp
                   ftpMode();
                   break;
               case 3: // sftp
                   sftpMode();
                   break;
               case 8: // sftp sec lib
            	   sftpMode();
                   break;
               case 9: // sftp sec lib
            	   sftpSecLibMode();
                   break;
               case 10: // aspera
            	   asperaMode();
                   break;
           }
        });
        jQuery('#protocol').change();
	});
	
	jQuery('#disable_metadata').change(function() {
		if (jQuery(this).is(':checked')) {
			jQuery('#enable_metadata_xslt').attr('checked', false).attr('disabled', true).change();
		}
		else {
			jQuery('#enable_metadata_xslt').attr('disabled', false);
		}
	});
	jQuery('#disable_metadata').change();
})();