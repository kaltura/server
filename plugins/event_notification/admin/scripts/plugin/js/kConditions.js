
var kConditions = {
	coreObjectValueChanged: {
		label:			'Core Object - Value Changed',
		subLabel:		'Select Object Type',
		variable:		'objectType',
		subSelections:	{
			entry:			{
				label:			'Entry',
				subLabel:		'Select Field',
				subSelections:	{
					'entryPeer::STATUS'					: {label: 'Status'},
					'entryPeer::KUSER_ID'				: {label: 'User'},
					'entryPeer::NAME'					: {label: 'Name'},
					'entryPeer::DESCRIPTION'			: {label: 'Description'},
					'entryPeer::DATA'					: {label: 'Content Version'},
					'entryPeer::THUMBNAIL'				: {label: 'Thumbnail Version'},
					'entryPeer::TOTAL_RANK'				: {label: 'Total Rank'},
					'entryPeer::RANK'					: {label: 'Rank'},
					'entryPeer::TAGS'					: {label: 'Tags'},
					'entryPeer::LENGTH_IN_MSECS'		: {label: 'Duration'},
					'entryPeer::PARTNER_DATA'			: {label: 'Partner Data'},
					'entryPeer::MODERATION_STATUS'		: {label: 'Moderation Status'},
					'entryPeer::MODERATION_COUNT'		: {label: 'Moderation Count'},
					'entryPeer::ACCESS_CONTROL_ID'		: {label: 'Access Control Profile'},
					'entryPeer::CONVERSION_PROFILE_ID'	: {label: 'Conversion Profile'},
					'entryPeer::CATEGORIES'				: {label: 'Categories'},
					'entryPeer::START_DATE'				: {label: 'Start Date'},
					'entryPeer::END_DATE'				: {label: 'End Date'},
					'entryPeer::FLAVOR_PARAMS_IDS'		: {label: 'Flavors List'}
				}
			},
			category:			{
				label:			'Category',
				subLabel:		'Select Field',
				subSelections:	{
					'categoryPeer::PARENT_ID'					: {label: 'Parent Category'},
					'categoryPeer::STATUS'						: {label: 'Status'},
					'categoryPeer::NAME'						: {label: 'Name'},
					'categoryPeer::DESCRIPTION'					: {label: 'Description'},
					'categoryPeer::ENTRIES_COUNT'				: {label: 'Entries Count'},
					'categoryPeer::DIRECT_ENTRIES_COUNT'		: {label: 'Direct Entries Count'},
					'categoryPeer::DIRECT_SUB_CATEGORIES_COUNT'	: {label: 'Direct Sub-Categories Count'},
					'categoryPeer::MEMBERS_COUNT'				: {label: 'Members Count'},
					'categoryPeer::PENDING_MEMBERS_COUNT'		: {label: 'Pending Members Count'},
					'categoryPeer::PENDING_ENTRIES_COUNT'		: {label: 'Pending Entries Count'},
					'categoryPeer::TAGS'						: {label: 'Tags'},
					'categoryPeer::PRIVACY'						: {label: 'Privacy'},
					'categoryPeer::INHERITANCE_TYPE'			: {label: 'Inheritance Type'},
					'categoryPeer::USER_JOIN_POLICY'			: {label: 'User Join Policy'},
					'categoryPeer::DEFAULT_PERMISSION_LEVEL'	: {label: 'Default Permission Level'},
					'categoryPeer::KUSER_ID'					: {label: 'User'},
					'categoryPeer::REFERENCE_ID'				: {label: 'Reference ID'},
					'categoryPeer::CONTRIBUTION_POLICY'			: {label: 'Contribution Policy'},
					'categoryPeer::PRIVACY_CONTEXT'				: {label: 'Privacy Context'},
					'categoryPeer::PRIVACY_CONTEXTS'			: {label: 'Privacy Contexts'},
					'categoryPeer::INHERITED_PARENT_ID'			: {label: 'Inherited Parent Category'},
					'categoryPeer::MODERATION'					: {label: 'Moderation'}
				}
			}
		},
		getCode:			function(subCode, variables){
								return '($scope->getEvent()->getObject() instanceof ' + variables.objectType + ') && in_array(' + variables.value + ', $scope->getEvent()->getObject()->getModifiedColumns())';
		}
	},
	coreObjectValueEqual: {
		label: 			'Core Object - Value Equal',
		variable:		'objectType',
		subSelections:	{
			entry:			{
				label:			'Entry',
				subLabel:		'Select Field',
				variable:		'getter',
				subSelections:	{
					'getStatus()'				: {
						label:			'Status',
						subLabel:		'Select Status',
						variable:		'getter',
						subSelections:	{
							'entryStatus::ERROR_IMPORTING'	: {label: 'Error Importing'},
							'entryStatus::ERROR_CONVERTING'	: {label: 'Error Converting'},
							'entryStatus::IMPORT'			: {label: 'Importing'},
							'entryStatus::PRECONVERT'		: {label: 'Converting'},
							'entryStatus::READY'			: {label: 'Ready'},
							'entryStatus::DELETED'			: {label: 'Deleted'},
							'entryStatus::PENDING'			: {label: 'Pending'},
							'entryStatus::NO_CONTENT'		: {label: 'No Content'}
						}
					},
					'getKuserID()'				: {label: 'User', fieldType: 'text'},
					'getName()'					: {label: 'Name', fieldType: 'text'},
					'getPartnerData()'			: {label: 'Partner Data', fieldType: 'text'},
					'getModerationStatus()'		: {
						label:			'Moderation Status',
						subLabel:		'Select Moderation Status',
						variable:		'getter',
						subSelections:	{
							'entry::ENTRY_MODERATION_STATUS_PENDING_MODERATION'	: {label: 'Pending'}, 
							'entry::ENTRY_MODERATION_STATUS_APPROVED'			: {label: 'Approved'},   
							'entry::ENTRY_MODERATION_STATUS_REJECTED'			: {label: 'Rejected'},   
							'entry::ENTRY_MODERATION_STATUS_FLAGGED_FOR_REVIEW'	: {label: 'Flagged for Review'},
							'entry::ENTRY_MODERATION_STATUS_AUTO_APPROVED'		: {label: 'Auto-Approved'}
						}
					},
					'getAccessControlId()'		: {label: 'Access Control Profile', fieldType: 'text'},
					'getConversionProfileId()'	: {label: 'Conversion Profile', fieldType: 'text'},
					'getFlavorParamsIds()'		: {label: 'Flavors List', fieldType: 'text'}
				},
				getCode:		function(subCode, variables){
									return variables.getter + ' == ' + variables.value;
				}
			},
			category:			{
				label:			'Category',
				subLabel:		'Select Field',
				variable:		'getter',
				subSelections:	{
					'getStatus()'					: {
						label:			'Status',
						subLabel:		'Select Status',
						variable:		'getter',
						subSelections:	{
							'CategoryStatus::UPDATING'	: {label: 'Updating'},
							'CategoryStatus::ACTIVE'	: {label: 'Active'},
							'CategoryStatus::DELETED'	: {label: 'Deleted'},
							'CategoryStatus::PURGED'	: {label: 'Purged'}
						}
					},
					'getName()'						: {label: 'Name', fieldType: 'text'},
					'getEntriesCount()'				: {label: 'Entries Count', fieldType: 'text'},
					'getDirectEntriesCount()'		: {label: 'Direct Entries Count', fieldType: 'text'},
					'getDirectSubCategoriesCount()'	: {label: 'Direct Sub-Categories Count', fieldType: 'text'},
					'getMembersCount()'				: {label: 'Members Count', fieldType: 'text'},
					'getPendingMembersCount()'		: {label: 'Pending Members Count', fieldType: 'text'},
					'getPendingEntriesCount()'		: {label: 'Pending Entries Count', fieldType: 'text'},
					'getPrivacy()'					: {label: 'Privacy', fieldType: 'text'},
					'getInheritanceType()'			: {
						label:			'Inheritance Type',
						subLabel:		'Select Inheritance Type',
						variable:		'getter',
						subSelections:	{
							'InheritanceType::INHERIT'	: {label: 'Inherit'},
							'InheritanceType::MANUAL'	: {label: 'Manual'}
						}
					},
					'getUserJoinPolicy()'			: {
						label:			'User Join Policy',
						subLabel:		'Select User Join Policy',
						variable:		'getter',
						subSelections:	{
							'UserJoinPolicyType::AUTO_JOIN'			: {label: 'Auto-Join'},
							'UserJoinPolicyType::REQUEST_TO_JOIN'	: {label: 'Request to Join'},
							'UserJoinPolicyType::NOT_ALLOWED'		: {label: 'Not Allowed'}
						}
					},
					'getDefaultPermissionLevel()'	: {
						label:			'Default Permission Level',
						subLabel:		'Select Permission Level',
						variable:		'getter',
						subSelections:	{
							'CategoryKuserPermissionLevel::MANAGER'		: {label: 'Manager'},
							'CategoryKuserPermissionLevel::MODERATOR'	: {label: 'Moderator'},
							'CategoryKuserPermissionLevel::CONTRIBUTOR'	: {label: 'Contributor'},
							'CategoryKuserPermissionLevel::MEMBER'		: {label: 'Member'}
						}
					},
					'getReferenceId()'				: {label: 'Reference ID', fieldType: 'text'},
					'getContributionPolicy()'		: {
						label:			'Contribution Policy',
						subLabel:		'Select Policy',
						variable:		'getter',
						subSelections:	{
							'ContributionPolicyType::ALL'									: {label: 'All'},
							'ContributionPolicyType::MEMBERS_WITH_CONTRIBUTION_PERMISSION'	: {label: 'Members with Contribution Permission'}
						}
					},
					'getPrivacyContext()'			: {label: 'Privacy Context', fieldType: 'text'},
					'getModeration()'				: {label: 'Moderation', fieldType: 'checkbox'}
				},
				getCode:		function(subCode, variables){
									return variables.getter + ' == ' + variables.value;
				}
			}
		},
		subLabel:		'Select Object Type',
		getCode:		function(subCode, variables){
							return '($scope->getEvent()->getObject() instanceof ' + variables.objectType + ') && ($scope->getEvent()->getObject()->' + subCode + ')';
		}
	}
};
