/**
* mwEmbed loader for SyntaxHighligher
*/
// Wrap in mw to not pollute global namespace
( function( mw ) {
	
/**
 * Presently we only include the php, css, javascript and xml brushes 
 * but their are many more in the scripts folder 
 */
mw.addResourcePaths( {
	"XRegExp" : "src/XRegExp.js",
	"SyntaxHighlighter" : "src/shCore.js",	
	"mw.style.shCore" : "styles/shCore.css",	
	"mw.style.shThemeDefault" : "styles/shThemeDefault.css",
	
	"SyntaxHighlighter.brushes.Xml" : "scripts/shBrushXml.js",
	"SyntaxHighlighter.brushes.CSS" : "scripts/shBrushCss.js",
	"SyntaxHighlighter.brushes.JScript" : "scripts/shBrushJScript.js"
})

mw.setDefaultConfig( 'SyntaxHighlighter.brushNames',  
		{
			'Xml': ['xml', 'xhtml', 'xslt', 'html', 'xhtml'],
			'JScript': ['js', 'jscript', 'javascript'],
			'CSS' : ['css']
		}
)

} )( window.mw );

( function( $ ) {
	$.fn.syntaxHighlighter = function( options, callback ) {
		if( typeof options == 'function' ){
			callback = options;
		}
		var brushNames = mw.getConfig( 'SyntaxHighlighter.brushNames');
		
		var brushClassSet = ['XRegExp', 'SyntaxHighlighter', 'mw.style.shCore', "mw.style.shThemeDefault" ]
		// Check for what type of brush we include ( SyntaxHighlighter uses class based configuration )
		$j( this.selector ).each( function(inx, element){
			var classString = $j( element ).attr('class');
			for( var brushKey in brushNames ){
				for(var i=0;i < brushNames[ brushKey ].length; i++ ){
					if( classString.toLowerCase().indexOf( 'brush: '+  brushNames[ brushKey ][i] ) !== -1 ){
						var brushClass = 'SyntaxHighlighter.brushes.' + brushKey;
						brushClassSet = $j.merge( brushClassSet, [ brushClass ] );
					}
				}
			}			
		});
		// load the resources
		mw.load( brushClassSet, function(){			
			// Run the syntaxHighligher
			SyntaxHighlighter.highlight();
			// Run the callback if set
			if( callback ){
				callback();
			}
		});
	}
} )( jQuery );

