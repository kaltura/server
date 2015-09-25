(function($){
	$.GoogleOAuth2Page = function(el, options){
		var $el = $(el);

		function callApi(data, onSuccessFunction, onErrorFunction){
			var url = options.serviceUrl + '/api_v3/index.php';
			data.format = 9;
			$.ajax ({
				url: url,
				data: data,
				success: onSuccessFunction,
				error: onErrorFunction,
				dataType: 'jsonp'
			});
		}

		function onSubmit() {
			$('body').addClass('wait');
			$el.find('.error').text('');
			var $email = $el.find('input[name=email]');
			var $password = $el.find('input[name=password]');
			$email.removeClass('invalid');
			$password.removeClass('invalid');
			if (!$email.val()) {
				$email.addClass('invalid');
				return false;
			}
			if (!$password.val()) {
				$password.addClass('invalid');
				return false;
			}

			var partnerId = getQueryVariable("partnerId");
			if (partnerId == false)
			{
				$el.find('.error').text('partnerId param not found, please contact Kaltura Support');
			}
			else
			{
				var data = {
				    service: 'user',
				    action: 'loginByLoginId',
				    loginId: $email.val(),
				    password: $password.val(),
				    partnerId: partnerId
				};
				callApi(data, onLoginApiSuccess, onLoginApiError);
			}
			return false;
		}
		
		function getQueryVariable(variable) {
	               var query = window.location.search.substring(1);
	               var vars = query.split("&");
	               for (var i=0;i<vars.length;i++) {
	                       var pair = vars[i].split("=");
	                       if(pair[0] == variable){return pair[1];}
               		}
               		return(false);
        	}


		function onLoginApiSuccess(data) {
			$('body').removeClass('wait');
			if (data.code && data.message) {
				$el.find('.error').text(data.message);
				return;
			}
			else
			{
				var nextUrl = options.nextUrl;
				nextUrl = nextUrl + data;
				window.location.href = nextUrl;
			}
		}

		function onLoginApiError() {
			$('body').removeClass('wait');
			$el.find('.error').text('Something went wrong, please try again');
		}

		function init() {
			$el.submit(onSubmit);
		}

		init();
	}
})(jQuery);
