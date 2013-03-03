(function(kmc) {

	var Preview = kmc.Preview || {};

	// Preview Partner Defaults
	kmc.vars.previewDefaults = {
		showAdvancedOptions: false,
		includeKalturaLinks: (!kmc.vars.ignore_seo_links),
		includeSeoMetadata: (!kmc.vars.ignore_entry_seo),
		deliveryType: kmc.vars.default_delivery_type,
		embedType: kmc.vars.default_embed_code_type,
		secureEmbed: kmc.vars.embed_code_protocol_https
	};	

	Preview.storageName = 'previewDefaults';
	Preview.el = '#previewModal';
	Preview.iframeContainer = 'previewIframe';

	// We use this flag to ignore all change evnets when we initilize the preview ( on page start up )
	// We will set that to true once Preview is opened.
	Preview.ignoreChangeEvents = true;

	// Set generator
	Preview.getGenerator = function() {
		if(!this.generator) {
			this.generator = new kEmbedCodeGenerator({
				host: kmc.vars.embed_host,
				securedHost: kmc.vars.embed_host_https,
				partnerId: kmc.vars.partner_id,
				includeKalturaLinks: kmc.vars.previewDefaults.includeKalturaLinks
			});
		}
		return this.generator;
	};

	Preview.clipboard = new ZeroClipboard($('.copy-code'), {
		moviePath: "lib/flash/ZeroClipboard.swf",
		trustedDomains: ['*'],
		allowScriptAccess: "always"
	});

	Preview.clipboard.on('complete', function() {
		var $this = $(this);
		// Mark embed code as selected
		$('#' + $this.data('clipboard-target')).select();
		// Close preview
		if($this.data('close') === true) {
			Preview.closeModal(Preview.el);
		}
	});

	Preview.objectToArray = function(obj) {
		var arr = [];
		for(var key in obj) {
			obj[key].id = key;
			arr.push(obj[key]);
		}
		return arr;
	};

	Preview.getObjectById = function(id, arr) {
		var result = $.grep(arr, function(e) {
			return e.id == id;
		});
		return(result.length) ? result[0] : false;
	};

	Preview.getDefault = function(setting) {
		var defaults = localStorage.getItem(Preview.storageName);
		if(defaults) {
			defaults = JSON.parse(defaults);
		} else {
			defaults = kmc.vars.previewDefaults;
		}
		if(defaults[setting] !== undefined) {
			return defaults[setting];
		}
		return null;
	};

	Preview.savePreviewState = function() {
		var previewService = this.Service;
		var defaults = {
			embedType: previewService.get('embedType'),
			secureEmbed: previewService.get('secureEmbed'),
			includeSeoMetadata: previewService.get('includeSeo'),
			deliveryType: previewService.get('deliveryType').id,
			showAdvancedOptions: previewService.get('showAdvancedOptions')
		};
		// Save defaults to localStorage
		localStorage.setItem(Preview.storageName, JSON.stringify(defaults));
	};

	Preview.getDeliveryTypeFlashVars = function(deliveryType) {
		var flashVars = (deliveryType && deliveryType.flashvars) ? deliveryType.flashvars : {};
		return $.extend({}, flashVars);
	};

	Preview.getPreviewTitle = function(options) {
		if(options.entryMeta && options.entryMeta.name) {
			return 'Embedding: ' + options.entryMeta.name;
		}
		if(options.playlistName) {
			return 'Playlist: ' + options.playlistName;
		}
		if(options.playerOnly) {
			return 'Player Name:' + options.name;
		}
	};

	Preview.openPreviewEmbed = function(options, previewService) {

		var _this = this;
		var el = _this.el;

		// Enable preview events
		this.ignoreChangeEvents = false;

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
		// Update our players
		previewService.updatePlayers(options);
		// Set options
		previewService.set(options);

		var title = this.getPreviewTitle(options);

		var $previewModal = $(el);
		$previewModal.find(".title h2").text(title).attr('title', title);
		$previewModal.find(".close").unbind('click').click(function() {
			_this.closeModal(el);
		});

		// Show our preview modal
		var modalHeight = window.innerHeight - 200;
		$previewModal.find('.content').height(modalHeight);
		kmc.layout.modal.show(el, false);
	};

	Preview.closeModal = function(el) {
		this.savePreviewState();
		this.emptyDiv(this.iframeContainer);
		$(el).fadeOut(300, function() {
			kmc.layout.overlay.hide();
			kmc.utils.hideFlash();
		});
	};

	Preview.emptyDiv = function(divId) {
		var container = document.getElementById(divId);
		container.innerHTML = '';
		return container;
	};

	Preview.hasIframe = function() {
		return $('#' + this.iframeContainer + ' iframe').length;
	};

	Preview.getCacheSt = function() {
		var d = new Date();
		return Math.floor(d.getTime() / 1000) + (15 * 60); // start caching in 15 minutes
	};

	Preview.generateIframe = function(embedCode) {

		var ltIE10 = $('html').hasClass('lt-ie10');
		var style = '<style>html, body {margin: 0; padding: 0; width: 100%; height: 100%; } #framePlayerContainer {margin: 0 auto; padding-top: 20px; text-align: center; } object, div { margin: 0 auto; }</style>';
		var container = this.emptyDiv(this.iframeContainer);
		var iframe = container.appendChild(document.createElement('iframe'));
		iframe.frameborder = 0;

		if(ltIE10) {
			iframe.src = this.getPreviewUrl(this.Service, true);
		} else {
			var newDoc = iframe.contentDocument;
			newDoc.open();
			newDoc.write('<!doctype html><html><head>' + style + '</head><body><div id="framePlayerContainer">' + embedCode + '</div></body></html>');
			newDoc.close();
		}
	};

	Preview.getEmbedProtocol = function(previewService, previewPlayer) {
		var protocol = (previewService.get('secureEmbed')) ? 'https' : 'http';
		if(previewPlayer === true) {
			protocol = location.protocol.substring(0, location.protocol.length - 1); // Get host protocol
		}
		return protocol;
	};

	Preview.getEmbedFlashVars = function(previewService, addKs) {
		var protocol = this.getEmbedProtocol(previewService, addKs);
		var player = previewService.get('player');
		var flashVars = this.getDeliveryTypeFlashVars(previewService.get('deliveryType'));
		if(addKs === true) {
			flashVars.ks = kmc.vars.ks;
		}

		var playlistId = previewService.get('playlistId');
		if(playlistId) {
			// Use new kpl0Id flashvar for new players only
			var html5_version = kmc.functions.getVersionFromPath(player.html5Url);
			if(kmc.functions.versionIsAtLeast(kmc.vars.min_kdp_version_for_playlist_api_v3, player.swf_version) 
				&& kmc.functions.versionIsAtLeast(kmc.vars.min_html5_version_for_playlist_api_v3, html5_version)) {
				flashVars['playlistAPI.kpl0Id'] = playlistId;
			} else {
				flashVars['playlistAPI.autoInsert'] = 'true';
				flashVars['playlistAPI.kpl0Name'] = previewService.get('playlistName');
				flashVars['playlistAPI.kpl0Url'] = protocol + '://' + kmc.vars.api_host + '/index.php/partnerservices2/executeplaylist?' + 'partner_id=' + kmc.vars.partner_id + '&subp_id=' + kmc.vars.partner_id + '00' + '&format=8&ks={ks}&playlist_id=' + playlistId;
			}
		}
		return flashVars;	
	};

	Preview.getEmbedCode = function(previewService, previewPlayer) {
		var player = previewService.get('player');
		if(!player || !previewService.get('embedType')) {
			return '';
		}
		var cacheSt = this.getCacheSt();
		var params = {
			protocol: this.getEmbedProtocol(previewService, previewPlayer),
			embedType: previewService.get('embedType'),
			uiConfId: player.id,
			width: player.width,
			height: player.height,
			entryMeta: previewService.get('entryMeta'),
			includeSeoMetadata: previewService.get('includeSeo'),
			playerId: 'kaltura_player_' + cacheSt,
			cacheSt: cacheSt,
			flashVars: this.getEmbedFlashVars(previewService, previewPlayer)
		};

		if(previewService.get('entryId')) {
			params.entryId = previewService.get('entryId');
		}

		var code = this.getGenerator().getCode(params);
		return code;
	};

	Preview.getPreviewUrl = function(previewService, framed) {
		var player = previewService.get('player');
		if(!player || !previewService.get('embedType')) {
			return '';
		}
		var protocol = (previewService.get('secureEmbed')) ? 'https' : 'http';
		var url = protocol + '://' + kmc.vars.base_host + '/index.php/kmc/preview';
		//var url = protocol + '://' + window.location.host + '/KMC_V2/preview.php';
		url += '/partner_id/' + kmc.vars.partner_id;
		url += '/uiconf_id/' + player.id;
		// Add entry Id
		if(previewService.get('entryId')) {
			url += '/entry_id/' + previewService.get('entryId');
		}
		url += '/embed/' + previewService.get('embedType');
		url += '?' + kmc.functions.flashVarsToUrl(this.getEmbedFlashVars(previewService, framed));
		if( framed === true ) {
			url += '&framed=true';
		}
		return url;
	};

	Preview.generateQrCode = function(url) {
		if($('html').hasClass('lt-ie9')) return;
		$('#qrcode').empty().qrcode({
			width: 80,
			height: 80,
			text: url
		});
	};

	Preview.generateShortUrl = function(url, callback) {
		if(!url) return ;
		kmc.client.createShortURL(url, callback);
	};

	kmc.Preview = Preview;

})(window.kmc);

var kmcApp = angular.module('kmcApp', []);
kmcApp.factory('previewService', ['$rootScope', function($rootScope) {
	var previewProps = {};
	return {
		get: function(key) {
			if(key === undefined) return previewProps;
			return previewProps[key];
		},
		set: function(key, value, quiet) {
			if(typeof key == 'object') {
				angular.extend(previewProps, key);
			} else {
				previewProps[key] = value;
			}
			if(!quiet) {
				$rootScope.$broadcast('previewChanged');
			}
		},
		updatePlayers: function(options) {
			$rootScope.$broadcast('playersUpdated', options);
		},
		changePlayer: function(playerId) {
			$rootScope.$broadcast('changePlayer', playerId);
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
				} else {
					elem.slideUp();
				}
			});
		}
	};
});

kmcApp.controller('PreviewCtrl', function($scope, previewService) {

	var draw = function() {
			if(!$scope.$$phase) {
				$scope.$apply();
			}
		};

	var Preview = kmc.Preview;
	Preview.playlistMode = false;

	Preview.Service = previewService;

	var updatePlayers = function(options) {
			options = options || {};
			var playerId = (options.uiConfId) ? options.uiConfId : undefined;
			// Exit if player not loaded
			if(!kmc.vars.playlists_list || !kmc.vars.players_list) {
				return ;
			}
			// List of players
			if(options.playlistId || options.playerOnly) {
				$scope.players = kmc.vars.playlists_list;
				if(!Preview.playlistMode) {
					Preview.playlistMode = true;
					$scope.$broadcast('changePlayer', playerId);
				}
			} else {
				$scope.players = kmc.vars.players_list;
				if(Preview.playlistMode || !$scope.player) {
					Preview.playlistMode = false;
					$scope.$broadcast('changePlayer', playerId);
				}
			}
			if(playerId){
				$scope.$broadcast('changePlayer', playerId);
			}
		};

	var setDeliveryTypes = function(player) {
			var deliveryTypes = Preview.objectToArray(kmc.vars.delivery_types);
			var defaultType = $scope.deliveryType || Preview.getDefault('deliveryType');
			var validDeliveryTypes = [];
			$.each(deliveryTypes, function() {
				if(this.minVersion && !kmc.functions.versionIsAtLeast(this.minVersion, player.swf_version)) {
					if(this.id == defaultType) {
						defaultType = null;
					}
					return true;
				}
				validDeliveryTypes.push(this);
			});
			// List of delivery types
			$scope.deliveryTypes = validDeliveryTypes;
			// Set default delivery type
			if(!defaultType) {
				defaultType = $scope.deliveryTypes[0].id;
			}
			$scope.deliveryType = defaultType;
		};

	var setEmbedTypes = function(player) {
			var embedTypes = Preview.objectToArray(kmc.vars.embed_code_types);
			var defaultType = $scope.embedType;
			var validEmbedTypes = [];
			$.each(embedTypes, function() {
				// Don't add embed code that are entry only for playlists
				if(Preview.playlistMode && this.entryOnly) {
					if(this.id == defaultType) {
						defaultType = null;
					}
					return true;
				}
				// Check for library minimum version to eanble embed type
				var libVersion = kmc.functions.getVersionFromPath(player.html5Url);
				if(this.minVersion && !kmc.functions.versionIsAtLeast(this.minVersion, libVersion)) {
					if(this.id == defaultType) {
						defaultType = null;
					}
					return true;
				}
				validEmbedTypes.push(this);
			});
			// List of embed types
			$scope.embedTypes = validEmbedTypes;
			// Set default embed type
			if(!defaultType) {
				defaultType = $scope.embedTypes[0].id;
			}
			$scope.embedType = defaultType;
		};

	// Set defaults
	$scope.players = [];
	$scope.player = null;
	$scope.deliveryTypes = [];
	$scope.deliveryType = null;
	$scope.embedTypes = [];
	$scope.embedType = Preview.getDefault('embedType');
	$scope.secureEmbed = Preview.getDefault('secureEmbed');
	$scope.includeSeo = Preview.getDefault('includeSeoMetadata');
	$scope.previewOnly = false;
	$scope.playerOnly = false;
	$scope.liveBitrates = false;
	$scope.showAdvancedOptionsStatus = Preview.getDefault('showAdvancedOptions');

	// Set players on update
	$scope.$on('playersUpdated', function(e, options) {
		updatePlayers(options);
	});

	$scope.$on('changePlayer', function(e, playerId) {
		playerId = ( playerId ) ? playerId : $scope.players[0].id;
		$scope.player = playerId;
	});

	$scope.showAdvancedOptions = function($event, show) {
		$event.preventDefault();
		previewService.set('showAdvancedOptions', show, true);
		$scope.showAdvancedOptionsStatus = show;
	};

	$scope.$watch('showAdvancedOptionsStatus', function() {
		Preview.clipboard.reposition();
	});

	// Listen to player change
	$scope.$watch('player', function() {
		var player = Preview.getObjectById($scope.player, $scope.players);
		if(!player) return ;
		setDeliveryTypes(player);
		setEmbedTypes(player);
		previewService.set('player', player);
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
		Preview.generateIframe($scope.embedCodePreview);
	});
	$scope.$watch('previewOnly', function() {
		if($scope.previewOnly) {
			$scope.closeButtonText = 'Close';
		} else {
			$scope.closeButtonText = 'Copy Embed & Close';
		}
		draw();
	});
	$scope.$on('previewChanged', function(e) {
		if(Preview.ignoreChangeEvents) return;
		var previewUrl = Preview.getPreviewUrl(previewService);
		$scope.embedCode = Preview.getEmbedCode(previewService);
		$scope.embedCodePreview = Preview.getEmbedCode(previewService, true);
		$scope.previewOnly = previewService.get('previewOnly');
		$scope.playerOnly = previewService.get('playerOnly');
		$scope.liveBitrates = previewService.get('liveBitrates');
		draw();
		// Generate Iframe if not exist
		if(!Preview.hasIframe()) {
			Preview.generateIframe($scope.embedCodePreview);
		}
		// Generate QR Code
		Preview.generateQrCode(previewUrl);
		// Update Short url
		$scope.previewUrl = 'Updating...';
		Preview.generateShortUrl(previewUrl, function(tinyUrl) {
			$scope.previewUrl = tinyUrl;
			draw();
		});
	});

});