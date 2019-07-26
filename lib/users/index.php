<?php
	include("../function.php");
	$key = $_REQUEST['key'];
	$type = $_REQUEST['type'];
	
	if(!$key)code(["code"=>"0","message"=>"无管理员key参数"]);
	$admin = $sql("SELECT * FROM admin WHERE apikey='{$key}' ");
	if(!$admin)code(["code"=>"0","message"=>"key错误！不存在该key",]);
	
		
		/* 注册账号 | 签名 -> key+账号+密码+imei+自定义参数+token   */
		$user = $_REQUEST['user'];
		$pass = $_REQUEST['pass'];
		$imei = $_REQUEST['imei'];
		$custom = $_REQUEST['custom'];
		$token = $_REQUEST["token"];
		
		$config = $sql("SELECT * FROM users_config WHERE admin_id='".$admin['id']."' ");
		if($config['reg_open']=='1')code(["code"=>"0","message"=>"管理员已关闭注册！"]);
		
		if(mb_strlen($user,'utf-8')>20)code(["code"=>"0","message"=>"注册账号勿超过20字符"]);
		if(mb_strlen($user,'utf-8')<1)code(["code"=>"0","message"=>"注册账号需大于1字符"]);
		if(mb_strlen($pass,'utf-8')>20)code(["code"=>"0","message"=>"注册密码勿超过20字符"]);
		if(mb_strlen($pass,'utf-8')<1)code(["code"=>"0","message"=>"注册密码需大于1字符"]);
		if(mb_strlen($imei,'utf-8')>20)code(["code"=>"0","message"=>"注册imei勿超过20字符"]);
		if(mb_strlen($custom,'utf-8')>20000)code(["code"=>"0","message"=>"注册自定义内容勿超过20000字符"]);
		
		if(mb_strlen($imei,'utf-8')<1 && $config['reg_onlyimei']==1)code(["code"=>"0","message"=>"请提交imei号"]);
		
		if(md5($key.$user.$pass.$imei.$custom.$admin['token'])!=$token)code(["code"=>"0","message"=>"token失效(请检查加密参数与顺序)"]);
		
		$isreg = $sql("SELECT * FROM users WHERE admin_id='".$admin['id']."' AND user='{$user}' ");
		if($isreg)code(["code"=>"0","message"=>"该账号已被注册"]);
		
		
		
		$res = $sql("SELECT * FROM users WHERE admin_id='".$admin['id']."' ORDER BY id DESC LIMIT 0,1 ");
		if(strtotime($res['reg_time'])+10>strtotime("now"))code(["code"=>"0","message"=>"请等待".(10-(strtotime("now")-strtotime($res['reg_time'])))."秒后重试!"]);
		
		if($sql("SELECT count(*) FROM users WHERE admin_id='".$admin['id']."'")>=$admin['max_user'])code(["code"=>"0","message"=>"注册用户已到达上限，请联系管理员扩容"]);
		
		
		$jf = $config['reg_jf'];
		if($config['reg_vip']=='0')$vip='0000-00-00 00:00:00';
		else $vip = date("Y-m-d H:i:s",(strtotime("now")+($config['reg_vip']*86400)));
		$money = $config['reg_money'];
		
		if($config['reg_onlyimei']==1){
			$isimei = $sql("SELECT * FROM users_config WHERE admin_id='".$admin['id']."' ");
			if($isimei)code(["code"=>"0","message"=>"该设备号imei已被注册"]);
		}
		
		$invite=$_REQUEST['invite'];
		if($invite && $config['yq_value']!='' && $config['yq_value']!='0'){
			$isuser = $sql("SELECT * FROM users WHERE admin_id='".$admin['id']."' AND user='{$invite}' ");
			if(!$isuser)code(["code"=>"0","message"=>"注册失败，邀请用户不存在"]);
			
			if($config['yq_what']=='积分' || $config['yq_what']==''){
				$jf = $config['yq_value'];
				$res = $sql("UPDATE  `users` SET jf='".($users['jf']+$jf)."' WHERE user='".$isuser['user']."' AND admin_id='".$admin['id']."' ");
				$res = $sql("INSERT INTO `users_log_jf` (`admin_id`,`user`,`num`,`msg`,`addtime`) VALUES ('".$admin['id']."','".$isuser['user']."','{$jf}','邀请新用户','{$date}')");
			}else if($config['yq_what']=='vip'){
				$vip = $config['yq_value'];
				if(strtotime($isuser['vip'])>strtotime($date)){
					$vipday = date("Y-m-d H:i:s",strtotime($isuser['vip'])+(86400*$vip));
				}else{
					$vipday = date("Y-m-d H:i:s",strtotime($date)+(86400*$vip));
				}
				$res = $sql("UPDATE  `users` SET vip='{$vipday}' WHERE user='".$isuser['user']."' AND admin_id='".$admin['id']."' ");
				$res = $sql("INSERT INTO `users_log_vip` (`admin_id`,`user`,`num`,`msg`,`addtime`) VALUES ('".$admin['id']."','".$isuser['user']."','{$vip}','邀请新用户','{$date}')");
			}else{
				$money = $config['yq_value'];
				$newmoney = $isuser['money']+$money; 
				$res = $sql("UPDATE  `users` SET money='{$newmoney}' WHERE user='".$isuser['user']."' AND admin_id='".$admin['id']."' ");
				$res = $sql("INSERT INTO `users_log_money` (`admin_id`,`user`,`num`,`msg`,`addtime`) VALUES ('".$admin['id']."','".$isuser['user']."','{$money}','邀请新用户','{$date}')");
			}
		}
		
		
		$pass = md5($user.$pass);
		
		$res = $sql("INSERT INTO `users`
			(`admin_id`,`user`,`pass`,`imei`,`jf`,`money`,`vip`,`custom`,`reg_time`,`reg_ip`)
			VALUES
			('".$admin['id']."','{$user}','{$pass}','{$imei}','{$jf}','{$money}','{$vip}','{$custom}','{$date}','".ip()."')
		");
		
		if(!$res)code(["code"=>"0","message"=>"注册失败!"]);
		
		if($config['reg_jf']>0){
			$jf = $config['reg_jf'];
			$res = $sql("UPDATE  `users` SET jf='".($users['jf']+$jf)."' WHERE user='$user' AND admin_id='".$admin['id']."' ");
			$res = $sql("INSERT INTO `users_log_jf` (`admin_id`,`user`,`num`,`msg`,`addtime`) VALUES ('".$admin['id']."','{$user}','{$jf}','注册赠送','{$date}')");
			$jf = "积分(".$jf.")";
		}
		
		if($config['reg_money']>0){
			$money = $config['reg_money'];
			$newmoney = $users['money']+$money; 
			$res = $sql("UPDATE  `users` SET money='{$newmoney}' WHERE user='$user' AND admin_id='".$admin['id']."' ");
			$res = $sql("INSERT INTO `users_log_money` (`admin_id`,`user`,`num`,`msg`,`addtime`) VALUES ('".$admin['id']."','{$user}','{$money}','注册赠送','{$date}')");
			$money = "余额(".$money.")";
		}
		
		if($config['reg_vip']>0){
			$vip = $config['reg_vip'];
			if(strtotime($users['vip'])>strtotime($date)){
				$t="续费";
				$vipday = date("Y-m-d H:i:s",strtotime($users['vip'])+(86400*$vip));
			}else{
				$t="开通";
				$vipday = date("Y-m-d H:i:s",strtotime($date)+(86400*$vip));
			}
			$res = $sql("UPDATE  `users` SET vip='{$vipday}' WHERE user='$user' AND admin_id='".$admin['id']."' ");
			$res = $sql("INSERT INTO `users_log_vip` (`admin_id`,`user`,`num`,`msg`,`addtime`) VALUES ('".$admin['id']."','{$user}','{$vip}','注册赠送','{$date}')");
			$vip = $t."会员(".$vip."天)";
		}
		
		
		
		
		
		code(["code"=>"1","message"=>"注册成功！","jf"=>$jf,"money"=>$money,"vip"=>$vip]);
	
	
	
	
	
?>