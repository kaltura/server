/**
 * Handles actions for view menu
 * such as view sequence xml
 */

mw.SequencerActionsView = function( sequencer ) {
	return this.init( sequencer );
};

mw.SequencerActionsView.prototype = {
	init: function( sequencer ) {
		this.sequencer = sequencer;
	},

	/**
	 * Sequencer "viewXml" action
	 * presents a dialog that displays the current smil xml document
	 */
	viewXML: function(){
		var _this = this;
		// For now just show the sequence output
		$viewSmilXmlDialog = mw.addDialog({
			'title' : gM('mwe-sequencer-menu-view-smilxml'),
			'dragable': true,
			'width' : 800,
			'height' : 600,
			'resizable': false,
			'content' : $j('<div />')
				.append(
				// Add a loading div
				$j('<div />')
				.addClass('syntaxhighlighter_loader')
				.loadingSpinner(),

				$j('<pre />')
				.addClass( 'brush: xml; ruler: true;' )
				.css({
					'display': 'none'
				})
				.html(
					mw.escapeQuotesHTML( _this.formatXML( _this.sequencer.getSmil().getXMLString() ) )
				)
			)
		})

		// load and run the syntax highlighter:
		$j( $viewSmilXmlDialog.find('pre') ).syntaxHighlighter( function(){
			$viewSmilXmlDialog.find('.syntaxhighlighter_loader').remove();
			$viewSmilXmlDialog.find('.syntaxhighlighter').css('height', '520px');
			$viewSmilXmlDialog.find('pre').fadeIn();
		});

	},
	formatXML: function (xml) {
		var reg = /(>)(<)(\/*)/g;
		var wsexp = / *(.*) +\n/g;
		var contexp = /(<.+>)(.+\n)/g;
		xml = xml.replace(reg, '$1\n$2$3').replace(wsexp, '$1\n').replace(contexp, '$1\n$2');
		var pad = 0;
		var formatted = '';
		var lines = xml.split('\n');
		var indent = 0;
		var lastType = 'other';
		// 4 types of tags - single, closing, opening, other (text, doctype, comment) - 4*4 = 16 transitions
		var transitions = {
			'single->single'    : 0,
			'single->closing'   : -1,
			'single->opening'   : 0,
			'single->other'     : 0,
			'closing->single'   : 0,
			'closing->closing'  : -1,
			'closing->opening'  : 0,
			'closing->other'    : 0,
			'opening->single'   : 1,
			'opening->closing'  : 0,
			'opening->opening'  : 1,
			'opening->other'    : 1,
			'other->single'     : 0,
			'other->closing'    : -1,
			'other->opening'    : 0,
			'other->other'      : 0
		};

		for (var i=0; i < lines.length; i++) {
			var ln = lines[i];
			var single = Boolean(ln.match(/<.+\/>/)); // is this line a single tag? ex. <br />
			var closing = Boolean(ln.match(/<\/.+>/)); // is this a closing tag? ex. </a>
			var opening = Boolean(ln.match(/<[^!].*>/)); // is this even a tag (that's not <!something>)
			var type = single ? 'single' : closing ? 'closing' : opening ? 'opening' : 'other';
			var fromTo = lastType + '->' + type;
			lastType = type;
			var padding = '';
			
			indent += transitions[fromTo];
			for (var j = 0; j < indent; j++) {
				padding += '    ';
			}
			
			formatted += padding + ln + '\n';
		}

		return formatted;
	}
};