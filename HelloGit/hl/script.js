//
//	jQuery Validate example script
//
//	Prepared by David Cochran
//
//	Free for your use -- No warranties, no guarantees!
//

$(document).ready(function(){

	// Validate
	// http://bassistance.de/jquery-plugins/jquery-plugin-validation/
	// http://docs.jquery.com/Plugins/Validation/
	// http://docs.jquery.com/Plugins/Validation/validate#toptions

	$('#hl7-form').validate({
	    rules: {
	      patient_id: {
			digits: true,
	        required: true
	      },
	      uuid: {
	      	minlength: 2,
	        required: true
	      },
	      modality_code: {
	        minlength: 2,
	        required: true
	      },
		  exam_date: {
			dateTime: true,
	        required: true
	      },
		  study_date: {
			dateTime: true,
	        required: true
	      }
	    },
		highlight: function(element) {
			$(element).closest('.control-group').removeClass('success').addClass('error');
		},
		success: function(element) {
			element
			.text('OK!').addClass('valid')
			.closest('.control-group').removeClass('error').addClass('success');
		}
	});
	
	$('#hl7_form').on('submit', function(e){
		e.preventDefault();
		$('#submit_button').attr('disabled', ''); // disable upload button
        //show uploading message
		$("#upload_result").html('<div style="padding:10px"><img src="assets/img/ajax-loader.gif" alt="Please Wait"/> <span>Uploading...</span></div>');
		$("#upload_result").hide();
		$("#upload_result").fadeIn(1000);
		$(this).ajaxSubmit({
            target:		'#upload_result',
        	success:	afterSuccess //call function after success
    	});
    });    
}); // end document.ready

function afterSuccess(){
    $('#hl7_form').resetForm();  // reset form
    $('#submit_button').removeAttr('disabled'); //enable submit button
}