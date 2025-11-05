$(document).ready(function() {

	//E-mail Ajax Send
	$(".modal-form").submit(function() { //Change
		var th = $(this);
		$.ajax({
		  type: "POST",
		  url: "mail.php", //Change
  
		  data: th.serialize()
		}).done(function() {
		//   alert("Спасибо за заявку! Наш менеджер свяжется с Вами в ближайшее время");
			 //$("button").addClass("popup-with-zoom-anim");
		  setTimeout(function() {
			 // Done Functions
			 th.trigger("reset");
		  window.location = 'good.html';//window.location.href = 'new.php';

		  }, 500);
		  
		});
		
		return false;
	});
  
});
function email_test(input) {
	return !/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,8})+$/.test(input.value);
}


