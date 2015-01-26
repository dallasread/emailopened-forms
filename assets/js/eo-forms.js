(function($) {
	function IsEmail(email) {
	  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	  return regex.test(email);
	}
	
	$(document).on("click", ".eo-embedded-subscribe-form .captcha_imgs img", function() {
		var form = $(this).closest(".eo-embedded-subscribe-form");
		var verifier = $(this).attr("src").split("img_hash=")[1];
		form.find("[name='verifier']").val(verifier);
		form.find("img").removeClass("selected");
		$(this).addClass("selected");
		return false;
	});
	
	$(document).on("submit", ".eo-embedded-subscribe-form", function() {
		var form = $(this);
		var email = form.find("[name='contact[details][email-address]']");
		var response = form.find(".eo_response");
		
    if (IsEmail(email.val()))
    {
      var token = form.attr("data-token");
			response.html("<p class='eo_notice eo_response_block'>Submitting...</p>").hide().fadeIn();
			
      $.ajax({
        type: "POST",
        url: form.attr("action"),
        data: form.serialize() 
      }).done(function(data) {
				if (data.indexOf("confirm your submission") != -1) {
					response.prependTo(form);
					form.find('.field, input[type="submit"], .captcha').hide();
					form.closest(".widget").find('.eo_description').hide();
					response.html("<p class='eo_success eo_response_block'>We have sent you a confirmation email.</p>").hide().fadeIn();
				} else {
					response.html("<p class='eo_error eo_response_block'>Please select the correct image to prove you're human.</p>").hide().fadeIn();
				}
      }).fail(function() {
        response.html("<p class='eo_error eo_response_block'>Error. Please Try again.</p>").hide().fadeIn();
      });
    }
    else
    {
      response.html("<p class='eo_error eo_response_block'>Invalid email address.</p>").hide().fadeIn();
    }

		return false;
  });
})(jQuery);