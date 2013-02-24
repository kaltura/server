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
		moviePath: "lib/flash/ZeroClipboard.swf"
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
		$(el).fadeOut(300, function() {
			kmc.layout.overlay.hide();
			kmc.utils.hideFlash();
		});
	};

	Preview.generateIframe = function(embedCode, previewUrl, containerId) {

		if( ! previewUrl ) return ;

		var ltIE10 = $('html').hasClass('lt-ie10');
		var style = '<style>html, body {margin: 0; padding: 0; width: 100%; height: 100%; } #framePlayerContainer {margin: 0 auto; padding-top: 20px; text-align: center; } object, div { margin: 0 auto; }</style>';
		container = document.getElementById(containerId);
		container.innerHTML = '';
		var iframe = container.appendChild(document.createElement('iframe'));
		iframe.frameborder = 0;

		// Append framed and KS params
		previewUrl += '/framed/true/ks/' + kmc.vars.ks;

		if(ltIE10) {
			iframe.src = previewUrl;
		} else {
			var newDoc = iframe.contentDocument;
			newDoc.open();
			newDoc.write('<!doctype html><html><head>' + style + '</head><body><div id="framePlayerContainer">' + embedCode + '</div></body></html>');
			newDoc.close();
		}
	};


	Preview.getEmbedCode = function(previewService, previewPlayer) {
		var player = previewService.get('player');
		if(!player || !previewService.get('embedType')) {
			return '';
		}

		var flashVars = this.getDeliveryTypeFlashVars(previewService.get('deliveryType'));
		var protocol = (previewService.get('secureEmbed')) ? 'https' : 'http';
		if(previewPlayer === true) {
			flashVars.ks = kmc.vars.ks;
			protocol = location.protocol.substring(0, location.protocol.length - 1); // Get host protocol
		}

		var playlistId = previewService.get('playlistId');
		if(playlistId) {
			// Use new kpl0Id flashvar for new players only
			var html5_version = kmc.functions.getVersionFromPath(player.html5Url);
			if(kmc.functions.versionIsAtLeast(kmc.vars.min_kdp_version_for_playlist_api_v3, player.swf_version) && kmc.functions.versionIsAtLeast(kmc.vars.min_html5_version_for_playlist_api_v3, html5_version)) {
				flashVars['playlistAPI.kpl0Id'] = playlistId;
			} else {
				flashVars['playlistAPI.autoInsert'] = 'true';
				flashVars['playlistAPI.kpl0Name'] = previewService.get('playlistName');
				flashVars['playlistAPI.kpl0Url'] = protocol + '://' + kmc.vars.api_host + '/index.php/partnerservices2/executeplaylist?' + 'partner_id=' + kmc.vars.partner_id + '&subp_id=' + kmc.vars.partner_id + '00' + '&format=8&ks={ks}&playlist_id=' + playlistId;
			}
		}

		var params = {
			protocol: protocol,
			embedType: previewService.get('embedType'),
			uiConfId: player.id,
			width: player.width,
			height: player.height,
			includeSeoMetadata: previewService.get('includeSeo'),
			flashVars: flashVars
		};

		if(previewService.get('entryId')) {
			params.entryId = previewService.get('entryId');
		}

		var code = this.getGenerator().getCode(params);
		return code;
	};

	Preview.getPreviewUrl = function(previewService) {
		var player = previewService.get('player');
		if(!player || !previewService.get('embedType')) {
			return '';
		}
		var protocol = (previewService.get('secureEmbed')) ? 'https' : 'http';
		var url = protocol + '://' + kmc.vars.host + '/index.php/kmc/preview';
		//var url = protocol + '://localhost/KMC_V2/preview.php';
		url += '/partner_id/' + kmc.vars.partner_id;
		url += '/uiconf_id/' + player.id;
		// Add playlist data
		if(previewService.get('playlistId')) {
			url += '/playlist_id/' + previewService.get('playlistId') + '/playlist_name/' + previewService.get('playlistName');
		}
		// Add entry Id
		if(previewService.get('entryId')) {
			url += '/entry_id/' + previewService.get('entryId');
		}
		url += '/delivery/' + previewService.get('deliveryType').id;
		url += '/embed/' + previewService.get('embedType');

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
		kmc.client.createShortURL(url, callback);
	};

	kmc.Preview = Preview;

})(window.kmc);

var kmcApp = angular.module('kmcApp', []);
kmcApp.factory('previewService', ['$rootScope', function($rootScope) {
	var previewProps = {};
	return {
		get: function(key) {
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

	var setPlayer = function(uiConfId) {
			if($scope.players.length) {
				// Set default player to the first
				uiConfId = uiConfId || $scope.players[0].id;
				$scope.player = uiConfId;
			}
		};

	var updatePlayers = function() {
			if(kmc.vars.playlists_list && kmc.vars.players_list) {
				// List of players
				if(previewService.get('playlistId') || previewService.get('playerOnly')) {
					$scope.players = kmc.vars.playlists_list;
					if(!Preview.playlistMode) {
						Preview.playlistMode = true;
						setPlayer(previewService.get('uiConfId'));
					}
				} else {
					$scope.players = kmc.vars.players_list;
					if(Preview.playlistMode || !$scope.player) {
						Preview.playlistMode = false;
						setPlayer(previewService.get('uiConfId'));
					}
				}
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
	$scope.iframeUrl = null;

	// Set players on update
	$scope.$on('playersUpdated', function() {
		updatePlayers();
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
		if( ! player ) return ;
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
		Preview.generateIframe($scope.embedCodePreview, $scope.iframeUrl, 'previewIframe');
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
		updatePlayers();
		$scope.iframeUrl = Preview.getPreviewUrl(previewService);
		$scope.embedCode = Preview.getEmbedCode(previewService);
		$scope.embedCodePreview = Preview.getEmbedCode(previewService, true);
		$scope.previewOnly = previewService.get('previewOnly');
		$scope.playerOnly = previewService.get('playerOnly');
		$scope.liveBitrates = previewService.get('liveBitrates');
		draw();
		// Generate QR Code
		Preview.generateQrCode($scope.iframeUrl);
		// Update Short url
		$scope.previewUrl = 'Updating...';
		Preview.generateShortUrl($scope.iframeUrl, function(tinyUrl) {
			$scope.previewUrl = tinyUrl;
			draw();
		});
	});

});