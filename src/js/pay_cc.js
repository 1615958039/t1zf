window.onload = function(){
	
	$(".rand").click(function(){
		$(".rand").css("background-image",'');
		$(".rand").css("background-image",'url(randcode.img)');
	});
	
	$('.btn-primary').click(function(){
		if($('.btn-primary').html()=='确认'){
			$('.btn-primary').html('确认中...');
			$.get('pay.do?type=cc&randcode='+$('.form-control').val(),function(res){
				res = JSON.parse(res);
				if(res.code=='1'){
					$('.btn-primary').html('跳转中...');
					setTimeout(function(){
						window.location.reload();
					},1000);
				}else{
					$('.form-control').val('');
					$('.btn-primary').html('确认');
					$(".rand").css("background-image", '');
					$(".rand").css("background-image", 'url(randcode.img)');
					layer.alert(res.message, {icon: 0});
				}
			})
			
		}
	});
	
	
	
}
