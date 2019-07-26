<?php 
	include("../function.php");
	if(!$admin)code(["code"=>"0","message"=>"请先登陆"]);
	
	$admin = $sql("SELECT * FROM admin WHERE id='".$admin['id']."' ");
	$_SESSION['admin']=$admin;
	
	if($_GET['type']=="get_md5"){
		$text = $_REQUEST['text'];
		echo md5($text);
		die("");
	}else if($_GET['type']=="addfile"){
		//1m => 1048576
		$num = (int)$_GET['num'];
		if($num<1 || $num>200)code(["code"=>"0","message"=>"数量为1-200"]);
		$need = $num*0.5;
		if($admin['money']<$need)code(["code"=>"0","message"=>"余额不足！"]);
		$num=$num*1048576;
		$res = $sql("UPDATE  `admin` SET money=money-'{$need}',filemax=filemax+'{$num}' WHERE id='".$admin['id']."'");
		$res = $sql("INSERT INTO `log_admin_money` (`admin_id`,`money`,`addtime`,`message`) VALUES ('".$admin['id']."','"."-".$need."','{$date}','云盘内存扩容')");	
		if(!$res)code(["code"=>"0","message"=>"扩容失败"]);
		code(["code"=>"1","message"=>"扩容成功！请等待5秒左右生效"]);
		
	}else if($_GET['type']=="adduser"){
		$num = (int)$_GET['num'];
		if($num<1 || $num>90)code(["code"=>"0","message"=>"数量为1-89"]);
		$need = $num*30;
		if($admin['money']<$need)code(["code"=>"0","message"=>"余额不足！"]);
		
		$num=$num*1000;
		$res = $sql("UPDATE  `admin` SET money=money-'{$need}',max_user=max_user+'{$num}' WHERE id='".$admin['id']."'");
		$res = $sql("INSERT INTO `log_admin_money` (`admin_id`,`money`,`addtime`,`message`) VALUES ('".$admin['id']."','"."-".$need."','{$date}','用户系统配额扩容')");	
		if(!$res)code(["code"=>"0","message"=>"扩容失败"]);
		code(["code"=>"1","message"=>"扩容成功！请等待5秒左右生效"]);
		
	}else if($_GET['type']=="addmail"){
		$num = (int)$_GET['num'];
		if($num<1 || $num>10)code(["code"=>"0","message"=>"数量为1-10"]);
		$need = $num*10;
		if($admin['money']<$need)code(["code"=>"0","message"=>"余额不足！"]);
		$num=$num*10000;
		$res = $sql("UPDATE  `admin` SET money=money-'{$need}',max_mail=max_mail+'{$num}' WHERE id='".$admin['id']."'");
		$res = $sql("INSERT INTO `log_admin_money` (`admin_id`,`money`,`addtime`,`message`) VALUES ('".$admin['id']."','"."-".$need."','{$date}','邮箱日志配额扩容')");	
		if(!$res)code(["code"=>"0","message"=>"扩容失败"]);
		code(["code"=>"1","message"=>"扩容成功！请等待5秒左右生效"]);
		
	}else if($_GET['type']=="30"){
		
		for($i=0;$i<30;$i++){
			$n = 30-$i;
			$now = date("Y-m-d",strtotime("-".$n."days"));
			$z = $now." 00:00:00";
			$w = $now." 23:59:59";
			$chart_date[$i] = date("d",strtotime($now));
			$chart_money[$i] = $sql("SELECT SUM(money) FROM pay_order WHERE add_time between '{$z}' AND '{$w}'  AND admin_id='".$admin['id']."' AND ispay='1' ")['SUM(money)'];
			if($chart_money[$i]==0 || $chart_money[$i]==null)$chart_money[$i]="0";
			
			$chart_yesorder[$i] = $sql("SELECT count(*) FROM users_log_all WHERE adddate='{$now}'  AND admin_id='".$admin['id']."' AND dowhat='在线' ");
			$chart_allorder[$i] = $sql("SELECT count(*) FROM users_log_all WHERE adddate='{$now}'  AND admin_id='".$admin['id']."' AND dowhat='签到' ");
			
		}
		code(["code"=>"1","message"=>"获取首页信息成功！",
			"chart_date"=>$chart_date,
			"chart_yesorder"=>$chart_yesorder,
			"chart_allorder"=>$chart_allorder,
			"chart_money"=>$chart_money,
		]);
	}
	
	for($i=0;$i<7;$i++){
		$n = 6-$i;
		$now = date("Y-m-d",strtotime("-".$n."days"));
		$z = $now." 00:00:00";
		$w = $now." 23:59:59";
		$chart_date[$i] = date("d",strtotime($now));
		$chart_money[$i] = $sql("SELECT SUM(money) FROM pay_order WHERE add_time between '{$z}' AND '{$w}'  AND admin_id='".$admin['id']."' AND ispay='1' ")['SUM(money)'];
		if($chart_money[$i]==0 || $chart_money[$i]==null)$chart_money[$i]="0";
		
		$chart_yesorder[$i] = $sql("SELECT count(*) FROM users_log_all WHERE adddate='{$now}'  AND admin_id='".$admin['id']."' AND dowhat='在线' ");
		$chart_allorder[$i] = $sql("SELECT count(*) FROM users_log_all WHERE adddate='{$now}'  AND admin_id='".$admin['id']."' AND dowhat='签到' ");
		
	}

	
	
	
	code(["code"=>"1","message"=>"获取首页信息成功！",
		"chart_date"=>$chart_date,
		"chart_yesorder"=>$chart_yesorder,
		"chart_allorder"=>$chart_allorder,
		"chart_money"=>$chart_money,
		"haveorder"=>$sql("SELECT count(*) FROM pay_order WHERE admin_id='".$admin['id']."'"),
		"haveuser"=>$sql("SELECT count(*) FROM users WHERE admin_id='".$admin['id']."'"),
		"money"=>$sql("SELECT * FROM admin WHERE id='".$admin['id']."' ")['money'],
		"apikey"=>$admin['apikey'],
		"token"=>$admin['token'],
		"online"=>$sql("SELECT count(*) FROM users_log_all WHERE admin_id='".$admin['id']."' AND adddate='".date('Y-m-d')."' AND dowhat='在线' "),
		"max_user"=>$admin['max_user'],
		"max_mail"=>$admin['max_mail'], 
		"havemail"=>$sql("SELECT count(*) FROM smtp_log WHERE admin_id='".$admin['id']."'"),
		"max_file"=>$admin['filemax'],
		
	]);