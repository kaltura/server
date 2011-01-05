/*
* Simple embed object for unknown application/ogg plugin
*/
mw.EmbedPlayerGeneric = {
	// List of supported features of the generic plugin
	 supports: {
		'playHead':false,
		'pause':false,
		'stop':true,
		'fullscreen':false,
		'timeDisplay':false,
		'volumeControl':false
	},

	// Instance name:
	instanceOf:'Generic',

	/*
	* Generic embed html
	*
	* @return {String}
	* 	embed code for generic ogg plugin
	*/
	doEmbedHTML: function() {
		$j( this ).html(
			'<object type="application/ogg" ' +
			'width="' + this.getWidth() + '" height="' + this.getHeight() + '" ' +
			'data="' + this.getSrc( this.seek_time_sec ) + '"></object>'
		);
	}
};
