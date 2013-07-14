(function($) {
	$.distributionMetadataDynamicFields = function(options) {
		var base = this;
		var _metadataConfigRows = null;
		
		base.init = function() {
			base.options = $.extend({}, $.distributionMetadataDynamicFields.defaultOptions, options);
			_createNewButton();
		};

		base.init();
		
		function _createNewButton() {
			var addMetadataButtonHtml = '';
			addMetadataButtonHtml += '<div class="field-config-row metadata-config-add-button">';
			addMetadataButtonHtml += '	<div class="field-config-head">';
			addMetadataButtonHtml += '		<a href="#">+ '+base.options.buttonText+' </a>';
			addMetadataButtonHtml += '	</div>';
			addMetadataButtonHtml += '</div>';
			var $addMetadataButtonHtml = $(addMetadataButtonHtml);
			$addMetadataButtonHtml.find('a').click(_onAddMetadataButtonClick);
			$('#frmDistributionProfileConfig #fieldset-fieldConfigArray').append($addMetadataButtonHtml);
			
			var $metadataConfigRows = _getMetadataConfigRows();
			$metadataConfigRows.hide();
		};
		
		function _getMetadataConfigRows() {
			if (!_metadataConfigRows) {
				_metadataConfigRows = $([]);
				$(base.options.fields).each(function(i, field) {
					var $metadataConfigRow = $('#frmDistributionProfileConfig input[value='+field+']').parent().parent();
					if ($metadataConfigRow.size() == 0)
						return alert('Field "'+field+'" not found, make sure a placeholder for this field is add in server side\'s getDefaultFieldConfigArray()');
					
					_metadataConfigRows.push($metadataConfigRow[0]);
				});
			}
			return _metadataConfigRows;
		}
		
		function _onAddMetadataButtonClick()
		{
			$metadataConfigRows = _getMetadataConfigRows();
			$metadataConfigRows.each(function(i, obj){
				var $new = $(obj).clone(true);
				$('.metadata-config-add-button:first').before($new.show());
			});
			
			_reorganizeMetadataFields();
			return false;
		};
		
		function _reorganizeMetadataFields()
		{
			$(base.options.fields).each(function(i, field){
				var index = 1;
				$('.field-config-row').each(function() {
					var name = $(this).find('input[type=hidden]:first').attr('name');
					var currentFieldConfigRow = this;
					
					if (!$(currentFieldConfigRow).is(':visible'))
						return;
					
					if (!name)
						return;
					
					var fieldRegex = field.replace('_N_', '_[N0-9]*_');
					var fieldIndexPath = '_'+index+'_';
					var fieldWithIndex = field.replace('_N_', fieldIndexPath);
					fieldRegex = new RegExp(fieldRegex);
					if (fieldRegex.test(name)) 
					{
						$(currentFieldConfigRow).find('input').each(function(i, obj) {
							var name = $(obj).attr('name');
							$(obj).attr('name', name.replace(/_[N0-9]*_/, fieldIndexPath));
							
							var name = $(obj).attr('id');
							$(obj).attr('id', name.replace(/_[N0-9]*_/, fieldIndexPath));
							
							var value = $(obj).val();
							$(obj).val(value.replace(/_[N0-9]*_/, fieldIndexPath));
						});
						index++;
					}
				});
			});
		};
	};

	$.distributionMetadataDynamicFields.defaultOptions = {
		sections: [],
		buttonText: ''
	};

})(jQuery);