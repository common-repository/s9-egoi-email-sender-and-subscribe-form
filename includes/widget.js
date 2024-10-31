jQuery(document).ready(function() {
	jQuery('form#s9egoi-widget-form').submit(function() {
		var postForm = true;
		var messages = Array();
		if (jQuery('#s9egoi-form_email').length > 0) {
			if (s9egoiValidateEmail(jQuery('#s9egoi-form_email')) == false) {
				postForm = false;
				messages.push('EMAIL_ADDRESS_INVALID');
			}
		}
		
		if (jQuery('#s9egoi-form_first_name').length > 0) {
			if (s9egoiValidateFirstName(jQuery('#s9egoi-form_first_name')) == false) {
				postForm = false;
				messages.push('FIRST_NAME_INVALID');
			}
		}
		
		if (jQuery('#s9egoi-form_last_name').length > 0) {
			if (s9egoiValidateLastName(jQuery('#s9egoi-form_last_name')) == false) {
				postForm = false;
				messages.push('LAST_NAME_INVALID');
			}
		}
		
		if (jQuery('#s9egoi-form_cellphone').length > 0) {
			if (s9egoiValidateCellphone(jQuery('#s9egoi-form_cellphone')) == false) {
				postForm = false;
				messages.push('CELLPHONE_INVALID');
			}
		}
		
		if (jQuery('#s9egoi-form_telephone').length > 0) {
			if (s9egoiValidateTelephone(jQuery('#s9egoi-form_telephone')) == false) {
				postForm = false;
				messages.push('TELEPHONE_INVALID');
			}
		}
		
		if (jQuery('#s9egoi-form_fax').length > 0) {
			if (s9egoiValidateFax(jQuery('#s9egoi-form_fax')) == false) {
				postForm = false;
				messages.push('FAX_INVALID');
			}
		}
		
		if (jQuery('#s9egoi-form_address').length > 0) {
			if (s9egoiValidateAddress(jQuery('#s9egoi-form_address')) == false) {
				postForm = false;
				messages.push('ADDRESS_INVALID');
			}
		}
		
		if (jQuery('#s9egoi-form_zip_code').length > 0) {
			if (s9egoiValidateZipCode(jQuery('#s9egoi-form_zip_code')) == false) {
				postForm = false;
				messages.push('ZIP_CODE_INVALID');
			}
		}
		
		if (jQuery('#s9egoi-form_city').length > 0) {
			if (s9egoiValidateCity(jQuery('#s9egoi-form_city')) == false) {
				postForm = false;
				messages.push('CITY_INVALID');
			}
		}
		
		if (jQuery('#s9egoi-form_district').length > 0) {
			if (s9egoiValidateDistrict(jQuery('#s9egoi-form_district')) == false) {
				postForm = false;
				messages.push('DISTRICT_INVALID');
			}
		}
		
		if (jQuery('#s9egoi-form_state').length > 0) {
			if (s9egoiValidateState(jQuery('#s9egoi-form_state')) == false) {
				postForm = false;
				messages.push('STATE_INVALID');
			}
		}
		
		if (jQuery('#s9egoi-form_age').length > 0) {
			if (s9egoiValidateAge(jQuery('#s9egoi-form_age')) == false) {
				postForm = false;
				messages.push('AGE_INVALID');
			}
		}
		
		if (jQuery('#s9egoi-form_birth_date').length > 0) {
			if (s9egoiValidateBirthDate(jQuery('#s9egoi-form_birth_date')) == false) {
				postForm = false;
				messages.push('BIRTH_DATE_INVALID');
			}
		}
		
		if (jQuery('#s9egoi-form_gender').length > 0) {
			if (s9egoiValidateGender(jQuery('#s9egoi-form_gender')) == false) {
				postForm = false;
				messages.push('GENDER_INVALID');
			}
		}
		
		if (jQuery('#s9egoi-form_company').length > 0) {
			if (s9egoiValidateCompany(jQuery('#s9egoi-form_company')) == false) {
				postForm = false;
				messages.push('COMPANY_INVALID');
			}
		}
		
		if (messages.length > 0) {
			jQuery('#s9egoi-widget-message').html('').addClass('error');
			jQuery.each(messages, function(index,value) {
				jQuery.post('/wp-admin/admin-ajax.php', {action:'s9egoi_message', message:value}, function(str) { jQuery('#s9egoi-widget-message').append('' + str + '<br/>'); });
			});
		}
		if (postForm == true) {
			var formData = jQuery(this).serialize();
			jQuery.post(
				"/wp-admin/admin-ajax.php", 
				formData,
				function(str) {
					if (str != 'SUCCESS') {
						jQuery('#s9egoi-widget-message').html('').addClass('error');
						
					} else {
						jQuery('#s9egoi-widget-message').html('');
					}
					jQuery.post('/wp-admin/admin-ajax.php', {action:'s9egoi_message', message:str}, function(str) { jQuery('#s9egoi-widget-message').append('' + str + '<br/>'); });
				}
			);
		}
		return false;
	});
});

function s9egoiValidateEmail(field) {
	if (jQuery(field).hasClass('required')) { if (jQuery(field).val() == '') { return false; } }
	var regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z.]{2,5}$/;
	return(regex.test(jQuery(field).val())); 
}

function s9egoiValidateFirstName(field) {
	if (jQuery(field).hasClass('required')) { if (jQuery(field).val() == '') { return false; } }
	var regex = /^[a-zA-Z_-]{3,30}$/;
	return(regex.test(jQuery(field).val())); 
}

function s9egoiValidateLastName(field) {
	if (jQuery(field).hasClass('required')) { if (jQuery(field).val() == '') { return false; } }
	var regex = /^[a-zA-Z_-]{3,30}$/;
	return(regex.test(jQuery(field).val())); 
}

function s9egoiValidateCellphone(field) {
	if (jQuery(field).hasClass('required')) { if (jQuery(field).val() == '') { return false; } }
	var regex = /^[0-9+_-]{5,30}$/;
	return(regex.test(jQuery(field).val())); 
}

function s9egoiValidateTelephone(field) {
	if (jQuery(field).hasClass('required')) { if (jQuery(field).val() == '') { return false; } }
	var regex = /^[0-9+_-]{5,30}$/;
	return(regex.test(jQuery(field).val())); 
}

function s9egoiValidateFax(field) {
	if (jQuery(field).hasClass('required')) { if (jQuery(field).val() == '') { return false; } }
	var regex = /^[0-9+_-]{5,30}$/;
	return(regex.test(jQuery(field).val())); 
}

function s9egoiValidateAddress(field) {
	if (jQuery(field).hasClass('required')) { if (jQuery(field).val() == '') { return false; } }
	return true;	
}

function s9egoiValidateZipCode(field) {
	if (jQuery(field).hasClass('required')) { if (jQuery(field).val() == '') { return false; } }
	var regex = /^[0-9_-]{4,15}$/;
	return(regex.test(jQuery(field).val())); 
}

function s9egoiValidateCity(field) {
	if (jQuery(field).hasClass('required')) { if (jQuery(field).val() == '') { return false; } }
	return true;	
}

function s9egoiValidateState(field) {
	if (jQuery(field).hasClass('required')) { if (jQuery(field).val() == '') { return false; } }
	return false;	
}

function s9egoiValidateDistrict(field) {
	if (jQuery(field).hasClass('required')) { if (jQuery(field).val() == '') { return false; } }
	return true;	
}

function s9egoiValidateAge(field) {
	if (jQuery(field).hasClass('required')) { if (jQuery(field).val() == '') { return false; } }
	var regex = /^[0-9]{1,3}$/;
	return(regex.test(jQuery(field).val())); 
}

function s9egoiValidateBirthDate(field) {
	if (jQuery(field).hasClass('required')) { if (jQuery(field).val() == '') { return false; } }
	var regex = /^\d\d\d?\d?\-\d\d?\-\d\d?$/;
	return(regex.test(jQuery(field).val())); 
}

function s9egoiValidateGender(field) {
	if (jQuery(field).hasClass('required')) { if (jQuery(field).val() == '') { return false; } }
	return true;	
}

function s9egoiValidateCompany(field) {
	if (jQuery(field).hasClass('required')) { if (jQuery(field).val() == '') { return false; } }
	return true;	
}
