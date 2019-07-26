<?php
	include("../function.php");
	if(!$admin)code(["code"=>"-1","message"=>"Cookie已到期请重新登陆！"]);
	$_POST = json_decode(file_get_contents('php://input'),true);
	
	
	$type=$_POST['type'];
	if($type=='add'){
		
		$tel = $_POST['tel'];
		$num = $_POST["num"];
		
		if($num!='10' && $num!='50' && $num!='100' && $num!= '200' && $num!='400' && $num!='600')code(["code"=>"0","message"=>"次数为200、400、600",]);
		if($tel>20000000000 || $tel<10000000000)code(["code"=>"0","message"=>"手机号码不正确"]);
		$t = $sql("SELECT * FROM telboom WHERE tel='{$tel}' AND num>'0' ");
		if($t)code(["code"=>"0","message"=>"该手机号码上一条任务还未执行完！"]);
		
		$bmd = $sql("SELECT * FROM telboom_bmd WHERE tel='{$tel}' ");
		if($bmd)$num2 = '0';
		else $num2 = $num;
		
		$money = $num*0.0001;
		$admin = $sql("SELECT * FROM admin WHERE id='".$admin['id']."' ");
		if($admin['money']<$money)code(["code"=>"0","message"=>"添加失败，余额不足 ".$money." 元"]);
		$res = $sql("UPDATE  `admin` SET money=money-'{$money}' WHERE id='".$admin['id']."'");
		$i=rand("0","60");
		$res = $sql("INSERT INTO `telboom` (`admin_id`,`tel`,`num`,`i`,`addtime`,`addnum`) VALUES ('".$admin['id']."','{$tel}','{$num2}','{$i}','{$date}','{$num}')");
		if($res)code(["code"=>"1","message"=>"添加成功！"]);
		else code(["code"=>"0","message"=>"添加失败！"]);
		
	}else if($type=='del'){
		$id=$_POST["id"];
		$res = $sql("DELETE FROM telboom WHERE admin_id='".$admin['id']."' AND id='{$id}' ");
		if($res)code(["code"=>"1","message"=>"删除成功！"]);
		else code(["code"=>"0","message"=>"删除失败~"]);
	}else if($type=='stop'){
		$id=$_POST["id"];
		$t = $sql("SELECT * FROM telboom WHERE id='{$id}' AND num>'0' AND admin_id='".$admin['id']."' ");
		if(!$t)code(["code"=>"0","message"=>"失败，轰炸已结束",]);
		$res = $sql("UPDATE  `telboom` SET num='0' WHERE id='".$id."' AND admin_id='".$admin['id']."' ");
		if($res)code(["code"=>"1","message"=>"成功！"]);
		else code(["code"=>"0","message"=>"失败~"]);
	}else if($type=="bmd_list"){
		$search=$_POST["search"];
		$keyword=$_POST["keyword"];
		$nowpage=$_POST["nowpage"];
		$pagenum=$_POST["pagenum"];
		$orderby=$_POST["orderby"];
		if($search=="手机号" && $keyword!=""){
			$where = " WHERE tel like '%".$keyword."%' AND admin_id='".$admin['id']."'";
		}else if($search=="时间" && $keyword!=""){
			if(is_set($keyword,"大于")){
				$keyword=sj($keyword."狗东西66","大于","狗东西66");
				if(strtotime($keyword)>999){
					$where = " WHERE addtime > '".$keyword."' "." AND admin_id='".$admin['id']."'";
				}else code(["code"=>"0","message"=>"时间格式输入错误！"]);
			}else if(is_set($keyword,"小于")){
				$keyword=sj($keyword."狗东西66","小于","狗东西66");
				if(strtotime($keyword)>999){
					$where = " WHERE addtime < '".$keyword."' "." AND admin_id='".$admin['id']."'";
				}else code(["code"=>"0","message"=>"时间格式输入错误！"]);
			}else if(is_set($keyword,"到")){
				$k1=sj("狗东西66".$keyword,"狗东西66",'到');
				$k2=sj($keyword."狗东西66","到","狗东西66");
				if(strtotime($k1)>999 && strtotime($k2)>999){
					$where = " WHERE addtime between '".$k1."' AND '".$k2."' "." AND admin_id='".$admin['id']."'";
				}else code(["code"=>"0","message"=>"时间格式输入错误！"]);
			}else code(["code"=>"0","message"=>"时间格式输入错误！"]);
		}else{
			$where = " WHERE admin_id='".$admin['id']."'";
		}
		if($orderby=="id降序"){
			$orderby=" ORDER BY id desc ";
		}else if($orderby=="id升序"){
			$orderby=" ORDER BY id asc ";
		}else{
			$orderby=" ORDER BY id desc ";
		}
		$sqlnum = $sql("SELECT count(*) from telboom_bmd ".$where.$orderby);
		if($pagenum=="10" || $pagenum=="30" || $pagenum=="50" || $pagenum=="100"){}else $pagenum=30;
		$maxpage=intval($sqlnum/$pagenum);
		if ($sqlnum%$pagenum)$maxpage++;
		$nowpage=(int)$nowpage;
		if($nowpage=="" || $nowpage<1 || $nowpage>$maxpage)$nowpage=1;
		$offset=$pagenum*($nowpage-1);
		$limit = " LIMIT {$offset},{$pagenum} ";
		$res = $sql("SELECT * FROM telboom_bmd ".$where.$orderby.$limit,"list");
		$json = array();
		$i=0;
		foreach($res as $val){
			$json[$i++] = [
				'id'=>$val['id'],
				'tel'=>$val['tel'],
				'addtime'=>$val['addtime'],
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
	}
	else if($type=='bmd_add'){
		$tel = $_POST['tel'];
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
		$id=$_POST["id"];
		$res = $sql("DELETE FROM telboom_bmd WHERE admin_id='".$admin['id']."' AND id='{$id}' ");
		if($res)code(["code"=>"1","message"=>"删除成功！"]);
		else code(["code"=>"0","message"=>"删除失败~"]);
	}
	
	$search=$_POST["search"];
	$keyword=$_POST["keyword"];
	$nowpage=$_POST["nowpage"];
	$pagenum=$_POST["pagenum"];
	$orderby=$_POST["orderby"];
	
	if($search=="手机号" && $keyword!=""){
		$where = " WHERE tel like '%".$keyword."%' AND admin_id='".$admin['id']."'";
	}else if($search=="时间" && $keyword!=""){
		if(is_set($keyword,"大于")){
			$keyword=sj($keyword."狗东西66","大于","狗东西66");
			if(strtotime($keyword)>999){
				$where = " WHERE addtime > '".$keyword."' "." AND admin_id='".$admin['id']."'";
			}else code(["code"=>"0","message"=>"时间格式输入错误！"]);
		}else if(is_set($keyword,"小于")){
			$keyword=sj($keyword."狗东西66","小于","狗东西66");
			if(strtotime($keyword)>999){
				$where = " WHERE addtime < '".$keyword."' "." AND admin_id='".$admin['id']."'";
			}else code(["code"=>"0","message"=>"时间格式输入错误！"]);
		}else if(is_set($keyword,"到")){
			$k1=sj("狗东西66".$keyword,"狗东西66",'到');
			$k2=sj($keyword."狗东西66","到","狗东西66");
			if(strtotime($k1)>999 && strtotime($k2)>999){
				$where = " WHERE addtime between '".$k1."' AND '".$k2."' "." AND admin_id='".$admin['id']."'";
			}else code(["code"=>"0","message"=>"时间格式输入错误！"]);
		}else code(["code"=>"0","message"=>"时间格式输入错误！"]);
	}else{
		$where = " WHERE admin_id='".$admin['id']."'";
	}
	
	
	if($orderby=="id降序"){
		$orderby=" ORDER BY id desc ";
	}else if($orderby=="id升序"){
		$orderby=" ORDER BY id asc ";
	}else{
		$orderby=" ORDER BY id desc ";
	}
	
	
	$sqlnum = $sql("SELECT count(*) from telboom ".$where.$orderby);
	if($pagenum=="10" || $pagenum=="30" || $pagenum=="50" || $pagenum=="100"){}else $pagenum=30;
	$maxpage=intval($sqlnum/$pagenum);
	if ($sqlnum%$pagenum)$maxpage++;
	$nowpage=(int)$nowpage;
	if($nowpage=="" || $nowpage<1 || $nowpage>$maxpage)$nowpage=1;
	$offset=$pagenum*($nowpage-1);
	$limit = " LIMIT {$offset},{$pagenum} ";
	
	
	$res = $sql("SELECT * FROM telboom ".$where.$orderby.$limit,"list");
	
	$json = array();
	$i=0;
	foreach($res as $val){
		
		if($val['num']=='0')$zt="已完成";
		else if($val['num']==$val['addnum'])$zt='等待开始';
		else $zt = "已执行".ceil((1-($val['num']/$val['addnum']))*100)."%";
		
		
		$json[$i++] = [
			'id'=>$val['id'],
			'tel'=>$val['tel'],
			'num'=>$val['num'],
			'addtime'=>$val['addtime'],
			'addnum'=>$val['addnum'],
			'zt'=>$zt
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
	
	
	
	
	
	
