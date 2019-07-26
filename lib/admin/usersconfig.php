<?php
	include("../function.php");
	if(!$admin)code(["code"=>"-1","message"=>"登陆状态失效"]);
	$_POST = json_decode(file_get_contents('php://input'),true);
	$type=$_POST["type"];
	
	
	if($type=="edu"){
		$reg_jf=$_POST['reg_jf'];
		$reg_vip=$_POST['reg_vip'];
		$reg_money=$_POST['reg_money'];
		$qd_jf=$_POST['qd_jf'];
		$qd_vip=$_POST['qd_vip'];
		$qd_money=$_POST['qd_money'];
		$mtj=$_POST["mtj"];
		$mtv=$_POST["mtv"];
		$cj_needwhat=$_POST["cj_needwhat"];
		$cj_needvalue=$_POST["cj_needvalue"];
		$cj_daynum=(int)$_POST["cj_daynum"];
		$yq_what=$_POST["yq_what"];
		$yq_value=$_POST["yq_value"];
		if($yq_what=='积分'){
			$yq_value=(int)$yq_value;
			if($yq_value>99999)code(["code"=>"0","message"=>"邀请注册最大赠送积分为99999"]);
		}else if($yq_what=='vip'){
			$yq_value=(int)$yq_value;
			if($yq_value>99999)code(["code"=>"0","message"=>"邀请注册最大赠送vip为99999"]);
		}else if($yq_what=='余额'){
			if(!is_money($yq_value))code(["code"=>"0","message"=>"邀请赠送的余额为0.01-9999.99。如需关闭邀请，请选择积分或VIP"]);
		}else{code(["code"=>"0","message"=>"日死你妈，老子有设置这个选项？"]);}
		
		if($cj_needwhat=='积分'){
			$cj_needvalue = (int)$cj_needvalue;
			if($cj_needvalue>9999)code(["code"=>"0","message"=>"抽奖消耗的积分仅可为0-9999"]);
		}else if($cj_needwhat=='余额'){
			if(!is_money($cj_needvalue))code(["code"=>"0","message"=>"请输入正确的抽奖消耗金额。如需关闭限制，请选择抽奖消耗积分设置为0"]);
		}else code(["code"=>"0","message"=>"抽奖选项错误！"]);
		if($cj_daynum>10 || $cj_daynum<1)code(["code"=>"0","message"=>"每日抽奖机会仅可设置为1-10"]);
		if(!is_money($mtv))code(["code"=>"0","message"=>"请输入正确的余额兑换VIP金额,0.01-9999.99"]);
		if($mtj>9999 || $mtj<0)code(["code"=>"0","message"=>"1余额兑换的积分值需要在1-9999之间"]);
		if($reg_jf>99999999 || $reg_jf<0)code(["code"=>"0","message"=>"请输入正确的积分格式"]);
		if($qd_jf>99999999 || $qd_jf<0)code(["code"=>"0","message"=>"请输入正确的积分格式"]);
		if(!is_money($reg_money) && $reg_money!='0.00' && $reg_money!='0.0' && $reg_money!='0')code(["code"=>"0","message"=>"请输入正确的金额"]);
		if(!is_money($qd_money) && $qd_money!='0.00' && $qd_money!='0.0' && $qd_money!='0')code(["code"=>"0","message"=>"请输入正确的金额"]);
		if($reg_vip>99999999 || $reg_vip<0)code(["code"=>"0","message"=>"请输入正确的积分格式"]);
		if($qd_vip>99999999 || $qd_vip<0)code(["code"=>"0","message"=>"请输入正确的积分格式"]);
		$res = $sql("UPDATE  `users_config` SET reg_jf='{$reg_jf}',reg_vip='{$reg_vip}',reg_money='{$reg_vip}',qd_jf='{$qd_jf}',qd_vip='{$qd_vip}',qd_money='{$qd_money}',mtv='{$mtv}',mtj='{$mtj}',cj_needwhat='{$cj_needwhat}',cj_needvalue='{$cj_needvalue}',cj_daynum='{$cj_daynum}',yq_what='{$yq_what}',yq_value='{$yq_value}' WHERE admin_id='".$admin['id']."'");
		if($res)code(["code"=>"1","message"=>"成功！"]);
		else code(["code"=>"0","message"=>"修改失败"]);
	}else if($type=='reg_open'){
		$text='reg_open';
		$res = $sql("SELECT * FROM users_config WHERE admin_id='".$admin['id']."' ");
		if($res[$text]=='1'){
			$res = $sql("UPDATE  `users_config` SET ".$text."='0' WHERE admin_id='".$admin['id']."'");
		}else{
			$res = $sql("UPDATE  `users_config` SET ".$text."='1' WHERE admin_id='".$admin['id']."'");
		}
		if($res)code(["code"=>"1","message"=>"修改成功！"]);
		code(["code"=>"0","message"=>"修改失败"]);
	}else if($type=='reg_onlyimei'){
		$text='reg_onlyimei';
		$res = $sql("SELECT * FROM users_config WHERE admin_id='".$admin['id']."' ");
		if($res[$text]=='1'){
			$res = $sql("UPDATE  `users_config` SET ".$text."='0' WHERE admin_id='".$admin['id']."'");
		}else{
			$res = $sql("UPDATE  `users_config` SET ".$text."='1' WHERE admin_id='".$admin['id']."'");
		}
		if($res)code(["code"=>"1","message"=>"修改成功！"]);
		code(["code"=>"0","message"=>"修改失败"]);
	}else if($type=='qd_open'){
		$text='qd_open';
		$res = $sql("SELECT * FROM users_config WHERE admin_id='".$admin['id']."' ");
		if($res[$text]=='1'){
			$res = $sql("UPDATE  `users_config` SET ".$text."='0' WHERE admin_id='".$admin['id']."'");
		}else{
			$res = $sql("UPDATE  `users_config` SET ".$text."='1' WHERE admin_id='".$admin['id']."'");
		}
		if($res)code(["code"=>"1","message"=>"修改成功！"]);
		code(["code"=>"0","message"=>"修改失败"]);
	}else if($type=='qdlist_open'){
		$text='qdlist_open';
		$res = $sql("SELECT * FROM users_config WHERE admin_id='".$admin['id']."' ");
		if($res[$text]=='1'){
			$res = $sql("UPDATE  `users_config` SET ".$text."='0' WHERE admin_id='".$admin['id']."'");
		}else{
			$res = $sql("UPDATE  `users_config` SET ".$text."='1' WHERE admin_id='".$admin['id']."'");
		}
		if($res)code(["code"=>"1","message"=>"修改成功！"]);
		code(["code"=>"0","message"=>"修改失败"]);
	}else if($type=='edu_custom'){
		$text='edu_custom';
		$res = $sql("SELECT * FROM users_config WHERE admin_id='".$admin['id']."' ");
		if($res[$text]=='1'){
			$res = $sql("UPDATE  `users_config` SET ".$text."='0' WHERE admin_id='".$admin['id']."'");
		}else{
			$res = $sql("UPDATE  `users_config` SET ".$text."='1' WHERE admin_id='".$admin['id']."'");
		}
		if($res)code(["code"=>"1","message"=>"修改成功！"]);
		code(["code"=>"0","message"=>"修改失败"]);
	}else if($type=='login_imei'){
		$text='login_imei';
		$res = $sql("SELECT * FROM users_config WHERE admin_id='".$admin['id']."' ");
		if($res[$text]=='1'){
			$res = $sql("UPDATE  `users_config` SET ".$text."='0' WHERE admin_id='".$admin['id']."'");
		}else{
			$res = $sql("UPDATE  `users_config` SET ".$text."='1' WHERE admin_id='".$admin['id']."'");
		}
		if($res)code(["code"=>"1","message"=>"修改成功！"]);
		code(["code"=>"0","message"=>"修改失败"]);
	}else if($type=='mtv_open'){
		$text='mtv_open';
		$res = $sql("SELECT * FROM users_config WHERE admin_id='".$admin['id']."' ");
		if($res[$text]=='1'){
			$res = $sql("UPDATE  `users_config` SET ".$text."='0' WHERE admin_id='".$admin['id']."'");
		}else{
			$res = $sql("UPDATE  `users_config` SET ".$text."='1' WHERE admin_id='".$admin['id']."'");
		}
		if($res)code(["code"=>"1","message"=>"修改成功！"]);
		code(["code"=>"0","message"=>"修改失败"]);
	}else if($type=='cj_open'){
		$text='cj_open';
		$res = $sql("SELECT * FROM users_config WHERE admin_id='".$admin['id']."' ");
		if($res[$text]=='1'){
			$res = $sql("UPDATE  `users_config` SET ".$text."='0' WHERE admin_id='".$admin['id']."'");
		}else{
			$res = $sql("UPDATE  `users_config` SET ".$text."='1' WHERE admin_id='".$admin['id']."'");
		}
		if($res)code(["code"=>"1","message"=>"修改成功！"]);
		code(["code"=>"0","message"=>"修改失败"]);
	}else if($type=='cj_isvip'){
		$text='cj_isvip';
		$res = $sql("SELECT * FROM users_config WHERE admin_id='".$admin['id']."' ");
		if($res[$text]=='1'){
			$res = $sql("UPDATE  `users_config` SET ".$text."='0' WHERE admin_id='".$admin['id']."'");
		}else{
			$res = $sql("UPDATE  `users_config` SET ".$text."='1' WHERE admin_id='".$admin['id']."'");
		}
		if($res)code(["code"=>"1","message"=>"修改成功！"]);
		code(["code"=>"0","message"=>"修改失败"]);
	}
	
	
	
	
	
	
	
	
	$res = $sql("SELECT * FROM users_config WHERE admin_id='".$admin['id']."' ");
	unset($res['id']);
	unset($res['admin_id']);
	code(["code"=>"1","message"=>"获取成功！","data"=>$res]);