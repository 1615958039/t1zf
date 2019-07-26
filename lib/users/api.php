<?php
	include("../function.php");
	$key = $_REQUEST['key'];
	$type = $_REQUEST['type'];
	$today = date("Y-m-d");
	
	if(!$key)code(["code"=>"0","message"=>"无管理员key参数"]);
	$admin = $sql("SELECT * FROM admin WHERE apikey='{$key}' ");
	if(!$admin)code(["code"=>"0","message"=>"key错误！不存在该key",]);
	
	$user = $_REQUEST['user'];
	$pass = $_REQUEST['pass'];
	
	
	$imei = $_REQUEST['imei'];
	$token = $_REQUEST['token'];
	$passs = md5($user.$pass);
	$config = $sql("SELECT * FROM users_config WHERE admin_id='".$admin['id']."'");
	if($config['login_imei']=='1'){
		$users = $sql("SELECT * FROM users WHERE admin_id='".$admin['id']."' AND user='{$user}' AND pass='{$passs}' AND imei='{$imei}' ");
		if(!$users)code(["code"=>"0","message"=>"登陆失败，用户账号或密码或imei错误"]);
	}else{
		$users = $sql("SELECT * FROM users WHERE admin_id='".$admin['id']."' AND user='{$user}' AND pass='{$passs}' ");
		if(!$users)code(["code"=>"0","message"=>"登陆失败，用户账号或密码错误"]);
	}
	if($users['zt']==1)code(["code"=>"0","message"=>"该账号已被管理员封号"]);
	
	//在线状态写入
	$res = $sql("SELECT * FROM users_log_all WHERE admin_id='".$admin['id']."' AND dowhat='在线' AND user='{$user}' AND adddate='{$today}' ");
	if(!$res){
		$res = $sql("INSERT INTO `users_log_all` (`admin_id`,`user`,`dowhat`,`addtime`,`adddate`) VALUES ('".$admin['id']."','{$user}','在线','{$date}','{$today}')");
		$res = $sql("SELECT * FROM users_log_all WHERE admin_id='".$admin['id']."' AND dowhat='在线' AND user='{$user}' AND adddate='{$today}' ");
	}
	$res = $sql("UPDATE  `users_log_all` SET addtime='{$date}' WHERE id='".$res['id']."'");
	//end
	
	
	if($type=='info'){
		/* 登陆，获取用户信息   | 签名 -> key+账号+密码+token       */
		
		if(md5($key.$user.$pass.$imei.$admin['token'])!=$token)code(["code"=>"0","message"=>"签名校验失败！"]);	
		
		$qd = $sql("SELECT * FROM users_log_all WHERE admin_id='".$admin['id']."' AND user='".$user."' AND adddate='{$today}' AND dowhat='签到' ");
		if($qd)$isqd="已签到";
		else $isqd="未签到";
		
		if($users['vip']=='0000-00-00 00:00:00'){
			$vip='0';
			$viptime='未开通';
		}else if(strtotime($users['vip'])>strtotime("now")){
			$vip=ceil((strtotime($users['vip'])-strtotime("now"))/86400);
			$viptime=$users['vip'];
		}else{
			$vip='0';
			$viptime='已到期';
		}
		
		code(["code"=>"1","message"=>"登陆成功，获取用户信息成功!",
			"user"=>$user,
			'imei'=>$users['imei'],
			'jf'=>$users['jf'],
			'money'=>$users['money'],
			'vip_day'=>$vip,
			'vip_time'=>$viptime,
			'custom'=>$users['custom'],
			'regtime'=>$users['reg_time'],
			'isqd'=>$isqd,
			
		]);
		
	}
	
	
	
	else if($type=='edu_jf'){
		// 编辑积分  | 签名-> key+user+pass+jf+msg
		
		$jf=(int)$_REQUEST['jf'];
		$msg=$_REQUEST['msg'];
		
		if(md5($key.$user.$pass.$jf.$msg.$admin['token'])!=$token)code(["code"=>"0","message"=>"签名校验失败"]);
		if($jf>99999999 || $jf<-99999999)code(["code"=>"0","message"=>"积分修改值勿过大"]);
		if(!$msg || $msg=='')$msg='用户操作';
		if(mb_strlen($msg,'utf-8')>10)code(["code"=>"0","message"=>"积分修改说明请勿大于10字符"]);
		if($jf<0 && $jf>$users['jf'])code(["code"=>"0","message"=>"积分余额不足"]);
		
		$res = $sql("UPDATE  `users` SET jf='".($users['jf']+$jf)."' WHERE user='$user' AND admin_id='".$admin['id']."' ");
		if(!$res)code(["code"=>"0","message"=>"修改积分失败！"]);
		
		if($jf>0){if(is_set($jf,'+')==FALSE)$jf='+'.$jf;}
		$res = $sql("INSERT INTO `users_log_jf` (`admin_id`,`user`,`num`,`msg`,`addtime`) VALUES ('".$admin['id']."','{$user}','{$jf}','{$msg}','{$date}')");
		
		code(["code"=>"1","message"=>"积分操作成功！"]);
		
	}
	
	
	else if($type=='edu_money'){
		// 编辑积分  | 签名-> key+user+pass+money+msg
		
		$money=$_REQUEST['money'];
		$msg=$_REQUEST['msg'];
		
		if(md5($key.$user.$pass.$money.$msg.$admin['token'])!=$token)code(["code"=>"0","message"=>"签名校验失败"]);
		if(!is_money($money*-1))code(["code"=>"0","message"=>"请提交正确的金额"]);
		if($money>0)code(["code"=>"0","message"=>"用户余额仅允许扣除，暂不支持增加"]);
		if(!$msg || $msg=='')$msg='用户操作';
		if(mb_strlen($msg,'utf-8')>10)code(["code"=>"0","message"=>"余额修改说明请勿大于10字符"]);
		$newmoney = $users['money']+$money; 
		if($newmoney<0)code(["code"=>"0","message"=>"账户余额不足".$users['money']]);
		
		$res = $sql("UPDATE  `users` SET money='{$newmoney}' WHERE user='$user' AND admin_id='".$admin['id']."' ");
		if(!$res)code(["code"=>"0","message"=>"修改余额失败！"]);
		
		$res = $sql("INSERT INTO `users_log_money` (`admin_id`,`user`,`num`,`msg`,`addtime`) VALUES ('".$admin['id']."','{$user}','{$money}','{$msg}','{$date}')");
		
		code(["code"=>"1","message"=>"余额操作成功！"]);
	}
	
	
	else if($type=='edu_custom'){
		// 编辑积分  | 签名-> key+user+pass+custom+msg
		$custom=$_REQUEST['custom'];
		$msg=$_REQUEST['msg'];
		if($config['edu_custom']=='0')code(["code"=>"0","message"=>"修改失败！管理员已关闭修改权限"]);
		if(md5($key.$user.$pass.$custom.$msg.$admin['token'])!=$token)code(["code"=>"0","message"=>"签名校验失败"]);
		if(mb_strlen($custom,'utf-8')>20000)code(["code"=>"0","message"=>"自定义参数在2W字符以内"]);
		if(!$msg || $msg=='')$msg='用户操作';
		if(mb_strlen($msg,'utf-8')>10)code(["code"=>"0","message"=>"余额修改说明请勿大于10字符"]);
		
		$res = $sql("UPDATE  `users` SET custom='{$custom}' WHERE user='$user' AND admin_id='".$admin['id']."' ");
		if(!$res)code(["code"=>"0","message"=>"修改自定义参数失败！"]);
		
		$res = $sql("INSERT INTO `users_log_custom` (`admin_id`,`user`,`custom`,`msg`,`addtime`) VALUES ('".$admin['id']."','{$user}','{$custom}','{$msg}','{$date}')");
		
		code(["code"=>"1","message"=>"自定义参数操作成功！"]);
		
	}
	
	else if($type=='changepass'){
		// 修改密码 | 签名 -> key+user+pass+newpass+token
		$newpass = $_REQUEST['newpass'];
		if(md5($key.$user.$pass.$newpass.$admin['token'])!=$token)code(["code"=>"0","message"=>"签名校验失败"]);
		if(mb_strlen($newpass,'utf-8')>20)code(["code"=>"0","message"=>"新密码勿超过20字符"]);
		if(mb_strlen($newpass,'utf-8')<1)code(["code"=>"0","message"=>"新密码需大于1字符"]);
		$newpass = md5($user.$newpass);
		$res = $sql("UPDATE  `users` SET pass='{$newpass}' WHERE admin_id='".$admin['id']."' AND user='{$user}' ");
		if($res)code(["code"=>"1","message"=>"修改成功！"]);
		else code(["code"=>"0","message"=>"修改失败"]);
	}

	else if($type=='qd'){
		//签名  key+user+pass+token
		if(md5($key.$user.$pass.$admin['token'])!=$token)code(["code"=>"0","message"=>"签名校验失败"]);
		
		if($config['qd_open']=='1')code(["code"=>"0","message"=>"管理员已关闭签到"]);
		
		$res = $sql("SELECT * FROM users_log_all WHERE admin_id='".$admin['id']."' AND user='".$user."' AND adddate='{$today}' AND dowhat='签到' ");
		if($res)code(["code"=>"0","message"=>"今日已签到"]);
		$res = $sql("INSERT INTO `users_log_all` (`admin_id`,`user`,`dowhat`,`addtime`,`adddate`) VALUES ('".$admin['id']."','{$user}','签到','{$date}','{$today}')");
		
		if($config['qd_jf']>0){
			$jf = $config['qd_jf'];
			$res = $sql("UPDATE  `users` SET jf='".($users['jf']+$jf)."' WHERE user='$user' AND admin_id='".$admin['id']."' ");
			$res = $sql("INSERT INTO `users_log_jf` (`admin_id`,`user`,`num`,`msg`,`addtime`) VALUES ('".$admin['id']."','{$user}','{$jf}','签到赠送','{$date}')");
			$jf = "积分(".$jf.")";
		}
		
		if($config['qd_money']>0){
			$money = $config['qd_money'];
			$newmoney = $users['money']+$money; 
			$res = $sql("UPDATE  `users` SET money='{$newmoney}' WHERE user='$user' AND admin_id='".$admin['id']."' ");
			$res = $sql("INSERT INTO `users_log_money` (`admin_id`,`user`,`num`,`msg`,`addtime`) VALUES ('".$admin['id']."','{$user}','{$money}','签到赠送','{$date}')");
			$money = "余额(".$money.")";
		}
		
		if($config['qd_vip']>0){
			$vip = $config['qd_vip'];
			if(strtotime($users['vip'])>strtotime($date)){
				$t="续费";
				$vipday = date("Y-m-d H:i:s",strtotime($users['vip'])+(86400*$vip));
			}else{
				$t="开通";
				$vipday = date("Y-m-d H:i:s",strtotime($date)+(86400*$vip));
			}
			$res = $sql("UPDATE  `users` SET vip='{$vipday}' WHERE user='$user' AND admin_id='".$admin['id']."' ");
			$res = $sql("INSERT INTO `users_log_vip` (`admin_id`,`user`,`num`,`msg`,`addtime`) VALUES ('".$admin['id']."','{$user}','{$vip}','签到赠送','{$date}')");
			$vip = $t."会员(".$vip."天)";
		}
		
		code(["code"=>"1","message"=>"签到成功！已获得奖励".$jf.$money.$vip]);
		
	}
	
	
	else if($type=='mtv'){
		//token -> key+user+pass+num+token
		$num = (int)$_REQUEST['num'];
		if($num>999 || $num<1)code(["code"=>"0","message"=>"兑换数量为1-999"]);
		if($config['mtv_open']==1)code(["code"=>"0","message"=>"管理员已关闭兑换VIP"]);
		
		if(md5($key.$user.$pass.$num.$admin['token'])!=$token)code(["code"=>"0","message"=>"签名校验失败"]);
		
		$needmoney = $config['mtv']*$num;
		if($users['money']<$needmoney)code(["code"=>"0","message"=>"兑换失败，余额不足！"]);
		if(!is_money($needmoney))code(["code"=>"0","message"=>"兑换失败，数量太小"]);
		
		//扣余额记录
		$res = $sql("INSERT INTO `users_log_money` (`admin_id`,`user`,`num`,`msg`,`addtime`) VALUES ('".$admin['id']."','{$user}','"."-".$needmoney."','兑换VIP','{$date}')");
		
		$vip = $num*31;
		if(strtotime($users['vip'])>strtotime($date)){
			$t="续费";
			$vipday = date("Y-m-d H:i:s",strtotime($users['vip'])+(86400*$vip));
		}else{
			$t="开通";
			$vipday = date("Y-m-d H:i:s",strtotime($date)+(86400*$vip));
		}
		
		$res = $sql("INSERT INTO `users_log_vip` (`admin_id`,`user`,`num`,`msg`,`addtime`) VALUES ('".$admin['id']."','{$user}','{$vip}','余额兑换','{$date}')");
		$vip = $t."会员(".$vip."天)";
		
		$res = $sql("UPDATE  `users` SET vip='{$vipday}',money='".($users['money']-$needmoney)."' WHERE user='$user' AND admin_id='".$admin['id']."' ");
		code(["code"=>"1","message"=>"兑换成功！".$vip]);
		
		
	} 
	
	
	else if($type=='mtj'){
		//token -> key+user+pass+num+token
		$num = (int)$_REQUEST['num'];
		if($num>99999 || $num<1)code(["code"=>"0","message"=>"兑换数量为1-99999"]);
		if($config['mtj_open']==1)code(["code"=>"0","message"=>"管理员已关闭兑换积分"]);
		
		if(md5($key.$user.$pass.$num.$admin['token'])!=$token)code(["code"=>"0","message"=>"签名校验失败"]);
		
		$needmoney = ceil((1/$config['mtj'])*$num*100)/100;
		if(!is_money($needmoney))code(["code"=>"0","message"=>"兑换失败，数量太小"]);
		
		if($users['money']<$needmoney)code(["code"=>"0","message"=>"兑换失败，余额不足！"]);
		
		//扣余额记录
		$res = $sql("INSERT INTO `users_log_money` (`admin_id`,`user`,`num`,`msg`,`addtime`) VALUES ('".$admin['id']."','{$user}','"."-".$needmoney."','兑换积分','{$date}')");
		$res = $sql("INSERT INTO `users_log_jf` (`admin_id`,`user`,`num`,`msg`,`addtime`) VALUES ('".$admin['id']."','{$user}','{$num}','余额兑换','{$date}')");
		
		$res = $sql("UPDATE  `users` SET jf=jf+'{$num}',money='".($users['money']-$needmoney)."' WHERE user='$user' AND admin_id='".$admin['id']."' ");
		code(["code"=>"1","message"=>"兑换成功！获得积分(".$num.")"]);
		
		
	}
	
	
	else if($type=="cj"){
		//签名  key+user+pass+token
		if(md5($key.$user.$pass.$admin['token'])!=$token)code(["code"=>"0","message"=>"签名校验失败"]);
		if($config['cj_open']=='1')code(["code"=>"0","message"=>"管理员已关闭抽奖"]);
		if($config['cj_isvip']=='1'){
			if(strtotime($users['vip'])<strtotime($date))code(["code"=>"0","message"=>"抽奖失败！权限不足"]);
		}
		$qdnum = $sql("SELECT count(*) FROM users_choujiang WHERE cjtime between '".$today." 00:00:00"."' AND '".$today." 23:59:59"."' AND user='{$user}' AND admin_id='".$admin['id']."' ");
		if($qdnum>=$config['cj_daynum'])code(["code"=>"0","message"=>"今天抽奖次数已达上限"]);
		$jp = $sql("SELECT * FROM users_choujiang WHERE admin_id='".$admin['id']."' AND cjtime='0000-00-00 00:00:00' order by rand() limit 1");
		if(!$jp)code(["code"=>"0","message"=>"奖品已被抽完"]);
		
		if($config['cj_needwhat']=='余额'){
			if($config['cj_needvalue']>$users['money'])code(["code"=>"0","message"=>"抽奖失败！余额不足"]);
			$res = $sql("UPDATE  `users` SET money=money-'".$config['cj_needvalue']."' WHERE user='$user' AND admin_id='".$admin['id']."' ");
			$res = $sql("INSERT INTO `users_log_money` (`admin_id`,`user`,`num`,`msg`,`addtime`) VALUES ('".$admin['id']."','{$user}','-".$config['cj_needvalue']."','兑换抽奖','{$date}')");
		}else if($config['cj_needwhat']=='积分'){
			if($config['cj_needvalue']!='0'){
				if($config['cj_needvalue']>$users['jf'])code(["code"=>"0","message"=>"抽奖失败！积分不足"]);
				$res = $sql("UPDATE  `users` SET jf=jf-'".$config['cj_needvalue']."' WHERE user='$user' AND admin_id='".$admin['id']."' ");
				$res = $sql("INSERT INTO `users_log_jf` (`admin_id`,`user`,`num`,`msg`,`addtime`) VALUES ('".$admin['id']."','{$user}','-".$config['cj_needvalue']."','兑换抽奖','{$date}')");
			}
		}
		
		
		if($jp['dowhat']=='积分'){
			$jf = $jp['dovalue'];
			$res = $sql("UPDATE  `users` SET jf='".($users['jf']+$jf)."' WHERE user='$user' AND admin_id='".$admin['id']."' ");
			$res = $sql("INSERT INTO `users_log_jf` (`admin_id`,`user`,`num`,`msg`,`addtime`) VALUES ('".$admin['id']."','{$user}','{$jf}','抽奖获得','{$date}')");
			$a = "积分(".$jf.")";
		}else if($jp['dowhat']=='余额'){
			$money = $jp['dovalue'];
			$newmoney = $users['money']+$money; 
			$res = $sql("UPDATE  `users` SET money='{$newmoney}' WHERE user='$user' AND admin_id='".$admin['id']."' ");
			$res = $sql("INSERT INTO `users_log_money` (`admin_id`,`user`,`num`,`msg`,`addtime`) VALUES ('".$admin['id']."','{$user}','{$money}','抽奖获得','{$date}')");
			$a = "余额(".$money.")";
		}else if($jp['dowhat']=='vip'){
			$vip = $jp['dovalue'];
			if(strtotime($users['vip'])>strtotime($date)){
				$t="续费";
				$vipday = date("Y-m-d H:i:s",strtotime($users['vip'])+(86400*$vip));
			}else{
				$t="开通";
				$vipday = date("Y-m-d H:i:s",strtotime($date)+(86400*$vip));
			}
			$res = $sql("UPDATE  `users` SET vip='{$vipday}' WHERE user='$user' AND admin_id='".$admin['id']."' ");
			$res = $sql("INSERT INTO `users_log_vip` (`admin_id`,`user`,`num`,`msg`,`addtime`) VALUES ('".$admin['id']."','{$user}','{$vip}','抽奖获得','{$date}')");
			$a = $t."会员(".$vip."天)";
		}
		
		$res = $sql("UPDATE  `users_choujiang` SET user='{$user}',cjtime='{$date}' WHERE id='".$jp['id']."' ");
		
		code(["code"=>"1","message"=>"抽奖成功！恭喜您抽中[".$jp['msg']."]获得奖品:".$a]);
		
	}

	
	else if($type=='cj_list'){
		// token -> key+user+pass+token 
		if(md5($key.$user.$pass.$admin['token'])!=$token)code(["code"=>"0","message"=>"签名校验失败"]);
		$num=$sql("SELECT count(*) FROM users_choujiang WHERE user='{$user}' AND admin_id='".$admin['id']."'");
		if($num==0)code(["code"=>"0","message"=>"没有抽奖记录！","num"=>$num,"data"=>"{}"]);
		$res = $sql("SELECT * FROM users_choujiang WHERE user='{$user}' AND admin_id='".$admin['id']."' ORDER BY id DESC","list");
		$i=0;
		foreach($res as $val){
			
			if($val['dowhat']=='余额'){
				$val['dovalue']="余额:".$val['dovalue']."";
			}else if($val['dowhat']=="积分"){
				$val['dovalue']="积分:".$val['dovalue']."";
			}else{
				$val['dovalue']="会员:".$val['dovalue']."";
			}
			
			$data[$i++] = [
				"name"=>$val['msg'],
				"value"=>$val['dovalue'],
				"time"=>$val['cjtime']
			]; 
		}
		code(["code"=>"1","message"=>"获取记录成功！","num"=>$num,"data"=>$data]);
		
		
	}

	
	else if($type=='money_log'){
		
		$nowpage=$_REQUEST["nowpage"];
		$pagenum=$_REQUEST["pagenum"];
		if(md5($key.$user.$pass.$nowpage.$pagenum.$admin['token'])!=$token)code(["code"=>"0","message"=>"签名校验失败"]);
		
		$where = " WHERE  admin_id='".$admin['id']."' AND user='".$user."'";
		$orderby=" ORDER BY id desc ";
		$sqlnum = $sql("SELECT count(*) from users_log_money ".$where.$orderby);
		if($pagenum=="10" || $pagenum=="30" || $pagenum=="50" || $pagenum=="100"){}else $pagenum=30;
		$maxpage=intval($sqlnum/$pagenum);
		if ($sqlnum%$pagenum)$maxpage++;
		$nowpage=(int)$nowpage;
		if($nowpage=="" || $nowpage<1 || $nowpage>$maxpage)$nowpage=1;
		$offset=$pagenum*($nowpage-1);
		$limit = " LIMIT {$offset},{$pagenum} ";
		$res = $sql("SELECT * FROM users_log_money ".$where.$orderby.$limit,"list");
		$json = array();
		$i=0;
		foreach($res as $val){
			if($val['num']>0)$val['num'] = '+'.$val['num'];
			$json[$i++] = [
				'value'=>$val['num'],
				'msg'=>$val['msg'],
				'time'=>$val['addtime']
			];
		}
		code([
			"code"=>"1","message"=>"获取成功！",
			'nowpage'=>$nowpage,
			'pagenum'=>$pagenum,
			'maxpage'=>$maxpage,
			'havenum'=>$sqlnum,
			"data"=>$json,
		]);
	}

	
	else if($type=='jf_log'){
		
		$nowpage=$_REQUEST["nowpage"];
		$pagenum=$_REQUEST["pagenum"];
		if(md5($key.$user.$pass.$nowpage.$pagenum.$admin['token'])!=$token)code(["code"=>"0","message"=>"签名校验失败"]);
		
		$where = " WHERE  admin_id='".$admin['id']."' AND user='".$user."'";
		$orderby=" ORDER BY id desc ";
		$sqlnum = $sql("SELECT count(*) from users_log_jf ".$where.$orderby);
		if($pagenum=="10" || $pagenum=="30" || $pagenum=="50" || $pagenum=="100"){}else $pagenum=30;
		$maxpage=intval($sqlnum/$pagenum);
		if ($sqlnum%$pagenum)$maxpage++;
		$nowpage=(int)$nowpage;
		if($nowpage=="" || $nowpage<1 || $nowpage>$maxpage)$nowpage=1;
		$offset=$pagenum*($nowpage-1);
		$limit = " LIMIT {$offset},{$pagenum} ";
		$res = $sql("SELECT * FROM users_log_jf ".$where.$orderby.$limit,"list");
		$json = array();
		$i=0;
		foreach($res as $val){
			if($val['num']>0)$val['num'] = '+'.$val['num'];
			$json[$i++] = [
				'value'=>$val['num'],
				'msg'=>$val['msg'],
				'time'=>$val['addtime']
			];
		}
		code([
			"code"=>"1","message"=>"获取成功！",
			'nowpage'=>$nowpage,
			'pagenum'=>$pagenum,
			'maxpage'=>$maxpage,
			'havenum'=>$sqlnum,
			"data"=>$json,
		]);
	}

	
	else if($type=='vip_log'){
		
		$nowpage=$_REQUEST["nowpage"];
		$pagenum=$_REQUEST["pagenum"];
		if(md5($key.$user.$pass.$nowpage.$pagenum.$admin['token'])!=$token)code(["code"=>"0","message"=>"签名校验失败"]);
		
		$where = " WHERE  admin_id='".$admin['id']."' AND user='".$user."'";
		$orderby=" ORDER BY id desc ";
		$sqlnum = $sql("SELECT count(*) from users_log_vip ".$where.$orderby);
		if($pagenum=="10" || $pagenum=="30" || $pagenum=="50" || $pagenum=="100"){}else $pagenum=30;
		$maxpage=intval($sqlnum/$pagenum);
		if ($sqlnum%$pagenum)$maxpage++;
		$nowpage=(int)$nowpage;
		if($nowpage=="" || $nowpage<1 || $nowpage>$maxpage)$nowpage=1;
		$offset=$pagenum*($nowpage-1);
		$limit = " LIMIT {$offset},{$pagenum} ";
		$res = $sql("SELECT * FROM users_log_vip ".$where.$orderby.$limit,"list");
		$json = array();
		$i=0;
		foreach($res as $val){
			if($val['num']>0)$val['num'] = '+'.$val['num'];
			$json[$i++] = [
				'value'=>$val['num'],
				'msg'=>$val['msg'],
				'time'=>$val['addtime']
			];
		}
		code([
			"code"=>"1","message"=>"获取成功！",
			'nowpage'=>$nowpage,
			'pagenum'=>$pagenum,
			'maxpage'=>$maxpage,
			'havenum'=>$sqlnum,
			"data"=>$json,
		]);
	}

	
	
