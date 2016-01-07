(function(){
	$('[data-modal]').click(function(){
		var modal = $(this).attr("data-modal");
		$("#" + modal).fadeIn();
		$("body").append('<div class="darkmatter"></div>');
		$(".darkmatter").fadeIn();
	});

	$("body").on("click", ".closebutton, .darkmatter, .cancelmodal",function(){
		$(".window").fadeOut();
		$(".darkmatter").fadeOut('normal', function(){
			$(this).remove();
		});
	});
})();
