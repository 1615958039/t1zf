<?php
	include("../function.php");
	if(!$admin)code(["code"=>"-1","message"=>"请先登陆账号"]);
	
	
	$type=$_REQUEST['type'];
	
	if(!$type){
		//首页请求获取登陆的用户数据
		
		$usersnum=$sql("SELECT count(*) FROM users WHERE admin_id='".$admin['id']."'");
		
		
		
		code(["code"=>"1","message"=>"yes",
			'user'=>$admin['user'],
			'usersnum'=>usersnum($usersnum),
		
		
		]);
	}
	
	
	if($type=="logout"){	//推出登陆
		$admin = '';
		$_SESSION['admin']='';
	}
