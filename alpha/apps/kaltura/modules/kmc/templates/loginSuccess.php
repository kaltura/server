<?php
	$divId = 'mainLogin';
	if ($hashKeyErrorCode) {
		$divId = 'newPasswordError';
	}
	else if ($setPassHashKey) {
		$divId = 'newPasswordSuccess';
	}
?>
<link href="/lib/css/login.css" media="screen" rel="stylesheet" type="text/css" />

<div id="kmcHeader">
	<img src="<?php echo $service_url; ?>/lib/images/kmc/logo_kmc.png" alt="Kaltura CMS" />
		<div id="user_links">
        	<a href="<?php echo $service_url; ?>/lib/pdf/KMC_Quick_Start_Guide.pdf" target="_blank">Quickstart Guide</a>
	</div> 
</div>

<div id="mainLogin" class="kmcLoginPage">
	<form method="post" id="loginForm" autocomplete="off">
		<div class="title"><h1>Login</h1></div>
		<div class="content">
				<div class="item clearfix">
					<label>Email:</label>
					<input name="loginId" />
				</div>
				<div class="item">
					<label>Password:</label>
					<input type="password" name="password" />
				</div>				
					<label class="remember">
						<input type="checkbox" name="remember" />
						Remeber Me?
					</label>
				
			<br style="clear: both" />
			<p class="errorText"></p>
		</div>			
		<div class="bottom">
			<a id="forgotPasswordLink" href="#" >Forgot Password?</a>
			<a id="signupLink" href="<?php echo kConf::get('signup_url'); ?>">Sigunup</a>	 
			<input type="submit" value="Login" />
		</div>
	</form>
</div>

<div id="forgotPasswordExtension" class="kmcLoginPage">
	<form method="post" id="forgotPasswordForm">
		<div class="title"><h1>Forgot Password?</h1></div>
		<div class="content">
				<div class="item bottomSpace">
					<p>
						Instructions to reset your password will be sent to your email. If you have forgotten which email you used, please
						<a href="<?php echo kConf::get('contact_url'); ?>">contact us</a>.
					</p>
				</div>
				<div class="item clearfix ">
					<label>Email:</label>
					<input name="loginId" />
				</div>
			<br style="clear: both" />
			<p class="errorText"></p>
		</div>	
		
		<div class="bottom">
			<a id="loginLink" href="#" >Login</a>	 
			<input type="submit" value="Send" />
		</div>
	</form>
</div>

<div id="newPasswordError" class="kmcLoginPage">
	<form method="post" id="newPasswordErrorForm">
		<div class="title"><h1>Link Invalid</h1></div>
		<div class="content">
				<div class="item">
					<p>
						This link is invalid or has expired. Please enter you email address to receive a new link.
					</p>
				</div>
				<div class="item clearfix ">
					<label>Email:</label>
					<input name="loginId" />
				</div>
			<br style="clear: both" />
			<p class="errorText"></p>
		</div>	
		
		<div class="bottom">
			<a id="loginLinkInNewPasswordError" href="#" >Login</a>	 
			<input type="submit" value="Send" />
		</div>
	</form>
</div>

<div id="newPasswordSuccess" class="kmcLoginPage">
	<form method="post" id="newPasswordSuccessForm" autocomplete="off">
		<div class="title"><h1>Set Password</h1></div>
		<div class="content">
				<div class="item">
					<p>
						Welcome to Kaltura, please select your password.
					</p>
				</div>
				<div class="item">
					<label>New Password:</label>
					<input type="password" name="newPassword" />
				</div>	
				<div class="item">
					<label>Confirm Password:</label>
					<input type="password" name="confirmPassword" />
				</div>
			<p class="errorText"></p>	
			<br style="clear: both" />
			
		</div>	
		
		<div class="bottom">
			<input type="submit" value="Send" />
		</div>
	</form>
</div>

<div id="passwordExpired" class="kmcLoginPage">
	<form method="post" id="passwordExpiredForm" autocomplete="off">
		<div class="title"><h1>Password Expired</h1></div>
		<div class="content">
				<div class="item bottomSpace">
					<p>
						Your password is older than 90 days. To continue using the system you need to change your password now.
					</p>
				</div>
				<div class="item">
					<label>Old Password:</label>
					<input type="password" name="oldPassword" />
				</div>	
				<div class="item">
					<label>New Password:</label>
					<input type="password" name="newPassword" />
				</div>	
				<div class="item">
					<label>Confirm Password:</label>
					<input type="password" name="confirmPassword" />
				</div>
				<input type="hidden" name="loginId" />
			<p class="errorText"></p>	
			<br style="clear: both" />
			
		</div>	
		
		<div class="bottom">
			<input type="submit" value="Send" />
		</div>
	</form>
</div>

<script>
// Prevent the page to be framed
if(top != window) { top.location = window.location; }

/******************************
 * 
 * General page events
 */
var toggleDiv = function (divId) {
	$('.kmcLoginPage').hide();
	$('#'+ divId).show();
};
//selecting the div to show first
$(function(){	
	var divId = '<?php echo $divId; ?>';
	toggleDiv(divId);
});

//creating button effects on the input element
$(".bottom input[type=submit]").mouseover(function() {
	this.style.backgroundColor='#6CA2B2';
});

$(".bottom input[type=submit]").mouseout(function() {
	this.style.backgroundColor='#467989';
});

$(".bottom input[type=submit]").click(function() {
	this.style.backgroundColor='#98CBD8';
});

//validate email inserted in loginForm
$("#loginForm input[name=loginId]").change(function(){
	valid = validate.email($(this));
	if (!valid){
		showError("Invalid e-mail");
	}
	else{
		eraseAllErrors();
	}
});

//validate email inserted
$(".kmcLoginPage input[name=loginId]").change(function(){
	valid = validate.email($(this));
	if (!valid){
		showError("Invalid e-mail");
	}
	else{
		eraseAllErrors();
	}
});

var login = {
		onSuccess: function(data) {
				if (data)
				{
					if (data.code && data.message)
					{					
						if (data.code == "PASSWORD_EXPIRED"){
							$("#passwordExpiredForm input[name=loginId]").val($("#loginForm input[name=loginId]").val());
							changeCurrentDiv("passwordExpired", "mainLogin", "loginForm");	
						}
						else{
							showError (data, "loginForm");
						}
					}
					else
					{
						var ks = data;
						var expiryTime = $("input[name=expiry]").val();
						var url = '<?php echo str_replace ( "https://" , "http://" , $service_url );?>/index.php/kmc/extlogin?ks='+ ks +'&exp=' + expiryTime; 
						window.location = url;
					}
				}
				else
				{				
					showError("Oops, an error occurred, please try again!" ,"loginForm");
				}
			},
					
		onError: function(data) {
					showError (data, "loginForm");
			},
					
		callApi: function(loginId, password, expiryTime) {
				var data = {
						service: 'user',
						action: 'loginByLoginId',
						loginId: loginId,
						password: password,
						expiry: expiryTime
				};
				callApi(data, this.onSuccess, this.onError);				
			},
				
		submitLogin: function (){
			//removing all forms error messages
			eraseAllErrors();	
			//retreiving the input fields from form	
			var loginId = $("#loginForm input[name=loginId]");
			var password = $("#loginForm input[name=password]");
			//check that fields are inserted and validate
			if (!validate.email(loginId)){
				showError("Invalid e-mail");
				return false;
			}
			if (!validate.password(password)){
				showError("Password was not supplied");
				return false;
			}
			var expiryTime = 86400; 
			if ($("#loginForm input[name=remember]").is(':checked')){
				expiryTime = 60*60*24*30;		
			}
			login.callApi(loginId.val(), password.val(), expiryTime);
			return false;	
		}
	}

var passwordExpired = {
			onSuccess: function(data,loginId){
				if (data)
				{
					if (data.code && data.message)
					{
						showError (data, "passwordExpiredForm");
					}
					else
					{
						showError("Oops, an error occurred, please try again!" ,"loginForm");
					}
				}
				else
				{	
					passwordExpired.loginCallApi($("#passwordExpiredForm input[name=loginId]").val(),
										$("#passwordExpiredForm input[name=newPassword]").val());				
				}
			},
			
			onError: function(data){
				showError(data,"passwordExpiredForm");
			},
			
			callApi: function(loginId, password, newPassword) {
				var data = {
							service: 'user',
							action: 'updateLoginData',
							oldLoginId: loginId,
							password: password,
							newPassword: newPassword	
					};
				callApi(data, this.onSuccess, this.onError, loginId);	
			},

			submitPasswordExpired: function(){
				eraseAllErrors();
				//check that fields are inserted and validate
				var loginId = $("#passwordExpiredForm input[name=loginId]");
				var oldPassword = $("#passwordExpiredForm input[name=oldPassword]");
				var newPassword = $("#passwordExpiredForm input[name=newPassword]");
				var confirmPassword = $("#passwordExpiredForm input[name=confirmPassword]");

				if (!validate.password(oldPassword)){
					showError("Old password was not supplied","passwordExpiredForm");
					return false;
				}
				if (!validate.password(newPassword)){
					showError("New password was not supplied","passwordExpiredForm");
					return false;
				}
				if (!validate.password(confirmPassword)){
					showError("Confirm password was not supplied","passwordExpiredForm");
					return false;
				}
				if (newPassword.val() == confirmPassword.val()){
					passwordExpired.callApi(loginId.val(), oldPassword.val(), newPassword.val());
				}
				else{					
					showError("Passwords do not match", "passwordExpiredForm");	
					return false;							
				}					
				return false;	
			},
			
			loginCallApi: function (loginId, password){
				var data = {
						service: 'user',
						action: 'loginByLoginId',
						loginId: loginId,
						password: password
				};
				callApi(data, this.loginOnSuccess, this.onError);	
			},

			loginOnSuccess: function(data) {
				if (data)
				{
					if (data.code && data.message)
					{										
						showError (data, "loginForm");
					}
					else
					{						
						var ks = data;
						var expiryTime = $("input[name=expiry]").val();
						var url = '<?php echo str_replace ( "https://" , "http://" , $service_url );?>/index.php/kmc/extlogin?ks='+ ks +'&exp=' + expiryTime; 
						window.location = url;
					}
				}
				else
				{				
					showError("Oops, an error occurred, please try again!" ,"loginForm");
				}
			}			
}

var resetPassword = {
			onSuccess: function(data, loginId) {
				if (data)
				{
					if (data.code && data.message)
					{
						showError(data,"forgotPasswordForm");
						showError(data,"newPasswordErrorForm");
					}
					else
					{		
						showError("Oops, an error occurred, please try again!" ,"forgotPasswordForm");
						showError("Oops, an error occurred, please try again!" ,"newPasswordErrorForm");
					}
				}
				else
				{
					alert('Instructions to reset your password have been sent to ' + loginId);
				}
			},
			onError: function(data) {
				showError(data,"forgotPasswordForm");
				showError(data,"newPasswordErrorForm");
			},
			
			callApi: function (loginId){
				var data = {
							service: 'user',
							action: 'resetPassword',
							email: loginId
					};
				callApi(data, this.onSuccess, this.onError, loginId);	
			},
			
			submitForgotPassword: function (){
				eraseAllErrors();
				if (!validate.email($("#forgotPasswordForm input[name=loginId]"))){
					showError("Invalid e-mail");
					return false;
				}
				resetPassword.callApi($("#forgotPasswordForm input[name=loginId]").val());
				return false;
			},

			submitInvalidLink: function (){
				eraseAllErrors();
				if (!validate.email($("#newPasswordErrorForm input[name=loginId]"))){
					return false;
				}
				resetPassword.callApi($("#newPasswordErrorForm input[name=loginId]").val());
				return false;
			}				
}

var setInitialPassword = {
			onSuccess: function(data) {
				if (data)
				{
					if (data.code && data.message)
					{
						showError(data,"newPasswordSuccessForm");
					}
					else
					{		
						showError("Oops, an error occurred, please try again!" ,"newPasswordSuccessForm");
					}
				}
				else
				{
					alert('The new password has been set, please login to Kaltura management console.');
					$("#newPasswordSuccess").fadeOut("slow", function(){ 
						$("#mainLogin").fadeIn();
					});	
					$("#loginForm input[name=loginId]").val('<?php echo $hashKeyLoginId ?>') ;					
				}
			},
			onError: function(data) {
				showError(data,"newPasswordSuccessForm");
			},
			
			callApi: function (newPassword){
				var data = {
							service: 'user',
							action: 'setInitialPassword',
							hashKey : '<?php echo $setPassHashKey; ?>' ,
							newPassword : newPassword
					};
				callApi(data, this.onSuccess, this.onError);	
			},

			submitSetInitialPassword : function (){
				eraseAllErrors();
				var newPassword = $("#newPasswordSuccessForm input[name=newPassword]").val() ;
				var confirmPassword = $("#newPasswordSuccessForm input[name=confirmPassword]").val();
				if (newPassword == confirmPassword){
					setInitialPassword.callApi(newPassword);
				}
				else{
					showError("Passwords do not match", "newPasswordSuccessForm");
				}	
				return false;
			}
}
/*******************
 * click on links function. 
 * Clear all form fields fromcontent
 * Fade out the current div, fade in the div to be shown
 */
var changeCurrentDiv = function (fadeInDiv, fadeOutDiv, clearFields){
	clearFormFields(clearFields);
	$("#"+fadeOutDiv).fadeOut("slow", function(){ 
		eraseAllErrors();
		$("#"+fadeInDiv).fadeIn();
	});	
	return false;
}

//clicks on Links in Forms
$("#loginLink").click(function (){
	return changeCurrentDiv("mainLogin", "forgotPasswordExtension", "forgotPasswordForm");
}),

$("#forgotPasswordLink").click(function (){	
	return changeCurrentDiv("forgotPasswordExtension", "mainLogin", "loginForm");
}),

$("#loginLinkInNewPasswordError").click(function (){
	return changeCurrentDiv("mainLogin", "newPasswordError", "newPasswordErrorForm");
}),	

/*******************
 * Forms submittion 
 */

$("#loginForm").submit( function() {
	login.submitLogin();
	return false;
});

$("#forgotPasswordForm").submit(function (){
	resetPassword.submitForgotPassword();
	return false;
});

$("#newPasswordErrorForm").submit(function (){	
	resetPassword.submitInvalidLink();
	return false;
});

$("#newPasswordSuccessForm").submit(function (){
	setInitialPassword.submitSetInitialPassword();
	return false;
});

$("#passwordExpiredForm").submit(function (){
	passwordExpired.submitPasswordExpired();
	return false;
});	
/**********************
 * show errors on the displayed div
 */
var showError = function (data, divId){
	if (divId){
		str = "#"+divId + " .errorText";
		if (data.message){
			$(str).text(data.message);
		}
		else{
			$(str).text(data);
		}
	}
	else{
		$(".errorText").text(data);
	}
}  


/************************
 * remove all error messages
 */
var eraseAllErrors = function (){
	$(".errorText").text("");
}

/************************
 * remove values from form fields
 */
var clearFormFields = function(formId){
	currentForm = "#"+formId; 
	$(':input', currentForm).each(function() {
		if (this.type == 'text' || this.type == 'password'){
			this.value = "";
			if ($(this).hasClass("missing_field")){
				$(this).removeClass("missing_field");
			}
		}
		if (this.type == 'checkbox'){
			this.checked = false;
		}		
	});
}

var callApi = function(data, onSuccessFunction, onErrorFunction, onSuccessParam){
	var url = "<?php echo $service_url; ?>/api_v3/index.php";
	data.format = 1;
	$.ajax ({
		url: url,
		data: data,
		success: function(data) {
			onSuccessFunction(data, onSuccessParam);
		},
		error: onErrorFunction,
		dataType:"json"		
	});
}

/*******************
 * function that validate email form and password existence.
 */
var validate = {
		email: function (emailField){
			if (emailField.val()== ""){
				emailField.addClass("missing_field");
				return false;
			}
			var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
			if(reg.test(emailField.val()) == false) {
				emailField.addClass("missing_field");
				emailField.attr("title","Wrong Email format");
			    return false;
			}
			emailField.removeClass("missing_field");
			return true;
			
		},
		password: function (passwordField){
			if (passwordField.val()== ""){
				passwordField.addClass("missing_field");
				return false;
			}else{
				passwordField.removeClass("missing_field");
				return true;
			}
		}
}

</script>