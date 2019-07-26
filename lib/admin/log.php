<?php
	include("../function.php");
	if(!$admin)code(["code"=>"-1","message"=>"Cookie已到期请重新登陆！"]);
	$_POST = json_decode(file_get_contents('php://input'),true);
	
	if($_POST["type"]=="del"){
		/* 删除记录 */
		$id=(int)htmlentities($_POST["id"]);
		$res = $sql("SELECT * from log_admin_sys WHERE id='{$id}' AND admin_id = '".$admin['id']."' ");
		if($res){
			$res = $sql("DELETE FROM log_admin_sys WHERE id='{$id}' AND admin_id = '".$admin['id']."' ");
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
	
	if($search=='id' && isset($keyword)){
		$where = " WHERE id like '%".$keyword."%' "." AND admin_id='".$admin['id']."'";
	}else if($search=="操作" && isset($keyword)){
		$where = " WHERE dowhat like '%".$keyword."%' "." AND admin_id='".$admin['id']."'";
	}else if($search=="备注" && isset($keyword)){
		$where = " WHERE message like '%".$keyword."%' "." AND admin_id='".$admin['id']."'";
	}else if($search=="时间" && isset($keyword)){
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
	
	
	$sqlnum = $sql("SELECT count(*) from log_admin_sys ".$where.$orderby);
	if($pagenum=="10" || $pagenum=="30" || $pagenum=="50" || $pagenum=="100"){}else $pagenum=30;
	$maxpage=intval($sqlnum/$pagenum);
	if ($sqlnum%$pagenum)$maxpage++;
	$nowpage=(int)$nowpage;
	if($nowpage=="" || $nowpage<1 || $nowpage>$maxpage)$nowpage=1;
	$offset=$pagenum*($nowpage-1);
	$limit = " LIMIT {$offset},{$pagenum} ";
	
	
	$res = $sql("SELECT * FROM log_admin_sys ".$where.$orderby.$limit,"list");

	$json = array();
	$i=0;
	foreach($res as $val){
		$json[$i] = array(
			"id"=>$val['id'],
			"dowhat"=>$val['dowhat'],
			"message"=>$val['message'],
			"addtime"=>$val['addtime']
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
	
	
	
	
	
	
