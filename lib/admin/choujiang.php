<?php
	include("../function.php");
	if(!$admin)code(["code"=>"-1","message"=>"Cookie已到期请重新登陆！"]);
	$_POST = json_decode(file_get_contents('php://input'),true);
	
	
	$type=$_POST['type'];
	if($type=='add'){
		$dowhat=$_POST["dowhat"];
		$dovalue=$_POST["dovalue"];
		$num=(int)$_POST["num"];
		$msg=$_POST["msg"];
		
		if($num>200 || $num<1)code(["code"=>"0","message"=>"添加数量为1~200"]);
		if($dowhat=='积分' || $dowhat=='vip' || $dowhat=='余额'){}else code(["code"=>"0","message"=>"请选择正确的选项"]);
		if(mb_strlen($msg,'utf-8')<1 || mb_strlen($msg,'utf-8')>10)code(["code"=>"0","message"=>"奖品名称为1-10个字"]);
		if($dowhat=='余额'){
			if(!is_money($dovalue))code(["code"=>"0","message"=>"请输入正确的金额"]);
		}else{
			$dovalue=(int)$dovalue;
			if($dovalue>99999 || $dovalue<1)code(["code"=>"0","message"=>"数值在1-99999之间"]);
		}
		$havenum = $sql("SELECT count(*) FROM users_choujiang WHERE admin_id='".$admin['id']."'");
		$allnum = $havenum+$num;
		if($allnum>500)code(["code"=>"0","message"=>"奖品最多添加500条，您还剩余:".(500-$havenum)]);
		
		
		$s = "INSERT INTO `users_choujiang` (`msg`,`addtime`,`admin_id`,`dowhat`,`dovalue`) VALUES ('{$msg}','{$date}','".$admin['id']."','{$dowhat}','{$dovalue}')";
		for($i=0;$i<$num;$i++){
			$res = $sql($s);
		}
		
		if($res)code(["code"=>"1","message"=>"添加成功！"]);
		else code(["code"=>"0","message"=>"添加失败！"]);
		
	}else if($type=='del'){
		$id=$_POST["id"];
		if($id=='all'){
			$res = $sql("DELETE FROM users_choujiang WHERE admin_id='".$admin['id']."' ");
		}else{
			$res = $sql("DELETE FROM users_choujiang WHERE admin_id='".$admin['id']."' AND id='{$id}' ");
		}
		if($res)code(["code"=>"1","message"=>"删除成功！"]);
		else code(["code"=>"0","message"=>"删除失败~"]);
	}
	
	
	$search=$_POST["search"];
	$keyword=$_POST["keyword"];
	$nowpage=$_POST["nowpage"];
	$pagenum=$_POST["pagenum"];
	$orderby=$_POST["orderby"];
	
	if($search=="名称" && $keyword!=""){
		$where = " WHERE msg like '%".$keyword."%' AND admin_id='".$admin['id']."'";
	}else if($search=="奖品" && $keyword!="" && ($keyword=='金额' || $keyword=='vip' || $keyword=='积分')){
		$where = " WHERE dowhat='".$keyword."' AND admin_id='".$admin['id']."'";
	}else if($search=="额度" && $keyword!=""){
		$where = " WHERE dovalue='{$keyword}' AND admin_id='".$admin['id']."'";
	}else if($search=="中奖用户" && $keyword!=""){
		$where = " WHERE user='{$keyword}' AND admin_id='".$admin['id']."'";
	}else if($search=="中奖时间" && $keyword!=""){
		if(is_set($keyword,"大于")){
			$keyword=sj($keyword."狗东西66","大于","狗东西66");
			if(strtotime($keyword)>999){
				$where = " WHERE cjtime > '".$keyword."' "." AND admin_id='".$admin['id']."'";
			}else code(["code"=>"0","message"=>"时间格式输入错误！"]);
		}else if(is_set($keyword,"小于")){
			$keyword=sj($keyword."狗东西66","小于","狗东西66");
			if(strtotime($keyword)>999){
				$where = " WHERE cjtime < '".$keyword."' "." AND admin_id='".$admin['id']."'";
			}else code(["code"=>"0","message"=>"时间格式输入错误！"]);
		}else if(is_set($keyword,"到")){
			$k1=sj("狗东西66".$keyword,"狗东西66",'到');
			$k2=sj($keyword."狗东西66","到","狗东西66");
			if(strtotime($k1)>999 && strtotime($k2)>999){
				$where = " WHERE cjtime between '".$k1."' AND '".$k2."' "." AND admin_id='".$admin['id']."'";
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
	
	
	$sqlnum = $sql("SELECT count(*) from users_choujiang ".$where.$orderby);
	if($pagenum=="10" || $pagenum=="30" || $pagenum=="50" || $pagenum=="100"){}else $pagenum=30;
	$maxpage=intval($sqlnum/$pagenum);
	if ($sqlnum%$pagenum)$maxpage++;
	$nowpage=(int)$nowpage;
	if($nowpage=="" || $nowpage<1 || $nowpage>$maxpage)$nowpage=1;
	$offset=$pagenum*($nowpage-1);
	$limit = " LIMIT {$offset},{$pagenum} ";
	
	
	$res = $sql("SELECT * FROM users_choujiang ".$where.$orderby.$limit,"list");
	
	$json = array();
	$i=0;
	foreach($res as $val){
		
		if($val['money']>0)$val['money'] = '+'.$val['money'];
		
		$json[$i++] = [
			'id'=>$val['id'],
			'msg'=>$val['msg'],
			'dowhat'=>$val['dowhat'],
			'dovalue'=>$val['dovalue'],
			'addtime'=>$val['addtime'],
			'cjtime'=>$val['cjtime'],
			'user'=>$val['user']
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
	
	
	
	
	
	
