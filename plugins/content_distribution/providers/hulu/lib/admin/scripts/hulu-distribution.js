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
    	showFormDtDd('sftp_host');
        showFormDtDd('sftp_login');
        showFormDtDd('sftp_pass');
        hideFormDtDd('aspera_public_key_readonly');
        hideFormDtDd('aspera_private_key_readonly');
        hideFormDtDd('aspera_public_key');
        hideFormDtDd('aspera_private_key');
        hideFormDtDd('passphrase');
        hideFormDtDd('aspera_host');
        hideFormDtDd('aspera_login');
        hideFormDtDd('aspera_pass');
    }
    
    function asperaMode() {
    	showFormDtDd('aspera_host');
        showFormDtDd('aspera_login');
        showFormDtDd('aspera_pass');
        showFormDtDd('passphrase');
        showFormDtDd('aspera_public_key_readonly');
        showFormDtDd('aspera_private_key_readonly');
        showFormDtDd('aspera_public_key');
        showFormDtDd('aspera_private_key');
        hideFormDtDd('sftp_login');
        hideFormDtDd('sftp_host');
        hideFormDtDd('sftp_pass');
        
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
	
	
	
	jQuery(function() {

        jQuery('#protocol').change(function() {
           switch(Number(jQuery(this).val())) {
               case 8: // sftp sec lib
            	   sftpMode();
                   break;
               case 10: // aspera
            	   asperaMode();
                   break;
           }
        });
        jQuery('#protocol').change();
	});
})();