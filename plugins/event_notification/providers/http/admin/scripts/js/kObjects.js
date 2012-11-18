
var kObjects = {
	coreObjectValueEqual: {
		label: 			'Event Object',
		subSelections:	{
			entry:			{label: 'Entry'},
			category:		{label:	'Category'},
			kuser:			{label:	'User'}
		},
		subLabel:		'Select Object Type',
		getCode:		function(subCode, variables){
							return '(($scope->getEvent()->getObject() instanceof ' + variables.value + ') ? $scope->getEvent()->getObject() : null)';
		}
	}
};
