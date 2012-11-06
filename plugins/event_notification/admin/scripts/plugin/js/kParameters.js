
var kParameters = {
	coreObjectValueEqual: {
		label: 			'Core Object Value',
		variable:		'objectType',
		subSelections:	{
			entry:			{
				label:			'Entry',
				subLabel:		'Select Field',
				subSelections:	{
					'getStatus()'				: {label: 'Status'},
					'getKuserID()'				: {label: 'User'},
					'getName()'					: {label: 'Name'},
					'getPartnerData()'			: {label: 'Partner Data'},
					'getModerationStatus()'		: {label: 'Moderation Status'},
					'getAccessControlId()'		: {label: 'Access Control Profile'},
					'getConversionProfileId()'	: {label: 'Conversion Profile'},
					'getFlavorParamsIds()'		: {label: 'Flavors List'}
				}
			},
			category:			{
				label:			'Category',
				subLabel:		'Select Field',
				subSelections:	{
					'getStatus()'					: {label: 'Status'},
					'getName()'						: {label: 'Name'},
					'getEntriesCount()'				: {label: 'Entries Count'},
					'getDirectEntriesCount()'		: {label: 'Direct Entries Count'},
					'getDirectSubCategoriesCount()'	: {label: 'Direct Sub-Categories Count'},
					'getMembersCount()'				: {label: 'Members Count'},
					'getPendingMembersCount()'		: {label: 'Pending Members Count'},
					'getPendingEntriesCount()'		: {label: 'Pending Entries Count'},
					'getPrivacy()'					: {label: 'Privacy'},
					'getInheritanceType()'			: {label: 'Inheritance Type'},
					'getUserJoinPolicy()'			: {label: 'User Join Policy'},
					'getDefaultPermissionLevel()'	: {label: 'Default Permission Level'},
					'getReferenceId()'				: {label: 'Reference ID'},
					'getContributionPolicy()'		: {label: 'Contribution Policy'},
					'getPrivacyContext()'			: {label: 'Privacy Context'},
					'getModeration()'				: {label: 'Moderation'}
				}
			}
		},
		subLabel:		'Select Object Type',
		getCode:		function(subCode, variables){
							return '(($scope->getEvent()->getObject() instanceof ' + variables.objectType + ') ? $scope->getEvent()->getObject()->' + variables.value + ' : null)';
		}
	}
};
