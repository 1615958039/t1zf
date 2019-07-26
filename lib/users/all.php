<?php
	include("../function.php");
	$key = $_REQUEST['key'];
	$type = $_REQUEST['type'];
	$token = $_REQUEST['token'];
	
	if(!$key)code(["code"=>"0","message"=>"无管理员key参数"]);
	$admin = $sql("SELECT * FROM admin WHERE apikey='{$key}' ");
	if(!$admin)code(["code"=>"0","message"=>"key错误！不存在该key",]);
	
	if($type=='sendmail'){
		//加密参数 key+title+text+who+token
		$title = $_REQUEST['title'];
		$text = $_REQUEST['text'];
		$who = $_REQUEST['who'];
		if($title && $text && $who){}else code(["code"=>"0","message"=>"请检查提交数据的完整性"]);
		
		$res = $sql("SELECT * FROM smtp_log WHERE admin_id='".$admin['id']."' ORDER BY id DESC LIMIT 0,1 ");
		if(strtotime($res['add_time'])+10>strtotime("now"))code(["code"=>"0","message"=>"请等待".(10-(strtotime("now")-strtotime($res['add_time'])))."秒后重试!"]);
		if($sql("SELECT count(*) FROM smtp_log WHERE admin_id='".$admin['id']."'")>=$admin['max_mail'])code(["code"=>"0","message"=>"内存已满，请联系管理员清理邮件内存"]);
		
		if(mb_strlen($title,'utf-8')>20)code(["code"=>"0","message"=>"标题字符在20以内"]);
		if(mb_strlen($text,'utf-8')>300)code(["code"=>"0","message"=>"邮件内容仅限300字符"]);
		if(mb_strlen($who,'utf-8')>25)code(["code"=>"0","message"=>"请输入正确的收件人地址"]);
		if(md5($key.$title.$text.$who.$admin['token'])!=$token)code(["code"=>"0","message"=>"Token校验失败"]);
		$res = $sql("SELECT * FROM smtp_config WHERE admin_id='".$admin['id']."' ");
		if($who=='admin')$who = $res['adminmail'];
		$a = smtp($res['smtp'],$res['port'],$res['isssl'],$res['name'],$res['user'],$res['pass'],$title,$text,$who);
		if($a){
			$res = $sql("INSERT INTO `smtp_log` (`admin_id`,`add_time`,`add_ip`,`add_title`,`add_text`,`add_who`) VALUES ('".$admin['id']."','{$date}','".ip()."','{$title}','{$text}','{$who}')");
			code(["code"=>"1","message"=>"发件成功！"]);
		}else code(["code"=>"0","message"=>"发件失败！"]);
	}
	
	else if($type=="online"){
		$online = $sql("SELECT count(*) FROM users_log_all WHERE admin_id='".$admin['id']."' AND  addtime>'".date("Y-m-d H:i:s",strtotime($date)-1800)."' AND dowhat='在线' ");
		$today_online = $sql("SELECT count(*) FROM users_log_all WHERE admin_id='".$admin['id']."' AND  adddate='".date("Y-m-d")."' AND dowhat='在线' ");
		$alluser = $sql("SELECT count(*) FROM users WHERE admin_id='".$admin['id']."'");
		$today_qd = $sql("SELECT count(*) FROM users_log_all WHERE admin_id='".$admin['id']."' AND  adddate='".date("Y-m-d")."' AND dowhat='签到' ");
		code(["code"=>"1","message"=>"获取成功！","online"=>$online,"today_online"=>$today_online,"alluser"=>$alluser,"today_qd"=>$today_qd]);	
	}
	
	else if($type=="getpay"){
		$orderid = $_GET['orderid'];
		$token = $_GET['token'];
		if(md5($admin['apikey'].$orderid.$admin['token'])!=$token)code(["code"=>"0","message"=>"token校验失败~"]);
		if(!$orderid)code(["code"=>"0","message"=>"请提交orderid"]);
		$res = $sql("SELECT * FROM pay_order WHERE orderid='{$orderid}' AND admin_id='".$admin['id']."' ");
		if(!$res)code(["code"=>"0","message"=>"无该订单记录"]);
		$config = $sql("SELECT * FROM users_config WHERE admin_id='".$admin['id']."' ");
		if($res['pay_time']=="0000-00-00 00:00:00")$res['pay_time']="";
		if($res['do_time']=="0000-00-00 00:00:00")$res['do_time']="";
		$payc = $sql("SELECT * FROM pay_config WHERE admin_id='".$admin['id']."' ");
		if($payc['sysmsg']!='1')$res['do_msg']="";
		if($res['goodsid']>0)$isg=1;else $isg=0;
		code([
			"code"=>"1",
			"message"=>"获取成功",
			"orderid"=>$res['orderid'],
			"paytype"=>$res['paytype'],
			"money"=>$res['money'],
			"addmsg"=>$res['add_msg'],
			"addtime"=>$res['add_time'],
			"ispay"=>$res['ispay'],
			"paytime"=>$res['pay_time'],
			"isgoods"=>$isg,
			"isdo"=>$res['isdo'],
			"dotime"=>$res['do_time'],
			"domsg"=>$res['do_msg']
		]);
	}

	else if($type=="text"){
		
		$id = textlistdecode($_REQUEST["id"]);
		$res = $sql("SELECT * FROM textlist WHERE id='{$id}' AND admin_id='".$admin['id']."' ");
		if(!$res)code(["code"=>"0","message"=>"获取失败！"]);
		
		$log = $sql("SELECT * FROM download_log WHERE ip='".ip()."' AND fileid='{$id}' AND istext='1' ");
		if(strtotime($log['addtime'])+86400<strtotime("now") || !$log){
			$down = $sql("INSERT INTO `download_log` (`ip`,`addtime`,`fileid`,`istext`) VALUES ('".ip()."','{$date}','{$id}','1')");
			$up = $sql("UPDATE  `textlist` SET see=see+1 WHERE id='{$id}' ");
		}
		code(["code"=>"1","message"=>"获取云文档信息成功！","see"=>$res['see'],"text"=>$res['textinfo']]);
	}

	else if($type=='get_km'){
		$orderid = $_REQUEST['orderid'];
		if(md5($key.$orderid.$admin['token'])!=$token)code(["code"=>"0","message"=>"token校验失败！"]);
		if(!$orderid)code(["code"=>"0","message"=>"请提交订单号！"]);
		$res = $sql("SELECT * FROM pay_order WHERE id='{$orderid}' AND admin_id='".$admin['id']."' ");
		if($res['isdo']==1){
			code(["code"=>"1","message"=>"获取成功！","km"=>$res['do_msg']]);
		}else if($res['ispay']==1){
			code(["code"=>"0","message"=>"系统正在安排发货！"]);
		}else{
			code(["code"=>"0","message"=>"无发货记录"]);
		}
	}
	
	
	else if($type=='get_goods_info'){
		$goodsid = $_REQUEST['goodsid'];
		if(md5($key.$goodsid.$admin['token'])!=$token)code(["code"=>"0","message"=>"token校验失败！"]);
		if(!$goodsid)code(["code"=>"0","message"=>"请提交商品ID！"]);
		$res = $sql("SELECT * FROM pay_goods WHERE id='{$goodsid}' AND admin_id='".$admin['id']."' ");
		if(!$res)code(["code"=>"0","message"=>"获取失败！"]);
		
		if($res['dowhat']=='自动发货'){
			$res['sell'] = $sql("SELECT count(*) FROM pay_goods_km WHERE goodsid='{$goodsid}' AND issell='1' ");
			$res['havenum'] = $sql("SELECT count(*) FROM pay_goods_km WHERE goodsid='{$goodsid}' AND issell='0' ");
		}
		
		if($res['zt']==1)$res['zt']='下架';else $res['zt']='正常';
		
		code(["code"=>"1",
			"message"=>"获取商品信息成功！",
			"title"=>$res['title'],
			"money"=>$res['money'],
			"dowhat"=>$res['dowhat'],
			"havenum"=>$res['havenum'],
			"sell"=>$res['sell'],
			"zt"=>$res['zt']
		]);
	}
	
	
	else if($type=='telboom_add'){
		
		$tel = $_REQUEST['tel'];
		$num = $_REQUEST['num'];
		if(md5($key.$tel.$num.$admin['token'])!=$token)code(["code"=>"0","message"=>"token校验失败！"]);
		
		if($admin['id']=='1004'){
			
			if($num!='10')code(["code"=>"0","message"=>"由于接口需要付费，请勿使用测试账号添加轰炸任务。请更换key和token为您本人的"]);
		}
		
		if($num!='10' && $num!='50' && $num!='100' && $num!= '200' && $num!='400' && $num!='600')code(["code"=>"0","message"=>"请提交正确次数"]);
		if($tel>20000000000 || $tel<10000000000)code(["code"=>"0","message"=>"手机号码不正确"]);
		$t = $sql("SELECT * FROM telboom WHERE tel='{$tel}' AND num>'0' ");
		if($t)code(["code"=>"0","message"=>"该手机号码上一条任务还未执行完！"]);
		
		$bmd = $sql("SELECT * FROM telboom_bmd WHERE tel='{$tel}' ");
		if($bmd)$num2 = '0';
		else $num2 = $num;
		
		
		$money = $num*0.0001;
		$admin = $sql("SELECT * FROM admin WHERE id='".$admin['id']."' ");
		if($admin['money']<$money)code(["code"=>"0","message"=>"添加失败-管理员余额不足"]);
		$res = $sql("UPDATE  `admin` SET money=money-'{$money}' WHERE id='".$admin['id']."'");
		$i = rand("0","60");
		$res = $sql("INSERT INTO `telboom` (`admin_id`,`tel`,`num`,`i`,`addtime`,`addnum`) VALUES ('".$admin['id']."','{$tel}','{$num2}','{$i}','{$date}','{$num}')");
		if($res)code(["code"=>"1","message"=>"添加成功！"]);
		else code(["code"=>"0","message"=>"添加失败！"]);
		
	}

	else if($type=='telboom_sel'){
		$tel = $_REQUEST['tel'];
		if(md5($key.$tel.$admin['token'])!=$token)code(["code"=>"0","message"=>"token校验失败！"]);
		$res = $sql("SELECT * FROM telboom WHERE admin_id='".$admin['id']."' AND tel='{$tel}' ORDER BY id desc ");
		if(!$res)code(["code"=>"0","message"=>"无记录！"]);
		if($res['num']=='0')$zt="已完成";
		else if($res['num']==$val['addnum'])$zt='等待开始';
		else $zt = "已执行".ceil((1-($res['num']/$res['addnum']))*100)."%";
		code(["code"=>"1","message"=>"获取成功！",'zt'=>$zt]);
	}

	else if($type=='telboom_stop'){
		$tel = $_REQUEST['tel'];
		if(md5($key.$tel.$admin['token'])!=$token)code(["code"=>"0","message"=>"token校验失败！"]);
		$res = $sql("SELECT * FROM telboom WHERE admin_id='".$admin['id']."' AND tel='{$tel}' ORDER BY id desc ");
		if(!$res)code(["code"=>"0","message"=>"无记录！"]);
		$res = $sql("UPDATE  `telboom` SET num='0' WHERE id='".$res['id']."'");
		if($res)code(["code"=>"1","message"=>"暂停成功！"]);
		else code(["code"=>"0","message"=>"暂停失败！"]);
	}
	
	else if($type=='bmd_add'){
		
		if($admin['id']=='1004')code(["code"=>"0","message"=>"该key为测试接口，无法操作需要扣除余额的接口。请替换key和token再试"]);
		
		$tel = $_REQUEST['tel'];
		if(md5($key.$tel.$admin['token'])!=$token)code(["code"=>"0","message"=>"token校验失败！"]);
		if($tel>20000000000 || $tel<10000000000)code(["code"=>"0","message"=>"手机号码不正确"]);
		$t = $sql("SELECT * FROM telboom_bmd WHERE tel='{$tel}' AND admin_id='".$admin['id']."' ");
		if($t)code(["code"=>"0","message"=>"该号码已在白名单保护内"]);
		$money = '0.01';
		$admin = $sql("SELECT * FROM admin WHERE id='".$admin['id']."' ");
		if($admin['money']<$money)code(["code"=>"0","message"=>"添加失败，余额不足 ".$money." 元"]);
		$res = $sql("UPDATE  `admin` SET money=money-'{$money}' WHERE id='".$admin['id']."'");
		$res = $sql("INSERT INTO `telboom_bmd` (`admin_id`,`tel`,`addtime`) VALUES ('".$admin['id']."','{$tel}','{$date}')");
		if($res)code(["code"=>"1","message"=>"添加成功！"]);
		else code(["code"=>"0","message"=>"添加失败！"]);
	}
	
	else if($type=='bmd_del'){
		if($admin['id']=='1004')code(["code"=>"0","message"=>"该key为测试接口，无法操作需要扣除余额的接口。请替换key和token再试"]);
		$tel = $_REQUEST["tel"];
		if(md5($key.$tel.$admin['token'])!=$token)code(["code"=>"0","message"=>"token校验失败！"]);
		$res = $sql("DELETE FROM telboom_bmd WHERE admin_id='".$admin['id']."' AND tel='{$tel}' ");
		if($res)code(["code"=>"1","message"=>"删除成功！"]);
		else code(["code"=>"0","message"=>"删除失败，该号码未在白名单内"]);
	}
	
	
	else if($type=='bmd_sel'){
		$tel = $_REQUEST["tel"];
		if(md5($key.$tel.$admin['token'])!=$token)code(["code"=>"0","message"=>"token校验失败！"]);
		$bmd = $sql("SELECT * FROM telboom_bmd WHERE tel='{$tel}' ");
		if($bmd)code(["code"=>"1","message"=>"是白名单手机号"]);
		else code(["code"=>"0","message"=>"非白名单手机号"]);
	}

	else if($type=="jsonp"){
		//$_POST = json_decode(file_get_contents('php://input'),true);
		var_dump($_POST);
	}