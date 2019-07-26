<?php
	include("../function.php");
	if(!$admin)code(["code"=>"-1","message"=>"Cookie已到期请重新登陆！"]);
	$_POST = json_decode(file_get_contents('php://input'),true);
	
	
	if($_POST["type"]=="del"){
		/* 删除用户 */
		$user = $_POST["user"];
		$res = $sql("SELECT * FROM users WHERE user='{$user}' AND admin_id='".$admin['id']."' ");
		if(!$res)code(["code"=>"0","message"=>"无权操作"]);
		
		$s2 =$sql("DELETE FROM users_log_vip WHERE user='{$user}' AND admin_id='".$admin['id']."' ");
		$s3 =$sql("DELETE FROM users_log_money WHERE user='{$user}' AND admin_id='".$admin['id']."' ");
		$s4 =$sql("DELETE FROM users_log_jf WHERE user='{$user}' AND admin_id='".$admin['id']."' ");
		$s5 =$sql("DELETE FROM users_log_custom WHERE user='{$user}' AND admin_id='".$admin['id']."' ");
		$s6 =$sql("DELETE FROM users WHERE user='{$user}' AND admin_id='".$admin['id']."' ");
		$s7 =$sql("DELETE FROM users_log_all WHERE user='{$user}' AND admin_id='".$admin['id']."' ");
		
		
		if($s2 && $s3 && $s4 && $s5 && $s6 && $s7)code(["code"=>"1","message"=>"删除成功！"]);
		else code(["code"=>"0","message"=>"删除失败！"]);
	}else if($_POST["type"]=="fh"){
		$user = $_POST["user"];
		$res = $sql("SELECT * FROM users WHERE user='{$user}' AND admin_id='".$admin['id']."' ");
		if(!$res)code(["code"=>"0","message"=>"无权操作"]);
		if($res['zt']==1)$zt=0;
		else $zt=1;
		$res = $sql("UPDATE  `users` SET zt='{$zt}' WHERE user='{$user}' AND admin_id='".$admin['id']."' ");
		if($res)code(["code"=>"1","message"=>"成功！"]);
		else code(["code"=>"0","message"=>"权限不足！"]);
	}else if($_POST['type']=="getuserinfo"){
		$user = $_POST["user"];
		$res = $sql("SELECT * FROM users WHERE user='{$user}' AND admin_id='".$admin['id']."' ");
		if(!$res)code(["code"=>"0","message"=>"无权操作"]);
		unset($res['pass']);
		unset($res['id']);
		unset($res['admin_id']);
		$rt = $sql("SELECT * FROM users_online WHERE user='{$user}' AND admin_id='".$admin['id']."' ");
		if(!$rt)$res['online']='无在线记录';
		else $res['online']=$rt['addtime'];
		
		if($res['zt']=="1")$res['zt']="封号";
		else $res['zt']="正常";
		
		if($admin['id']=='1031')$res['is_xyy']='1';
		else $res['is_xyy']='0';
		
		code(["code"=>"1","message"=>"获取用户信息成功！","model"=>$res]);
		
	}else if($_POST['type']=='doedu'){
		$user = $_POST["user"];
		$res = $sql("SELECT * FROM users WHERE user='{$user}' AND admin_id='".$admin['id']."' ");
		if(!$res)code(["code"=>"0","message"=>"无权操作"]);
		$users = $res;
		$custom = $_POST['custom'];
		$jf = $_POST['jf'];
		$money = $_POST['money'];
		$newpass = $_POST['newpass'];
		$viptime = $_POST['viptime'];
		if($money=='0' || $money=='0.0' || $money=='0.00'){
		}else if(!is_money($money))code(["code"=>"0","message"=>"请输入正确的金额"]);
		
		if($jf<0 || $jf>999999)code(["code"=>"0","message"=>"积分数值太大！"]);
		if(mb_strlen($custom,'utf-8')>999999)code(["code"=>"0","message"=>"自定义参数字数太大！"]);
		if(strtotime($viptime)<1000)code(["code"=>"0","message"=>"VIP到期日期格式不正确"]);
		if($newpass){
			if(mb_strlen($newpass,'utf-8')>20||mb_strlen($newpass,'utf-8')<1)code(["code"=>"0","message"=>"密码长度为1-20"]);
			$newpass = md5($user.$newpass);
			$res = $sql("UPDATE  `users` SET pass='{$newpass}',jf='{$jf}',money='{$money}',vip='{$viptime}',custom='{$custom}' WHERE user='{$user}' AND admin_id='".$admin['id']."' ");
		}else{
			$res = $sql("UPDATE  `users` SET jf='{$jf}',money='{$money}',vip='{$viptime}',custom='{$custom}' WHERE user='{$user}' AND admin_id='".$admin['id']."' ");
		}
		if($users['custom']!=$custom)$res = $sql("INSERT INTO `users_log_custom` (`admin_id`,`user`,`custom`,`msg`,`addtime`) VALUES ('".$admin['id']."','{$user}','{$custom}','管理员修改','{$date}')");
		if($users['jf']!=$jf)$res = $sql("INSERT INTO `users_log_jf` (`admin_id`,`user`,`num`,`msg`,`addtime`) VALUES ('".$admin['id']."','{$user}','".($jf-$users['jf'])."','管理员修改','{$date}')");
		if($users['money']!=$money)$res = $sql("INSERT INTO `users_log_money` (`admin_id`,`user`,`num`,`msg`,`addtime`) VALUES ('".$admin['id']."','{$user}','".($money-$users['money'])."','管理员修改','{$date}')");
		if($users['vip']!=$viptime){
			if(strtotime($users['vip'])<strtotime("now"))$users['vip']=$date;
			$c = ceil((strtotime($viptime)-strtotime($users['vip']))/86400*10)/10;
			$res = $sql("INSERT INTO `users_log_vip` (`admin_id`,`user`,`num`,`msg`,`addtime`) VALUES ('".$admin['id']."','{$user}','".$c."','管理员修改','{$date}')");
		}
		
		if($res)code(["code"=>"1","message"=>"修改成功！"]);
		else code(["code"=>"0","message"=>"权限不足"]);
	}
	
	$search=$_POST["search"];
	$keyword=$_POST["keyword"];
	$nowpage=$_POST["nowpage"];
	$pagenum=$_POST["pagenum"];
	$orderby=$_POST["orderby"];
	
	if($search=="账号"){
		$where = " WHERE user like '%".$keyword."%' AND admin_id='".$admin['id']."'";
	}else if($search=="imei"){
		$where = " WHERE imei like '%".$keyword."%' AND admin_id='".$admin['id']."'";
	}else if($search=="自定义内容"){
		$where = " WHERE custom like '%".$keyword."%' AND admin_id='".$admin['id']."'";
	}else if($search=="状态"){
		if($keyword=='封号'){
			$where = " WHERE zt='1' AND admin_id='".$admin['id']."'";
		}else{
			$where = " WHERE zt='0' AND admin_id='".$admin['id']."'";
		}
	}else if($search=="注册时间"){
		if(is_set($keyword,"大于")){
			$keyword=sj($keyword."狗东西66","大于","狗东西66");
			if(strtotime($keyword)>999){
				$where = " WHERE reg_time > '".$keyword."' "." AND admin_id='".$admin['id']."'";
			}else code(["code"=>"0","message"=>"时间格式输入错误！"]);
		}else if(is_set($keyword,"小于")){
			$keyword=sj($keyword."狗东西66","小于","狗东西66");
			if(strtotime($keyword)>999){
				$where = " WHERE reg_time < '".$keyword."' "." AND admin_id='".$admin['id']."'";
			}else code(["code"=>"0","message"=>"时间格式输入错误！"]);
		}else if(is_set($keyword,"到")){
			$k1=sj("狗东西66".$keyword,"狗东西66",'到');
			$k2=sj($keyword."狗东西66","到","狗东西66");
			if(strtotime($k1)>999 && strtotime($k2)>999){
				$where = " WHERE reg_time between '".$k1."' AND '".$k2."' "." AND admin_id='".$admin['id']."'";
			}else code(["code"=>"0","message"=>"时间格式输入错误！"]);
		}else code(["code"=>"0","message"=>"时间格式输入错误！"]);
	}else{
		$where = " WHERE admin_id='".$admin['id']."'";
	}
	
	
	if($orderby=="积分升序"){
		$orderby=" ORDER BY jf asc ";
	}else if($orderby=="积分降序"){
		$orderby=" ORDER BY jf desc ";
	}else if($orderby=="余额升序"){
		$orderby=" ORDER BY money asc ";
	}else if($orderby=="余额降序"){
		$orderby=" ORDER BY money desc ";
	}else if($orderby=="会员升序"){
		$orderby=" ORDER BY vip asc ";
	}else if($orderby=="会员降序"){
		$orderby=" ORDER BY vip desc ";
	}else if($orderby=="注册升序"){
		$orderby=" ORDER BY id asc ";
	}else{
		$orderby=" ORDER BY id desc ";
	}
	
	
	$sqlnum = $sql("SELECT count(*) from users ".$where.$orderby);
	if($pagenum=="10" || $pagenum=="30" || $pagenum=="50" || $pagenum=="100"){}else $pagenum=30;
	$maxpage=intval($sqlnum/$pagenum);
	if ($sqlnum%$pagenum)$maxpage++;
	$nowpage=(int)$nowpage;
	if($nowpage=="" || $nowpage<1 || $nowpage>$maxpage)$nowpage=1;
	$offset=$pagenum*($nowpage-1);
	$limit = " LIMIT {$offset},{$pagenum} ";
	
	
	$res = $sql("SELECT * FROM users ".$where.$orderby.$limit,"list");

	$json = array();
	$i=0;
	foreach($res as $val){
		
		if($val['zt']=='1')$zt='封号';
		else $zt='正常';
		$vip = $val['vip'];
		if($vip=='0000-00-00 00:00:00')$vip='未开通';
		else if(strtotime($vip)>strtotime("now")){
			$vip = ceil((strtotime($vip)-strtotime("now"))/86400);
		}else $vip='已到期';
		
		if(mb_strlen($val['custom'],'utf-8')>10)$val['custom']=mb_substr($val['custom'],0,10,'utf-8').'...';
		
		$json[$i] = array(
			'user'=>$val['user'],
			'jf'=>$val['jf'],
			'money'=>$val['money'],
			'vip'=>$vip,
			'regtime'=>$val['reg_time'],
			'money'=>$val['money'],
			'custom'=>$val['custom'],
			'zt'=>$zt,
		);
		$i=$i+1;
	}
	
	code(["code"=>"1","message"=>"获取成功！","data"=>$json,"modle"=>[
		
		'search'=>$_POST["search"],
		'keyword'=>$_POST["keyword"],
		'nowpage'=>$_POST["nowpage"],
		'pagenum'=>$_POST["pagenum"],
		'orderby'=>$_POST["orderby"],
		'maxpage'=>$maxpage,
		'havenum'=>$sqlnum
		
	]]);
	
	
	
	
	