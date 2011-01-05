
// Wrap in mw 
( function( mw ) {
	// Add as loader dependency 'mw.style.mirosubsMenu'
	mw.addResourcePaths({
		"mw.ui.languageSelectBox" : "mw.ui.languageSelectBox.js",
		"mw.Language.names" : "mw.Language.names.js",		
		"$j.ui.combobox" : "jQueryPlugins/jquery.ui.combobox.js"
	});
	
} )( window.mw );