<?php
	include("../function.php");
	$type = $_GET["type"];
	$_POST = json_decode(file_get_contents('php://input'),true);
	
	function r($num){
		$a = ($num + 123456781232) * 11;
		$a = str_replace('1','r',$a);
		$a = str_replace('2','t',$a);
		$a = str_replace('3','g',$a);
		$a = str_replace('4','a',$a);
		$a = str_replace('5','x',$a);
		$a = str_replace('6','m',$a);
		$a = str_replace('7','p',$a);
		$a = str_replace('8','q',$a);
		$a = str_replace('9','k',$a);
		$a = str_replace('0','z',$a);
		return $a;
	}
	function dr($num){
		$a = $num;
		$a = str_replace('r','1',$a);
		$a = str_replace('t','2',$a);
		$a = str_replace('g','3',$a);
		$a = str_replace('a','4',$a);
		$a = str_replace('x','5',$a);
		$a = str_replace('m','6',$a);
		$a = str_replace('p','7',$a);
		$a = str_replace('q','8',$a);
		$a = str_replace('k','9',$a);
		$a = str_replace('z','0',$a);
		$a = ($a / 11) - 123456781232;
		return $a;
	}
	
	
	
	if($type=="reg_getTelcode"){
		
		$user = htmlentities($_POST["user"]);
		$pass = $_POST["pass"];
		$randcode = $_POST["randcode"];
		$token = $_POST['token'];
		
		$user = dr($user);
		
		if($user<10000000000 || $user>20000000000)code(["code"=>"0","message"=>"手机号码格式错误，请重新输入"]);
		if(mb_strlen($pass,'utf-8')<6 || mb_strlen($pass,'utf-8')>16)code(["code"=>"0","message"=>"密码长度为6到16个字符"]);
		if($_SESSION["code_i"]>1)code(["code"=>"0","message"=>"请重新输入图形验证码"]);
		$_SESSION["code_i"]=$_SESSION["code_i"]+1;
		if($randcode!=$_SESSION["code"]){code(["code"=>"2","message"=>"图形验证码输入错误"]);}
		
		if($token != md5($_POST["user"]."1杰2"))code(["code"=>"0","message"=>"发送验证码失败！请更换浏览器再试"]);
		
		//判断IP是否限制,单IP限制发3条短信
		$res = $sql("SELECT count(*) from log_admin_sms WHERE ip='".ip()."'");
		if($res > 2)code(["code"=>"0","message"=>"发送短信失败"]);
		
		
		
		
		
		
		
		
		//判断手机号是否已被注册
		$res = $sql("SELECT * from admin WHERE user='$user' ");
		if($res)code(["code"=>"0","message"=>"该手机号已注册"]);
		$_SESSION['code'] = rand(10000,99999);
		$reg_telcode = rand(100000,999999);
		if(sms($user,$reg_telcode)){
			$_SESSION["reg_telcode"]=$reg_telcode;
			$_SESSION["reg_user"]=$user;
			$_SESSION["reg_pass"]=$pass;
			$_SESSION["code"]=rand(1,999999);//重设验证码
			
			$res = $sql("insert into `log_admin_sms` (`addtime`,`ip`,`tel`) values ('{$date}','".ip()."','{$user}')");
			if(!$res)code(["code"=>"0","message"=>"验证码发送失败！"]);
			$_SESSION['reg_i']=1;
			code(["code"=>"1","message"=>"短信验证码发送成功！"]);
		}else{
			code(["code"=>"0","message"=>"短信验证码发送失败！"]);
		}
	}
	else if($type=="reg_yes"){
		$telcode = $_GET['telcode'];
		if($telcode=="")code(["code"=>"0","message"=>"请输入短信验证码"]);
		if($telcode!=$_SESSION['reg_telcode']){
			if(!$_SESSION['reg_i'])code(["code"=>"0","message"=>"短信验证码错误！"]);
			if($_SESSION['reg_i']>5)code(["code"=>"0","message"=>"请重新获取短信验证码"]);
			$_SESSION['reg_i']=$_SESSION['reg_i']+1;
			code(["code"=>"0","message"=>"短信验证码错误！"]);
		}
		if($_SESSION["reg_telcode"]=="")code(["code"=>"0","message"=>"注册失败！"]);
		if($_SESSION["reg_user"]=="")code(["code"=>"0","message"=>"注册失败！"]);
		if($_SESSION["reg_pass"]=="")code(["code"=>"0","message"=>"注册失败！"]);
		if($_SESSION["code"]=="")code(["code"=>"0","message"=>"注册失败！"]);
		
		//判断手机号是否已被注册
		$res = $sql("SELECT * from admin WHERE user='".$_SESSION["reg_user"]."' ");
		if($res)code(["code"=>"0","message"=>"该手机号已注册"]);
		
		$pass = substr(md5(md5($date.$_SESSION["reg_pass"]."杰哥哥")),8,16);
		$key = substr(md5("杰哥哥".$_SESSION['reg_user'].rand("1","99999").$date),8,16);
		$token = md5("杰哥哥".strtotime("now").date("Y-m-d H:i:s").rand("1","999999999"));
		
		
		$res = $sql("insert into `admin` (`user`,`pass`,`apikey`,`money`,`token`) values ('".$_SESSION["reg_user"]."','{$pass}','{$key}','".$reg_money."','{$token}')");
		
		
		$admin_id = mysqli_insert_id($dbconnect);
		$res = $sql("insert into `users_config` (`admin_id`) values ('{$admin_id}')");
		
		
		$ip = ip();
		$city=@file_get_contents("http://ip.ws.126.net/ipquery?ip=".$ip);
		$city=iconv('GB2312', 'UTF-8', $city);
	    $city=sj($city,'city:"','"');
	   	if(!$city)$city="未知";
		
		$res2 = $sql("insert into `admin_info` (`admin_id`,`reg_time`,`reg_ip`,`reg_city`) values ('{$admin_id}','{$date}','{$ip}','{$city}')");
		
		
		if($res && $res2){
			$_SESSION['reg_telcode']=rand("1","9999999");
			code(["code"=>"1","message"=>"注册成功！请返回登陆"]);
		}
		
		code(["code"=>"0","message"=>"注册失败！"]);
		
		
		
		
		
		
		
		
	}
	

	else if($type=="forget_getTelcode"){
		$user = htmlentities($_POST["user"]);
		$randcode = $_POST["randcode"];
		if($user<10000000000 || $user>20000000000)code(["code"=>"0","message"=>"手机号码格式错误，请重新输入"]);
		
		if($_SESSION["code_i"]>1)code(["code"=>"0","message"=>"请重新输入图形验证码"]);
		$_SESSION["code_i"]=$_SESSION["code_i"]+1;
		if($randcode!=$_SESSION["code"]){code(["code"=>"2","message"=>"图形验证码输入错误"]);}
		
		
		
		//判断IP是否限制,单IP限制发3条短信
		$res = $sql("SELECT count(*) from log_admin_sms WHERE ip='".ip()."'");
		if($res > 2)code(["code"=>"0","message"=>"发送短信失败"]);
		//判断手机号是否已注册
		$res = $sql("SELECT * from admin WHERE user='$user' ");
		if(!$res)code(["code"=>"0","message"=>"该手机号还未注册"]);
		
		$_SESSION['code'] = rand(10000,99999);
		$forget_telcode = rand(100000,999999);
		if(sms($user,$forget_telcode)){
			$_SESSION["forget_telcode"]=$forget_telcode;
			$_SESSION["forget_user"]=$user;
			$_SESSION["code"]=rand(1,999999);//重设验证码
			
			$res = $sql("insert into `log_admin_sms` (`addtime`,`ip`,`tel`) values ('{$date}','".ip()."','{$user}')");
			if(!$res)code(["code"=>"0","message"=>"验证码发送失败！"]);
			$_SESSION['forget_i']=1;
			code(["code"=>"1","message"=>"短信验证码发送成功！"]);
		}else{
			code(["code"=>"0","message"=>"短信验证码发送失败！"]);
		}
	}
	else if($type=="forget_yes"){
		$user=$_POST["user"];
		$pass=$_POST["pass"];
		$pas2=$_POST["pas2"];
		$telcode=$_POST["telcode"];
		if($pas2!=$pass)code(["code"=>"0","message"=>"两次输入的密码不同"]);
		if($_SESSION["forget_user"]!=$user)code(["code"=>"0","message"=>"注册失败！"]);
		if($telcode=="")code(["code"=>"0","message"=>"请输入短信验证码"]);
		if($telcode!=$_SESSION['forget_telcode']){
			if(!$_SESSION['forget_i'])code(["code"=>"0","message"=>"短信验证码错误！"]);
			if($_SESSION['forget_i']>5)code(["code"=>"0","message"=>"请重新获取短信验证码"]);
			$_SESSION['forget_i']=$_SESSION['forget_i']+1;
			code(["code"=>"0","message"=>"短信验证码错误！"]);
		}
		if($_SESSION["forget_telcode"]=="")code(["code"=>"0","message"=>"注册失败！"]);
		if($_SESSION["forget_user"]=="")code(["code"=>"0","message"=>"注册失败！"]);
		if($_SESSION["code"]=="")code(["code"=>"0","message"=>"注册失败！"]);
		
		//判断手机号是否已被注册
		$users = $sql("SELECT * from admin WHERE user='".$_SESSION["forget_user"]."' ");
		if(!$users)code(["code"=>"0","message"=>"该手机号还未注册"]);
		
		$user_info = $sql("SELECT * from admin_info WHERE admin_id='".$users['id']."' ");
		
		$pass = substr(md5(md5($user_info['reg_time'].$pass."杰哥哥")),8,16);
		$res = $sql("update  `admin` set pass='$pass' where user='$user' ");
		
		$ip = ip();
		$city=@file_get_contents("http://ip.ws.126.net/ipquery?ip=".$ip);
		$city=iconv('GB2312', 'UTF-8', $city);
	    $city=sj($city,'city:"','"');
	   	if(!$city)$city="未知";
		
		$res2 = $sql("insert into `log_admin_sys` (`admin_id`,`addtime`,`dowhat`,`message`) values ('".$users['id']."','{$date}','重设密码','"."ip:".$ip."(".$city.")"."')");
		
		
		if($res && $res2){
			$_SESSION['reg_telcode']=rand("1","9999999");
			code(["code"=>"1","message"=>"重设密码成功！请返回登陆"]);
		}
		
		code(["code"=>"0","message"=>"重设失败！"]);
		
		
		
		
		
		
		
		
	}
	

else if($type=="login"){
	if($_SESSION["admin"])code(["code"=>"3","message"=>"您已登陆"]);
	$data=$_POST["data"];
	$data = base64_decode(base64_decode(base64_decode(base64_decode(base64_decode($data)))));
	$length = mb_strlen($data,'UTF8');
	
	$code = mb_substr($data,$length-4);
	$user = htmlentities(mb_substr($data,0,11));
	$pass = mb_substr($data,11,$length-15);
	
	if($_SESSION["code_i"]>1)code(["code"=>"2","message"=>"请重新输入图形验证码"]);
	$_SESSION["code_i"]=$_SESSION["code_i"]+1;
	if($code!=$_SESSION["code"]){code(["code"=>"2","message"=>"图形验证码输入错误"]);}
	
	$users = $sql("SELECT * from admin WHERE user='".$user."' ");
	if(!$users)code(["code"=>"0","message"=>"账号或密码错误"]);	
	$user_info = $sql("SELECT * from admin_info WHERE admin_id='".$users['id']."' ");
	
	$pass = substr(md5(md5($user_info['reg_time'].$pass."杰哥哥")),8,16);
	
	
	
	$admin = $sql("SELECT * from admin WHERE user='".$user."' AND pass='".$pass."' ");
	if($admin){
		//写入登陆日志
		$ip = ip();
		$city=@file_get_contents("http://ip.ws.126.net/ipquery?ip=".$ip);
		$city=iconv('GB2312', 'UTF-8', $city);
	    $city=sj($city,'city:"','"');
	   	if(!$city)$city="未知";
		if(is_mobile())$type="手机";
		else $type="电脑";
		
		$res2 = $sql("insert into `log_admin_sys` (`admin_id`,`addtime`,`dowhat`,`message`) values ('".$users['id']."','{$date}','登陆','"."设备:".$type."  ip:".$ip."(".$city.")"."')");
		if($res2){
			$_SESSION["admin"] = $admin;
			code(["code"=>"1","message"=>"登陆成功！"]);
			
		}
		else code(["code"=>"0","message"=>"登陆失败！"]);
	}else{
		code(["code"=>"2","message"=>"账号或密码错误"]);
	}
	
	
	
	
}









	