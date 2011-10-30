Ext.Loader.setConfig({
	enabled : true
});

Ext.require([ '*', 'Ext.tree.*', 'Ext.data.*', 'Ext.grid.*', 'Ext.tip.*',
		'Ext.tab.Panel', 'Ext.selection.*', 'Ext.menu.*' ]);

var KCodeExample = function(container, codeGenerator){

	panel = Ext.create('Ext.panel.Panel', {
		title : codeGenerator.getTitle(),
		anchor : '100% 100%',
		closable : false,
		autoScroll : true,
		bodyPadding : 4,
		html: '<div id="dv-example-' + codeGenerator.getLanguage() + '"/>',
		listeners: {
			show: function(component, eOpts){
				codeGenerator.setEntity(jQuery('#dv-example-' + codeGenerator.getLanguage()));
				kTestMe.initCodeExample(codeGenerator);
			}
		}
	});
	container.add(panel);
	
	if(!container.addedListener){
		container.addedListener = true;
		panel.addListener('afterlayout', function(){
			codeGenerator.setEntity(jQuery('#dv-example-' + codeGenerator.getLanguage()));
			kTestMe.initCodeExample(codeGenerator);
		}, this, {
			single : true,
			delay: 3000
		});
	}
};

var KDoc = {
	testmeResults : null,
	testmePropertiesPanel : null,
	openTab : null,
	
	jsonToStore : function(json, data) {

		if (!data) {
			data = {
				objectName : 'results',
				objectType : 'array',
				expanded : true,
				children : []
			};
		}

		if (typeof (json) == 'object') {
			for ( var attr in json) {
				if (attr == 'length')
					continue;

				if (attr == 'objectType') {
					data.objectType = json[attr];
					continue;
				}

				var obj = {
					objectName : attr,
					objectType : 'array',
					expanded : true,
					children : []
				};
				obj = KDoc.jsonToStore(json[attr], obj);
				data.children.push(obj);
			}
		} else {
			data.objectType = typeof (json);
			data.objectValue = json;
			data.leaf = true;
		}

		return data;
	},

	onTestmeResults : function(service, action, json) {
		var data = KDoc.jsonToStore(json);

		var store = Ext.create('Ext.data.TreeStore', {
			fields : [ {
				name : 'objectName',
				type : 'string'
			}, {
				name : 'objectType',
				type : 'string'
			}, {
				name : 'objectValue',
				type : 'string'
			} ],
			root : {
				objectName : '.',
				children : data
			}
		});

		var testmeResultsSelector = {
			selectedText : null,
			selector : Ext.create('Ext.selection.CellModel', {
				mode : 'SIMPLE',
				allowDeselect : true,
				listeners : {
					select : function(selectionModel, record, row, column, eOpts) {
						testmeResultsSelector.selectedText = null;
						if(!record)
							return;
						
						var treeColumn = testmeResultsTab.columns[column];
						testmeResultsSelector.selectedText = record.data[treeColumn.dataIndex];
					}
				}
			})
		};
		
		var testmeResultsTab = Ext.create('Ext.tree.Panel', {
			title : service + '::' + action,
			closable : true,
			region : 'center',
			rootVisible : false,
			autoScroll : true,
			store : store,
			selModel : testmeResultsSelector.selector,
			columns : [ {
				xtype : 'treecolumn',
				text : 'Name',
				flex : 2,
				sortable : true,
				dataIndex : 'objectName'
			}, {
				text : 'Value',
				flex : 1,
				sortable : false,
				dataIndex : 'objectValue',
				align : 'left'
			}, {
				text : 'Type',
				flex : 1,
				dataIndex : 'objectType',
				sortable : false
			} ]
		});

		var testmeResultsContextMenu = new Ext.menu.Menu({
			items : [ {
				text : 'Copy',
				iconCls : 'copy',
				handler : function() {
			        if (window.clipboardData) { // Internet Explorer
						window.clipboardData.setData('Text', testmeResultsSelector.selectedText);
					} else if (window.netscape) { // Mozilla
						try {
							netscape.security.PrivilegeManager.enablePrivilege('UniversalXPConnect');

							var gClipboardHelper = Components.classes["@mozilla.org/widget/clipboardhelper;1"]
									.getService(Components.interfaces.nsIClipboardHelper);

							gClipboardHelper.copyString(testmeResultsSelector.selectedText);
						} catch (e) {
							alert(e + '\n\nPlease type: "about:config" in your address bar.\nThen filter by "signed".\nChange the value of "signed.applets.codebase_principal_support" to true.\nYou should then be able to use this feature.');
						}
					} else {
						alert("Your browser does not support this feature");
					}
				}
			} ]
		});

		testmeResultsTab.on('itemcontextmenu', function(view, record, item, index, e, eOpts) {
			e.preventDefault();
			testmeResultsContextMenu.setPosition(e.xy[0], e.xy[1]);
			testmeResultsContextMenu.show();
		});

		testmeResultsTab.on('sortchange', function(container, column, direction, eOpts) {
			testmeResultsTab.doComponentLayout();
		});
		
		var tab = KDoc.testmeResults.add(testmeResultsTab);
		tab.show();
	},

	toggleItem : function(className) {
		jQuery('.' + className).toggle();
	},

	openObject : function(type) {

		var url = 'testmeDoc/page.php?object=' + type;
		var title = type;
		var iconCls = 'icon-object';
		var toolTip = type;
		KDoc.openTab(url, title, iconCls, toolTip);
	},

	openAction : function(serviceId, serviceName, actionId) {

		var url = 'testmeDoc/page.php?service=' + serviceId + '&action=' + actionId;
		var title = serviceName + '::' + actionId;
		var iconCls = 'icon-action';
		var toolTip = title;
		KDoc.openTab(url, title, iconCls, toolTip);
	},

	utf8_encode: function (argString) {
	    // Encodes an ISO-8859-1 string to UTF-8  
	    // 
	    // version: 1109.2015
	    // discuss at: http://phpjs.org/functions/utf8_encode
	    // +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
	    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	    // +   improved by: sowberry
	    // +    tweaked by: Jack
	    // +   bugfixed by: Onno Marsman
	    // +   improved by: Yves Sucaet
	    // +   bugfixed by: Onno Marsman
	    // +   bugfixed by: Ulrich
	    // +   bugfixed by: Rafal Kukawski
	    // *     example 1: utf8_encode('Kevin van Zonneveld');
	    // *     returns 1: 'Kevin van Zonneveld'
	    if (argString === null || typeof argString === "undefined") {
	        return "";
	    }
	 
	    var string = (argString + ''); // .replace(/\r\n/g, "\n").replace(/\r/g, "\n");
	    var utftext = "",
	        start, end, stringl = 0;
	 
	    start = end = 0;
	    stringl = string.length;
	    for (var n = 0; n < stringl; n++) {
	        var c1 = string.charCodeAt(n);
	        var enc = null;
	 
	        if (c1 < 128) {
	            end++;
	        } else if (c1 > 127 && c1 < 2048) {
	            enc = String.fromCharCode((c1 >> 6) | 192) + String.fromCharCode((c1 & 63) | 128);
	        } else {
	            enc = String.fromCharCode((c1 >> 12) | 224) + String.fromCharCode(((c1 >> 6) & 63) | 128) + String.fromCharCode((c1 & 63) | 128);
	        }
	        if (enc !== null) {
	            if (end > start) {
	                utftext += string.slice(start, end);
	            }
	            utftext += enc;
	            start = end = n + 1;
	        }
	    }
	 
	    if (end > start) {
	        utftext += string.slice(start, stringl);
	    }
	 
	    return utftext;
	},
	
	base64_encode: function (data) {
	    // Encodes string using MIME base64 algorithm  
	    // 
	    // version: 1109.2015
	    // discuss at: http://phpjs.org/functions/base64_encode
	    // +   original by: Tyler Akins (http://rumkin.com)
	    // +   improved by: Bayron Guevara
	    // +   improved by: Thunder.m
	    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	    // +   bugfixed by: Pellentesque Malesuada
	    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	    // +   improved by: Rafał Kukawski (http://kukawski.pl)
	    // -    depends on: utf8_encode
	    // *     example 1: base64_encode('Kevin van Zonneveld');
	    // *     returns 1: 'S2V2aW4gdmFuIFpvbm5ldmVsZA=='
	    // mozilla has this native
	    // - but breaks in 2.0.0.12!
	    //if (typeof this.window['atob'] == 'function') {
	    //    return atob(data);
	    //}
	    var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
	    var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
	        ac = 0,
	        enc = "",
	        tmp_arr = [];
	 
	    if (!data) {
	        return data;
	    }
	 
	    data = KDoc.utf8_encode(data + '');
	 
	    do { // pack three octets into four hexets
	        o1 = data.charCodeAt(i++);
	        o2 = data.charCodeAt(i++);
	        o3 = data.charCodeAt(i++);
	 
	        bits = o1 << 16 | o2 << 8 | o3;
	 
	        h1 = bits >> 18 & 0x3f;
	        h2 = bits >> 12 & 0x3f;
	        h3 = bits >> 6 & 0x3f;
	        h4 = bits & 0x3f;
	 
	        // use hexets to index into b64, and append result to encoded string
	        tmp_arr[ac++] = b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4);
	    } while (i < data.length);
	 
	    enc = tmp_arr.join('');
	    
	    var r = data.length % 3;
	    
	    return (r ? enc.slice(0, r - 3) : enc) + '==='.slice(r || 3);
	},

	loadClientLibs : function() {
		var clientLibsPanel = Ext.create('Ext.panel.Panel', {
			title: 'Clinet Libraries',
			closable : true,
			autoScroll : true,
			bodyPadding : 8,
			bodyCls: 'api-doc',
			loader : {
				url : 'client-libs.php',
				renderer : 'html',
				autoLoad : true,
				scripts : true
			}
		});
		
		return clientLibsPanel;
	},

	loadTestConsole : function() {

		var testmeForm = Ext.create('Ext.panel.Panel', {
			region : 'center',
			autoScroll : true,
			border : false,
			bodyPadding : 8,
			collapsible : false,
			loader : {
				url : 'testme/form.php',
				renderer : 'html',
				autoLoad : true,
				listeners : {
					load : function() {
						kTestMe = new KTestMe();
					}
				}
			}
		});

		KDoc.testmePropertiesPanel = Ext.create('Ext.panel.Panel', {
			region : 'east',
			split : true,
			collapsible : true,
			collapsed : true,
			preventHeader: true,
			width: 270,
			resizable: false,
			layout : 'accordion'
		});

		KDoc.testmeResults = Ext.create('Ext.tab.Panel', {
			region : 'center',
			autoScroll : false,
			collapsible : false,
	        plugins: [Ext.create('Ext.ux.TabCloseMenu'), Ext.create('Ext.ux.TabReorderer')]
		});

		var codeExamplePanel = Ext.create('Ext.tab.Panel', {
			title : 'Code Example',
			region : 'south',
			autoScroll : true,
			height : 200,
			collapsible : true,
			collapsed : true
		});
		new KCodeExample(codeExamplePanel, new KCodeExamplePHP());
		new KCodeExample(codeExamplePanel, new KCodeExampleJava());
		new KCodeExample(codeExamplePanel, new KCodeExampleCsharp());
		new KCodeExample(codeExamplePanel, new KCodeExamplePython());
		new KCodeExample(codeExamplePanel, new KCodeExampleJavascript());

		var testConsole = Ext.create('Ext.Panel', {
			title: 'Test Console',
			layout : 'border',
			items : [Ext.create('Ext.Panel', {
				region : 'west',
				layout : 'border',
				collapsed : false,
				collapsible : true,
				split : true,
				minWidth: 290,
				width: 540,
				maxWidth: 550,
				items : [testmeForm, KDoc.testmePropertiesPanel]
			}),KDoc.testmeResults, codeExamplePanel]
		});
		
		return testConsole;
	},

	loadReferenceDocuments : function() {

		var apiDocumentsStore = Ext.create('Ext.data.TreeStore', {
			proxy : {
				type : 'ajax',
				url : 'testmeDoc/doc.js.php'
			},
			root : {
				id : 'main',
				expanded : false
			},
			folderSort : true,
			sorters : [ {
				property : 'text',
				direction : 'ASC'
			} ]
		});

		var xsdDocumentsStore = Ext.create('Ext.data.TreeStore', {
			proxy : {
				type : 'ajax',
				url : 'xsdDoc/doc.js.php'
			},
			root : {
				id : 'main',
				expanded : false
			},
			folderSort : true,
			sorters : [ {
				property : 'text',
				direction : 'ASC'
			} ]
		});

		Ext.define('Kaltura.tree.View', {
		    extend: 'Ext.tree.View',
		    alias: 'widget.kalturaview',
		    
		    expand: function(record, deep, callback, scope) {

		    	if(!callback)
		    		callback = function(){
			    		apiDocuments.filterNode(record);
			    	};
		    	
		    	return record.expand(deep, callback, scope);
		    } 
		});
		
		var apiDocuments = Ext.create('Ext.tree.Panel', {
			flex: 1,
			viewType: 'kalturaview',
			store : apiDocumentsStore,
			rootVisible : false,
			autoScroll : true,
			collapsible : false,
			listeners: {
				itemclick: function(view, record, item, index, e, eOpts) {
					if (!record.data.leaf && record.data.parentId != 'services')
						return;
	
					var url = 'testmeDoc/page.php?' + record.data.id;
					var title = (record.data.qtip ? record.data.qtip : record.data.text);
					var iconCls = record.data.iconCls;
					var toolTip = record.data.text;
					KDoc.openTab(url, title, iconCls, toolTip);
				},
				beforeload: function(store, operation, eOpts){
					apiDocuments.setLoading(true);
				},
				load: function(store, operation, eOpts){
					apiDocuments.setLoading(false);
				}
			},
			searchFilter : null,
			matchesFilter : function(text){
				return (!apiDocuments.searchFilter || text.toLowerCase().indexOf(apiDocuments.searchFilter) >= 0);
			},
			filterNode : function(node){

				if(node.parentNode){
					var parentId = node.parentNode.internalId;
					if(parentId == 'services'
							|| parentId == 'objects'
								|| parentId == 'filters'
									|| parentId == 'arrays'
										|| parentId == 'enums'
						){
						
						var element = apiDocuments.getView().getNode(node);
						if(element){
							var $jq = $(element);
							if(apiDocuments.matchesFilter(node.data.text)){
								$jq.show();
							}
							else{
								$jq.hide();
							}
						}
					}
				}
				
				node.eachChild(apiDocuments.filterNode);
			},
			applyFilter : function(value){
				
				apiDocuments.searchFilter = null;
				if(value.length)
					apiDocuments.searchFilter = value.toLowerCase();

				apiDocuments.filterNode(apiDocuments.getRootNode());
			}
		});
		
		var apiDocumentsForm = Ext.create('Ext.form.Panel', {
			bodyPadding: 8,
	        frame:false,
	        defaultType: 'textfield',
	        defaults: {
	            anchor: '100%'
	        },
	        items: [{
	            fieldLabel: 'Search',
		        enableKeyEvents: true,
		        listeners: {
		        	keyup: {
		        		buffer : 800,
		        		fn: function(textField, e, eOpts){
		        			apiDocuments.applyFilter(textField.getValue());
		        		}
		        	}
		        }
	        }]
		});

		var apiDocumentsPanel = Ext.create('Ext.Panel', {
			title : 'API',
			iconCls : 'nav',
            layout: {
                type:'vbox',
                padding:'0',
                align:'stretch'
            },
            items:[apiDocumentsForm, apiDocuments]
		});
		
		var xsdDocuments = Ext.create('Ext.tree.Panel', {
			title : 'XML Schema',
			store : xsdDocumentsStore,
			rootVisible : false,
			iconCls : 'nav',
			autoScroll : true,
			collapsible : true,
			listeners: {
				itemclick: function(view, record, item, index, e, eOpts) {
					if (!record.data.leaf)
						return;
		
					var url = 'xsdDoc/page.php?' + record.data.id;
					var title = (record.data.qtip ? record.data.qtip : record.data.text);
					var iconCls = record.data.iconCls;
					KDoc.openTab(url, title, iconCls);
				}
			}
		});

		var referenceMenu = Ext.create('Ext.panel.Panel', {
			region : 'west',
			width : 200,
			minSize : 175,
			maxSize : 400,
			collapsible : true,
			split : true,
			layout : 'accordion',
			layoutConfig : {
				animate : true
			},
			items : [ apiDocumentsPanel, xsdDocuments ]
		});

		var referenceDocuments = Ext.create('Ext.tab.Panel', {
			region : 'center',
			autoScroll : false,
			collapsible : false,
			defaults : {
				layout : 'anchor',
				defaults : {
					anchor : '100%',
					autoScroll : true
				}
			},
	        plugins: [Ext.create('Ext.ux.TabCloseMenu'), Ext.create('Ext.ux.TabReorderer')]
		});
		
		var referencePanel = Ext.create('Ext.Panel', {
			title: 'Documentation',
			layout : 'border',
			items : [referenceMenu, referenceDocuments]
		});
		

		KDoc.openTab = function(url, title, iconCls, toolTip) {
			var tabId = KDoc.base64_encode(url);
			var tab = Ext.getCmp(tabId);
			if (!tab) {

				tab = referenceDocuments.add({
					title : title,
					iconCls : iconCls,
					id : tabId,
					closable : true,
					autoScroll : true,
					bodyPadding : 8,
					bodyCls: 'api-doc',
					loader : {
						url : url,
						renderer : 'html',
						autoLoad : true,
						scripts : true
					}
				});

				if(toolTip){
					Ext.create('Ext.tip.ToolTip', {
						target : tab.tab.id,
						html : toolTip,
						width : 'auto',
						height : 'auto'
					});
				}
			}

			tab.show();
			referencePanel.show();
		};
		
		return referencePanel;
	},

	load : function() {

		var testConsoleTab = KDoc.loadTestConsole();
		var apiDocTab = KDoc.loadReferenceDocuments();
		var clientLibs = KDoc.loadClientLibs();
		
		Ext.create('Ext.Viewport', {
			layout : 'fit',
			items : [ Ext.create('Ext.tab.Panel', {
				autoScroll : false,
				defaults : {
					layout : 'anchor',
					defaults : {
						anchor : '100% 100%',
						closable : false,
						autoScroll : false,
						padding : 0		,
						bodyPadding : 0					
					}
				},
				items : [ testConsoleTab, apiDocTab, clientLibs ]
			}) ]
		});
	}
};

Ext.onReady(function() {

	Ext.state.Manager.setProvider(Ext.create('Ext.state.CookieProvider'));

	// enable the tabTip config below
	Ext.tip.QuickTipManager.init();

	KDoc.load();
});

