Delegate = function () {
};

Delegate.create = function (/*Object*/ scope, /*Function*/ method ) {
	var f = function () {
		return method.apply (scope, arguments);
	}
	return f;
};


KalturaPlayerController = function (playerId) {
	this.playerId = playerId;
	this.currentKshowId = -1;
	this.currentEntryId = -1;
	this.commands = Array();
};

KalturaPlayerController.prototype = {
	insertMedia: function (kshowId, entryId, autoStart) {
		this.queue("insertMedia", kshowId, entryId, autoStart);
		this.currentKshowId = kshowId;
		this.currentEntryId = entryId;
	},
	
	insertEntry: function (entryId, autoStart) {
		this.insertMedia(-1, entryId, autoStart);
	},
	
	insertKShow: function (kshowId, autoStart) {
		this.insertMedia(kshowId, -1, autoStart);
	},
	
	pause: function () {
		this.queue("pauseMedia");
	},
	
	stop: function () {
		this.queue("stopMedia");
	},
	
	play: function () {
		this.queue("playMedia");
	},
	
	seek: function (time) {
		this.queue("seekMedia", time);
	}, 
	
	queue: function () {
		if (arguments.length > 0) { // first argument is the method name and is mandatory
			var method = arguments[0];
			var args = Array.prototype.slice.call(arguments, [1]); // shift to remove the method name from the array
			this.commands.push({ method: method, args: args });
			this.invoke();
		}
	},
	
	invoke: function () {
		this.playerElement = document.getElementById(this.playerId);

		// no commands in queue
		if (!this.commands || this.commands.length == 0)
			return;
	
		var command = this.commands[0];
		var method = command.method;
		var args = command.args;
		
		if (this.playerElement && this.playerElement[method]) {
			// apply is not possible on external interface functions, so we'll do this with a switch
			switch (args.length) {
				case 1:
					this.playerElement[method](args[0]);
					break;
				case 2:
					this.playerElement[method](args[0], args[1]);
					break;
				case 3:
					this.playerElement[method](args[0], args[1], args[2]);
					break;
				case 4:
					this.playerElement[method](args[0], args[1], args[2], args[2]);
					break;
				default:
					this.playerElement[method]();
					break; 	
			}

			this.commands.shift();
		}
		else {
			var f = Delegate.create(this, this.invoke);
			setTimeout(f, 200);
		}
	},
	
	reload: function () {
		this.insertMedia(this.currentKshowId, this.currentEntryId, true);
	}
};