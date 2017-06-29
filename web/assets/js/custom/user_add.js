$(function(){
	$('.show_username_password').on('change',function(){
		setTimeout(function(){
			$('.uk-form-stacked').submit();
		}, 500);
	});
});

