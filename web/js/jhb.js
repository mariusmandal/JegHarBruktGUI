$(document).ready(function () {
	$('[data-toggle=offcanvas]').click(function () {
			$('.row-offcanvas').toggleClass('active')
	});
});


$(document).on('click','#homepage_button_logon',function(){
 $('#homepage_tron_content').hide();
 $('#homepage_form_logon').fadeIn();
 $('#homepage_logon_username').focus();
});

$(document).on('click','#homepage_logon_cancel', function(){
 $('#homepage_form_logon').hide();
 $('#homepage_tron_content').fadeIn();
});

$(document).on('click','#homepage_button_signup',function(){
 $('#homepage_tron_content').hide();
 $('#homepage_form_signup').fadeIn();
 $('#homepage_signup_username').focus();
});

$(document).on('click','#homepage_signup_cancel', function(){
 $('#homepage_form_signup').hide();
 $('#homepage_tron_content').fadeIn();
});