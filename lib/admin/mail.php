<?php
	include("../function.php");
	if(!$admin)code(["code"=>"-1","message"=>"Cookie已到期请重新登陆！"]);
	$_POST = json_decode(file_get_contents('php://input'),true);
	
	
	if($_POST['type']=='index'){
		$res = $sql("SELECT * FROM smtp_config WHERE admin_id='".$admin['id']."' ");
		if(!$res){
			$up = $sql("INSERT INTO `smtp_config` (`admin_id`) VALUES ('".$admin['id']."')");
			$res = $sql("SELECT * FROM smtp_config WHERE admin_id='".$admin['id']."' ");
		}
		if($res['isssl']==1)$res['isssl']="true";
		else $res['isssl']="false";
		if($res['port']==0)$res['port']='';
		code(["code"=>"1","message"=>"获取成功！",
			"smtp"=>$res['smtp'],
			"port"=>$res['port'],
			"ssl"=>$res['isssl'],
			"user"=>$res['user'],
			"pass"=>$res['pass'],
			"name"=>$res['name'],
			"email"=>$res['adminmail'],
		]);
	}else if($_POST['type']=='edu'){
		
		$res = $sql("SELECT * FROM smtp_config WHERE admin_id='".$admin['id']."' ");
		if(!$res){
			$up = $sql("INSERT INTO `smtp_config` (`admin_id`) VALUES ('".$admin['id']."')");
			$res = $sql("SELECT * FROM smtp_config WHERE admin_id='".$admin['id']."' ");
		}
		
		$smtp=$_POST['smtp'];
		$port=$_POST['port'];
		$ssl=$_POST['ssl'];
		$user=$_POST['user'];
		$pass=$_POST['pass'];
		$name=$_POST['name'];
		$email=$_POST['email'];
		if(!$smtp || !$port || !$ssl || !$user || !$pass || !$name || !$email)code(["code"=>"0","message"=>"请填写完整数据再提交"]);
		if(mb_strlen($smtp,'utf-8')>30)code(["code"=>"0","message"=>"smtp地址仅限30字符"]);
		if($port<1 || $port>99999)code(["code"=>"0","message"=>"请输入正确的发件端口号"]);
		if($ssl!='false' && $ssl!='true')code(["code"=>"0","message"=>"SSL选择错误!"]);
		if(mb_strlen($user,'utf-8')>25)code(["code"=>"0","message"=>"邮箱账号仅限25字符"]);
		if(mb_strlen($pass,'utf-8')>20)code(["code"=>"0","message"=>"邮箱密码仅限20字符"]);
		if(mb_strlen($name,'utf-8')>10)code(["code"=>"0","message"=>"邮件大标题仅限10字符"]);
		if(mb_strlen($email,'utf-8')>30)code(["code"=>"0","message"=>"管理员邮箱仅限30字符"]);
		if($ssl=='true')$ssl=1;
		else $ssl=0;
		$res = $sql("UPDATE  `smtp_config` SET smtp='{$smtp}',port='{$port}',isssl='{$ssl}',user='{$user}',pass='{$pass}',name='{$name}',adminmail='{$email}' WHERE admin_id='".$admin['id']."'");
		if($res)code(["code"=>"1","message"=>"修改成功！"]);
		else code(["code"=>"0","message"=>"修改失败"]);
	}else if($_POST['type']=='tesk'){
		$who = "admin";
		$title = "测试邮件";
		$text = "这是一封来自后台服务器的测试邮件，当您看到这封邮件时，表示您的接口已配置正常！感谢您对我们的支持";
		
		$res = $sql("SELECT * FROM smtp_config WHERE admin_id='".$admin['id']."' ");
		if($who=='admin')$who = $res['adminmail'];
		if(!$res || !$res['smtp'])code(["code"=>"0","message"=>"请先配置接口"]);
		$a = smtp($res['smtp'],$res['port'],$res['isssl'],$res['name'],$res['user'],$res['pass'],$title,$text,$who);
		if($a){
			$res = $sql("INSERT INTO `smtp_log` (`admin_id`,`add_time`,`add_ip`,`add_title`,`add_text`,`add_who`) VALUES ('".$admin['id']."','{$date}','".ip()."','{$title}','{$text}','{$who}')");
			code(["code"=>"1","message"=>"已成功发送测试邮件到您的管理员邮件({$who})!请您注意查收"]);
		}else code(["code"=>"0","message"=>"发件失败！接口配置失败"]);
	}else if($_POST['type']=='del'){
		
		$id=$_POST["id"];
		$res = $sql("SELECT * from smtp_log WHERE id='{$id}' AND admin_id = '".$admin['id']."' ");
		if($res){
			$res = $sql("DELETE FROM smtp_log WHERE id='{$id}' AND admin_id = '".$admin['id']."' ");
			if($res)code(["code"=>"1","message"=>"删除成功！"]);
			else code(["code"=>"0","message"=>"删除失败，权限不足"]);
		}else{
			code(["code"=>"0","message"=>"无权限！"]);
		}
		
	}
	
	
	$search=$_POST["search"];
	$keyword=$_POST["keyword"];
	$nowpage=$_POST["nowpage"];
	$pagenum=$_POST["pagenum"];
	$orderby=$_POST["orderby"];
	
	if($search=="ip" && $keyword!=""){
		$where = " WHERE add_ip like '%".$keyword."%' AND admin_id='".$admin['id']."'";
	}else if($search=="标题" && $keyword!=""){
		$where = " WHERE add_title like '%".$keyword."%' AND admin_id='".$admin['id']."'";
	}else if($search=="内容" && $keyword!=""){
		$where = " WHERE add_text like '%".$keyword."%' AND admin_id='".$admin['id']."'";
	}else if($search=="收件人" && $keyword!=""){
		$where = " WHERE add_who like '%".$keyword."%' AND admin_id='".$admin['id']."'";
	}else if($search=="时间" && $keyword!=""){
		if(is_set($keyword,"大于")){
			$keyword=sj($keyword."狗东西66","大于","狗东西66");
			if(strtotime($keyword)>999){
				$where = " WHERE add_time > '".$keyword."' "." AND admin_id='".$admin['id']."'";
			}else code(["code"=>"0","message"=>"时间格式输入错误！"]);
		}else if(is_set($keyword,"小于")){
			$keyword=sj($keyword."狗东西66","小于","狗东西66");
			if(strtotime($keyword)>999){
				$where = " WHERE add_time < '".$keyword."' "." AND admin_id='".$admin['id']."'";
			}else code(["code"=>"0","message"=>"时间格式输入错误！"]);
		}else if(is_set($keyword,"到")){
			$k1=sj("狗东西66".$keyword,"狗东西66",'到');
			$k2=sj($keyword."狗东西66","到","狗东西66");
			if(strtotime($k1)>999 && strtotime($k2)>999){
				$where = " WHERE add_time between '".$k1."' AND '".$k2."' "." AND admin_id='".$admin['id']."'";
			}else code(["code"=>"0","message"=>"时间格式输入错误！"]);
		}else code(["code"=>"0","message"=>"时间格式输入错误！"]);
	}else{
		$where = " WHERE admin_id='".$admin['id']."'";
	}
	
	
	if($orderby=="日期升序"){
		$orderby=" ORDER BY id asc ";
	}else if($orderby=="日期降序"){
		$orderby=" ORDER BY id desc ";
	}else{
		$orderby=" ORDER BY id desc ";
	}
	
	
	$sqlnum = $sql("SELECT count(*) from smtp_log ".$where.$orderby);
	if($pagenum=="10" || $pagenum=="30" || $pagenum=="50" || $pagenum=="100"){}else $pagenum=30;
	$maxpage=intval($sqlnum/$pagenum);
	if ($sqlnum%$pagenum)$maxpage++;
	$nowpage=(int)$nowpage;
	if($nowpage=="" || $nowpage<1 || $nowpage>$maxpage)$nowpage=1;
	$offset=$pagenum*($nowpage-1);
	$limit = " LIMIT {$offset},{$pagenum} ";
	
	
	$res = $sql("SELECT * FROM smtp_log ".$where.$orderby.$limit,"list");
	
	$json = array();
	$i=0;
	foreach($res as $val){
		
		if($val['money']>0)$val['money'] = '+'.$val['money'];
		
		$json[$i++] = [
			'id'=>$val['id'],
			'add_time'=>$val['add_time'],
			'add_title'=>$val['add_title'],
			'add_text'=>$val['add_text'],
			'add_who'=>$val['add_who'],
			'add_ip'=>$val['add_ip'],
		];
		
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
	
	
	
	
	
	
