jQuery(document).on('keyup','#homepage_signup_username',function(){
	if( jQuery(this).val().length > 2 ) {
		jQuery.get(JHB_AJAX+'user-available/'+jQuery(this).val()+'/',null,
			function(response){
				data = jQuery.parseJSON(response);
				if(data){
					jQuery('#homepage_signup_username').parents('div.form-group').removeClass('has-error').addClass('has-success')
					jQuery('#homepage_signup_username_help').html('');
				} else {
					jQuery('#homepage_signup_username').parents('div.form-group').removeClass('has-success').addClass('has-error')  
					jQuery('#homepage_signup_username_help').html('Brukernavnet er opptatt');
				}
			}
		);
	} else {
		jQuery('#homepage_signup_username').parents('div.form-group').removeClass('has-success').addClass('has-error')  
		jQuery('#homepage_signup_username_help').html('Brukernavnet må inneholde minst 3 tegn');
	}
});

jQuery(document).on('keyup','#homepage_signup_password', function(){
  // at least one number, one lowercase and one uppercase letter, at least six characters
  var re = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}/;
  var result = re.test(jQuery(this).val());
  
	if( result ) {
		jQuery('#homepage_signup_password').parents('div.form-group').removeClass('has-error').addClass('has-success');
		jQuery('#homepage_signup_password_help').html('');
	} else {
		jQuery('#homepage_signup_password').parents('div.form-group').removeClass('has-success').addClass('has-error');
		jQuery('#homepage_signup_password_help').html('Bruk minst 6 tegn, hvorav en liten bokstav, en stor bokstav og et tall.');
	}
});

jQuery(document).on('submit', '#JHBsignup', function(e) {
	var validated = true;
	console.log(jQuery(this).children('div.form-group'));
	jQuery(this).children('div.form-group').each(function(){
		if(jQuery(this).hasClass('has-error')) {
			validated = false;
		}
	});
	
	if( validated ) {
		return true;
//		jQuery(this).submit();
	} else {
		e.preventDefault();
		alert('Ett eller flere felt er ikke godkjent (se rød hake)');
		return false;
	}
});

jQuery(document).ready(function(){
	jQuery('#homepage_signup_email').verimail({
		messageElement:'#homepage_signup_email_help',
		prefixName: 'has',
		language: 'nb',
		statusElement: jQuery('#homepage_signup_email').parents('div.form-group')
	});
});