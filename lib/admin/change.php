<?php
	include("../function.php");
	if(!$admin)code(["code"=>"0","message"=>"请先登陆账号"]);
	$_POST = json_decode(file_get_contents('php://input'),true);
	$pass = $_POST["pass"];
	$newpass = $_POST["newpass"];
	$newpas2 = $_POST["newpas2"];
	if($pass == '' || mb_strlen($pass,'utf-8')<6 || mb_strlen($pass,'utf-8')>16)code(["code"=>"0","message"=>"旧密码格式错误"]);
	if($newpass == '' || mb_strlen($newpass,'utf-8')<6 || mb_strlen($newpass,'utf-8')>16)code(["code"=>"0","message"=>"新密码格式错误"]);
	if($newpas2 != $newpass)code(["code"=>"0","message"=>"两次输入的新密码不一致"]);
	if($pass == $newpass)code(["code"=>"0","message"=>"新密码不与旧密码相同"]);
	
	if(!$_SESSION["change_i"])$_SESSION["change_i"]=1;
	if($_SESSION['change_i']>5){
		$_SESSION["admin"]='';
		$_SESSION['change_i']=1;
		code(["code"=>"0","message"=>"修改密码失败！请重新登录"]);
	}
	
	$user_info = $sql("SELECT * from admin_info WHERE admin_id='".$admin['id']."' ");
	
	
	if(substr(md5(md5($user_info['reg_time'].$pass."杰哥哥")),8,16)!=$admin['pass']){
		$_SESSION["change_i"]= $_SESSION["change_i"]+1;
		code(["code"=>"0","message"=>"旧密码输入错误！"]);
	}
	$pass = substr(md5(md5($user_info['reg_time'].$newpass."杰哥哥")),8,16);
	$res = $sql("update  `admin` set pass='$pass' where id='".$admin['id']."' ");
	if($res){
		$_SESSION["admin"] = '';
		code(["code"=>"1","message"=>"修改密码成功，请重新登陆"]);
	}else code(["code"=>"0","message"=>"修改密码失败！"]);

?>