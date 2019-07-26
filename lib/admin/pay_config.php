<?php
	include("../function.php");
	if(!$admin)code(["code"=>"-1","message"=>"登陆状态失效"]);
	$_POST = json_decode(file_get_contents('php://input'),true);
	$type=$_POST["type"];
	
	if($_GET['type']=="edu_sysmsg"){
		$res = $sql("SELECT * FROM pay_config WHERE admin_id='".$admin['id']."' ");
		if($res['sysmsg']==1){
			$a=0;
		}else{
			$a=1;
		}
		$res = $sql("UPDATE  `pay_config` SET sysmsg='{$a}' WHERE admin_id='".$admin['id']."'");
		if($res)code(["code"=>"1","message"=>"修改成功！"]);
	}
	
	
	if($_GET['type']=="edu_ismail"){
		$res = $sql("SELECT * FROM pay_config WHERE admin_id='".$admin['id']."' ");
		if($res['ismail']==1){
			$aa=0;
		}else{
			$aa=1;
			$who = "admin";
			$title = "支付系统收款通知";
			$text = "这是一封来自后台服务器的测试邮件，当您看到这封邮件时，表示您的接口已配置正常！感谢您对我们的支持";
			$res = $sql("SELECT * FROM smtp_config WHERE admin_id='".$admin['id']."' ");
			if($who=='admin')$who = $res['adminmail'];
			if(!$res || !$res['smtp'])code(["code"=>"0","message"=>"请先配置接口"]);
			$a = smtp($res['smtp'],$res['port'],$res['isssl'],$res['name'],$res['user'],$res['pass'],$title,$text,$who);
			if(!$a)code(["code"=>"0","message"=>"开启失败！测试邮件发送失败！"]);
		}
		$res = $sql("INSERT INTO `smtp_log` (`admin_id`,`add_time`,`add_ip`,`add_title`,`add_text`,`add_who`) VALUES ('".$admin['id']."','{$date}','".ip()."','{$title}','{$text}','{$who}')");
		$res = $sql("UPDATE  `pay_config` SET ismail='{$aa}' WHERE admin_id='".$admin['id']."'");
		if($res)code(["code"=>"1","message"=>"修改成功！"]);
	}
	
	
	
	
	if($type=='' || !$type){	//初始化页面
	
		$epay=array();
		$mpay=array();
		$model=array();
		
		$config = $sql("SELECT * from pay_config WHERE admin_id='".$admin['id']."' ");
		if(!$config){
			$res = $sql("insert into `pay_config` (`admin_id`) values ('".$admin['id']."')");
			$config = $sql("SELECT * from pay_config WHERE admin_id='".$admin['id']."' ");
		}
		if($config['e_api']=="" || $config['e_pid']=='' || $config['e_key']==''){
			//易支付需要初始化
			$epay['zt']='0';
			$epay['e_api']='';
			$epay['e_pid']='';
			$epay['e_key']='';
		}else{
			$epay['zt']='1';
			$epay['e_api']=$config['e_api'];
			$epay['e_pid']=$config['e_pid'];
			$epay['e_key']=$config['e_key'];
		}
		if($config['m_pid']=='' || $config['m_key']==''){
			//码支付需要初始化
			$mpay['zt']='0';
			$mpay['m_pid']='';
			$mpay['m_key']='';
		}else{
			$mpay['zt']='1';
			$mpay['m_pid']=$config['m_pid'];
			$mpay['m_key']=$config['m_key'];
		}
		
		$model['qqpay']=$config['qqpay'];
		$model['wxpay']=$config['wxpay'];
		$model['alipay']=$config['alipay'];
		$model['rt']=$config['return_mb'];
		$model['sysmsg']=$config['sysmsg'];
		$model['ismail']=$config['ismail'];
		
		code(["code"=>"1","message"=>"获取成功！",'epay'=>$epay,'mpay'=>$mpay,'model'=>$model]);
	}
	else if($type=="epay_edu"){
		$api=htmlentities($_POST['e_api']);
		$pid=htmlentities($_POST["e_pid"]);
		$key=htmlentities($_POST["e_key"]);
		if(mb_strlen($key,'utf-8')<1 || mb_strlen($key,'utf-8')>32)code(["code"=>"0","message"=>"key长度为1-32"]);
		if(!file_get_contents($api))code(["code"=>"0","message"=>"该支付接口无法访问！"]);
		if(mb_strlen($api,'utf-8')>100)code(["code"=>"0","message"=>"支付接口太长，请压缩"]);
		if($pid<1000 || $pid>9999999)code(["code"=>"0","message"=>"pid为数字！"]);
		$res = $sql("UPDATE  `pay_config` SET e_key='{$key}',e_pid='{$pid}',e_api='{$api}' WHERE admin_id='".$admin['id']."'");
		if($res)code(["code"=>"1","message"=>"保存成功！"]);
		else code(["code"=>"0","message"=>"数据写入失败！"]);
	}
	
	
	else if($type=="mpay_edu"){
		$pid=htmlentities($_POST["m_pid"]);
		$key=htmlentities($_POST["m_key"]);
		if(mb_strlen($key,'utf-8')<1 || mb_strlen($key,'utf-8')>32)code(["code"=>"0","message"=>"key长度为1-32位"]);
		if($pid<1000 || $pid>9999999)code(["code"=>"0","message"=>"pid为数字！"]);
		$res = $sql("UPDATE  `pay_config` SET m_key='{$key}',m_pid='{$pid}' WHERE admin_id='".$admin['id']."'");
		if($res)code(["code"=>"1","message"=>"保存成功！"]);
		else code(["code"=>"0","message"=>"数据写入失败！"]);
	}

	
	else if($type=='edu_wxpay'){
		$val = $_POST["val"];
		if($val=="epay" || $val=="mpay" || $val=='off'){
			$res = $sql("UPDATE  `pay_config` SET wxpay='{$val}' WHERE admin_id='".$admin['id']."'");
			if($res)code(["code"=>"1","message"=>"修改成功！"]);
			else code(["code"=>"0","message"=>"修改失败！"]);
		}else code(["code"=>"0","message"=>"选你妈逼呢"]);
	}
	
	else if($type=='edu_qqpay'){
		$val = $_POST["val"];
		if($val=="epay" || $val=="mpay" || $val=='off'){
			$res = $sql("UPDATE  `pay_config` SET qqpay='{$val}' WHERE admin_id='".$admin['id']."'");
			if($res)code(["code"=>"1","message"=>"修改成功！"]);
			else code(["code"=>"0","message"=>"修改失败！"]);
		}else code(["code"=>"0","message"=>"选你妈逼呢"]);
	}
	
	
	else if($type=='edu_alipay'){
		$val = $_POST["val"];
		if($val=="epay" || $val=="mpay" || $val=='off'){
			$res = $sql("UPDATE  `pay_config` SET alipay='{$val}' WHERE admin_id='".$admin['id']."'");
			if($res)code(["code"=>"1","message"=>"修改成功！"]);
			else code(["code"=>"0","message"=>"修改失败！"]);
		}else code(["code"=>"0","message"=>"选你妈逼呢"]);
	}
	
	
	
	else if($type=='edu_rt'){
		$val = $_POST["val"];
		if($val=="0"){
			$res = $sql("UPDATE  `pay_config` SET return_mb='{$val}' WHERE admin_id='".$admin['id']."'");
			if($res)code(["code"=>"1","message"=>"修改成功！"]);
			else code(["code"=>"0","message"=>"修改失败！"]);
		}else code(["code"=>"0","message"=>"选你妈逼呢"]);
	}
	