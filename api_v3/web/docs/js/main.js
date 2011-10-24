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
	mainWindow : null,
	testmeResultsTab : null,
	testmePropertiesPanel : null,
	apiDocumentsStore : null,
	xsdDocumentsStore : null,

	jsonToStore : function(json, data) {

		if (!data) {
			data = {
				objectName : 'results',
				objectType : 'array',
				expanded : false,
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

	onTestmeResults : function(json) {
		var data = KDoc.jsonToStore(json);

		KDoc.testmeResultsTab.store.tree.root.removeAll(false);
		KDoc.testmeResultsTab.store.tree.root.appendChild(data);
		KDoc.testmeResultsTab.expandAll(function(){
			KDoc.testmeResultsTab.doComponentLayout();
		});
	},

	loadStores : function() {
		
		KDoc.apiDocumentsStore = Ext.create('Ext.data.TreeStore', {
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

		KDoc.xsdDocumentsStore = Ext.create('Ext.data.TreeStore', {
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

	openTab : function(url, title, iconCls, toolTip) {
		var tabId = KDoc.base64_encode(url);
		var tab = Ext.getCmp(tabId);
		if (!tab) {
			tab = KDoc.mainWindow.add({
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
	
	load : function() {

		KDoc.loadStores();

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
				objectName : '.'
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
						
						var treeColumn = KDoc.testmeResultsTab.columns[column];
						testmeResultsSelector.selectedText = record.data[treeColumn.dataIndex];
					}
				}
			})
		};

		KDoc.testmeResultsTab = Ext.create('Ext.tree.Panel', {
			region : 'center',
			rootVisible : false,
			autoScroll : true,
			store : store,
			selModel : testmeResultsSelector.selector,
			listeners: {
				sortchange: function(container, column, direction, eOpts){
					KDoc.testmeResultsTab.doComponentLayout();
				}
			},
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

		KDoc.testmeResultsTab.on('itemcontextmenu', function(view, record, item, index, e, eOpts) {
			e.preventDefault();
			testmeResultsContextMenu.setPosition(e.xy[0], e.xy[1]);
			testmeResultsContextMenu.show();
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

		KDoc.testmePropertiesPanel = Ext.create('Ext.panel.Panel', {
			region : 'west',
			split : true,
			collapsible : true,
			collapsed : true,
			minWidth : 270,
			layout : 'accordion'
		});

		var testmePanel = Ext.create('Ext.panel.Panel', {
			title : 'Console Results',
			closable : false,
			autoScroll : false,
			layout : 'border',
			items : [ KDoc.testmeResultsTab, codeExamplePanel, KDoc.testmePropertiesPanel ]
		});

		KDoc.mainWindow = Ext.create('Ext.tab.Panel', {
			region : 'center',
			margins : '5 5 5 0',
			autoScroll : true,
			defaults : {
				layout : 'anchor',
				defaults : {
					anchor : '100%'
				}
			},
			items : [ testmePanel ]
		});

		var testmeForm = Ext.create('Ext.panel.Panel', {
			title : 'Test Console',
			autoScroll : true,
			border : false,
			iconCls : 'nav',
			bodyPadding : 8,
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

		var apiDocuments = Ext.create('Ext.tree.Panel', {
			id : 'apiDocumentsTree',
			title : 'API',
			store : KDoc.apiDocumentsStore,
			rootVisible : false,
			iconCls : 'nav',
			collapsible : true
		});

		var xsdDocuments = Ext.create('Ext.tree.Panel', {
			id : 'xsdDocumentsTree',
			title : 'XML Schema',
			store : KDoc.xsdDocumentsStore,
			rootVisible : false,
			iconCls : 'nav',
			collapsible : true
		});

		apiDocuments.addListener('itemclick', function(view, record, item,
				index, e, eOpts) {

			if (!record.data.leaf && record.data.parentId != 'services')
				return;

			var url = 'testmeDoc/page.php?' + record.data.id;
			var title = (record.data.qtip ? record.data.qtip : record.data.text);
			var iconCls = record.data.iconCls;
			var toolTip = record.data.text;
			KDoc.openTab(url, title, iconCls, toolTip);
		});

		xsdDocuments.addListener('itemclick', function(view, record, item,
				index, e, eOpts) {

			if (!record.data.leaf)
				return;


			var url = 'xsdDoc/page.php?' + record.data.id;
			var title = (record.data.qtip ? record.data.qtip : record.data.text);
			var iconCls = record.data.iconCls;
			KDoc.openTab(url, title, iconCls);
		});

		Ext.create('Ext.Viewport', {
			layout : 'border',
			items : [ {
				region : 'west',
				id : 'west-panel',
				width : 200,
				minSize : 175,
				maxSize : 400,
				collapsible : true,
				split : true,
				margins : '5 0 5 5',
				cmargins : '5 5 5 5',
				layout : 'accordion',
				layoutConfig : {
					animate : true
				},
				items : [ testmeForm, apiDocuments, xsdDocuments ]
			}, KDoc.mainWindow ]
		});
	}
};

Ext.onReady(function() {

	Ext.state.Manager.setProvider(Ext.create('Ext.state.CookieProvider'));

	// enable the tabTip config below
	Ext.tip.QuickTipManager.init();

	KDoc.load();
});

