(function (kmc) {

var Preview = kmc.Preview || {};

// Set generator
Preview.generator = new kEmbedCodeGenerator({
	host: kmc.vars.embed_host,
	securedHost: kmc.vars.embed_host_https,
	partnerId: kmc.vars.partner_id
});

Preview.objectToArray = function( obj ) {
	var arr = [];
	for( var key in obj ) {
		obj[key].id = key;
		arr.push( obj[key] );
	}
	return arr;
};

Preview.getObjectById = function( id, arr ) {
	var result = $.grep(arr, function(e){ return e.id == id; });
	return (result.length) ? result[0] : false;
};
Preview.getDeliveryTypeFlashVars = function( deliveryType ) {
	return ( deliveryType && deliveryType.flashvars ) ? deliveryType.flashvars : {};
};

Preview.getPreviewTitle = function( options ) {
	if( options.entryMeta && options.entryMeta.name ) {
		return 'Embedding: ' + options.entryMeta.name;
	}
	if( options.playlistName ) {
		return 'Playlist: ' + options.playlistName;
	}
	if( options.playerOnly ) {
		return 'Player Name:' + options.name;
	}
}

Preview.openPreviewEmbed = function( options, previewService ) {

	var _this = this;
	var el = '#previewModal';

	var defaults = {
		entryId: null,
		entryMeta: {},
		playlistId: null,
		playlistName: null,
		previewOnly: false,
		liveBitrates: null,
		playerOnly: false,
		uiConfId: null,
		name: null
	};

	options = $.extend({}, defaults, options);

	// If liveStream, set delivery type to 'auto' and hide deliveryTypes combo and show Bitrates data in the UI
	// If playerOnly ( Multiple Playlists ) hide Players combo
	// Auto generate shortlink

	// Set options
	previewService.set(options);

	var title = this.getPreviewTitle( options );

	var $previewModal = $(el);
	$previewModal.find(".title h2").text(title).attr('title', title);
	$previewModal.find(".close").unbind('click').click( function() {
		_this.closeModal(el);
	});

	// Show our preview modal
	kmc.layout.modal.show(el, false);
};

Preview.closeModal = function(el) {
	$(el).fadeOut(300, function() {
		kmc.layout.overlay.hide();
		kmc.utils.hideFlash();
	});
};

Preview.generateIframe = function(embedCode, container) {

	var style = '<style>html, body {margin: 0; padding: 0; width: 100%; height: 100%; } #framePlayerContainer {margin: 0 auto; padding-top: 20px;} object, div { margin: 0 auto; }</style>';
	var container = document.getElementById(container);	
	container.innerHTML = '';	
	var iframe = container.appendChild(document.createElement('iframe'));
	var newDoc = iframe.contentDocument;

	newDoc.open();
	newDoc.write( '<!doctype html><html><head>' + style + '</head><body><div id="framePlayerContainer">' + embedCode + '</div></body></html>' );
	newDoc.close();
};


Preview.getEmbedCode = function(previewService, previewPlayer) {
	var player = previewService.get('player');
	if( !player || !previewService.get('embedType') ) {
		return '';
	}
	var fv = this.getDeliveryTypeFlashVars( previewService.get('deliveryType') );
	var protocol = (previewService.get('secureEmbed')) ? 'https':'http';
	if( previewPlayer === true ) {
		fv['ks'] = kmc.vars.ks;
		protocol = location.protocol.substring(0, location.protocol.length - 1); // Get host protocol
	} else {
		if( fv['ks'] ) {
			delete fv['ks'];
		}
	}
	var params = {
		protocol: protocol,
		embedType: previewService.get('embedType'),
		uiConfId: player.id,
		width: player.width,
		height: player.height,
		includeSeoMetadata: previewService.get('includeSeo'),
		flashVars: fv
	};
	if( previewService.get('entryId') ) {
		params['entryId'] = previewService.get('entryId');
	}
	var code = this.generator.getCode(params);
	return code;
};

Preview.getPreviewUrl = function(previewService) {
	var player = previewService.get('player');
	if( !player || !previewService.get('embedType') ) {
		return '';
	}
	var protocol = (previewService.get('secureEmbed')) ? 'https':'http';
	var url = protocol + '://' + kmc.vars.host + '/index.php/kmc/preview';
		url += '/partner_id/' + kmc.vars.partner_id;
		url += '/uiconf_id/' + player.id;
		// Add playlist data
		if( previewService.get('playlistId') ) {
			url += '/playlist_id/' + previewService.get('playlistId') + '/playlist_name/' + previewService.get('playlistName');
		}
		// Add entry Id
		if( previewService.get('entryId') ) {
			url += '/entry_id/' + previewService.get('entryId');
		}
		url += '/delivery/' + previewService.get('deliveryType').id;
		url += '/embed/' + previewService.get('embedType');

	return url;
};

Preview.generateQrCode = function( url ) {
	$('#qrcode').empty().qrcode({width: 90,height: 90,text: url});
};

Preview.generateShortUrl = function( url, callback ) {
	kmc.client.createShortURL( url, callback );
};

kmc.Preview = Preview;

})(window.kmc);

var kmcApp = angular.module('kmcApp', []);
kmcApp.factory('previewService', ['$rootScope', function($rootScope) {
   var previewProps = {};
    return {
        get: function (key) {
            return previewProps[key];
        },
        set: function (key, value) {
        	if( typeof key == 'object' ) {
        		angular.extend(previewProps, key);
        	} else {
        		previewProps[key] = value;
        	}
            $rootScope.$broadcast('previewChanged');
        },
        updatePlayers: function() {
        	$rootScope.$broadcast('playersUpdated');
        }
    };
}]);
kmcApp.directive('showSlide', function() {
   return {
     //restrict it's use to attribute only.
     restrict: 'A',

     //set up the directive.
     link: function(scope, elem, attr) {

        //get the field to watch from the directive attribute.
        var watchField = attr.showSlide;

        //set up the watch to toggle the element.
        scope.$watch(attr.showSlide, function(v) {
           if(v && !elem.is(':visible')) {
              elem.slideDown();
           }else {
              elem.slideUp();
           }
        });
     }
   }
});

kmcApp.controller('PreviewCtrl', function($scope, previewService) {

	var Preview = kmc.Preview;

	Preview.Service = previewService;

	// Set empty players
	$scope.players = [];
	$scope.player = null;
	// Set players on update
  	$scope.$on('playersUpdated', function() {
		if( kmc.vars.playlists_list && kmc.vars.players_list ) {
			// List of players
			$scope.players = (previewService.get('playlistId')) ? kmc.vars.playlists_list : kmc.vars.players_list;
		}
		if( $scope.players.length ){
			// Set default player to the first
		  	$scope.player = $scope.players[0].id;
		}
  	});	

	// List of delivery types
	$scope.deliveryTypes = Preview.objectToArray(kmc.vars.delivery_types);
	// Set default delivery type
	$scope.deliveryType = $scope.deliveryTypes[0].id;
	// List of embed types
	$scope.embedTypes = Preview.objectToArray(kmc.vars.embed_code_types);
	// Set default embed type
	$scope.embedType = $scope.embedTypes[0].id;

	$scope.secureEmbed = false;
	$scope.includeSeo = false;

	$scope.showAdvancedOptionsStatus = false;
	$scope.showAdvancedOptions = function( $event, show ) {
		$event.preventDefault();
		$scope.showAdvancedOptionsStatus = show;
	};

  	// Listen to player change
  	$scope.$watch('player', function() {
  		previewService.set('player', Preview.getObjectById( $scope.player, $scope.players ));
  	});
	$scope.$watch('deliveryType', function() {
		previewService.set('deliveryType', Preview.getObjectById($scope.deliveryType, $scope.deliveryTypes));
  	});
	$scope.$watch('embedType', function() {
		previewService.set('embedType', $scope.embedType);
  	});
  	$scope.$watch('secureEmbed', function() {
  		previewService.set('secureEmbed', $scope.secureEmbed);
  	});
  	$scope.$watch('includeSeo', function() {
  		previewService.set('includeSeo', $scope.includeSeo);
  	});
  	$scope.$watch('embedCodePreview', function() {
  		Preview.generateIframe($scope.embedCodePreview, 'previewIframe');
  	});
  	$scope.$on('previewChanged', function(e) {
  		$scope.embedCode = Preview.getEmbedCode(previewService);
  		$scope.embedCodePreview = Preview.getEmbedCode(previewService, true);
  		if (!$scope.$$phase) {
  			$scope.$apply();
  		}
  		// Get preview url
  		var previewUrl = Preview.getPreviewUrl(previewService);
  		// Generate QR Code
  		Preview.generateQrCode( previewUrl );
  		// Update Short url
  		$scope.previewUrl = 'Updating...';
  		Preview.generateShortUrl( previewUrl, function( tinyUrl ) {
  			$scope.previewUrl = tinyUrl;
  			$scope.$apply();
  		});
  	});

});
