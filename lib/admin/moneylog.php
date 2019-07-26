<?php
	include("../function.php");
	if(!$admin)code(["code"=>"-1","message"=>"Cookie已到期请重新登陆！"]);
	$_POST = json_decode(file_get_contents('php://input'),true);
	
	
	
	$search=$_POST["search"];
	$keyword=$_POST["keyword"];
	$nowpage=$_POST["nowpage"];
	$pagenum=$_POST["pagenum"];
	$orderby=$_POST["orderby"];
	
	if($search=="备注" && $keyword!=""){
		$where = " WHERE message like '%".$keyword."%' AND admin_id='".$admin['id']."'";
	}else if($search=="金额" && $keyword!=""){
		$where = " WHERE money='".$keyword."' AND admin_id='".$admin['id']."'";
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
	
	
	if($orderby=="金额降序"){
		$orderby=" ORDER BY money desc ";
	}else if($orderby=="金额升序"){
		$orderby=" ORDER BY money asc ";
	}else if($orderby=="id降序"){
		$orderby=" ORDER BY id desc ";
	}else if($orderby=="id升序"){
		$orderby=" ORDER BY id asc ";
	}else{
		$orderby=" ORDER BY id desc ";
	}
	
	
	$sqlnum = $sql("SELECT count(*) from log_admin_money ".$where.$orderby);
	if($pagenum=="10" || $pagenum=="30" || $pagenum=="50" || $pagenum=="100"){}else $pagenum=30;
	$maxpage=intval($sqlnum/$pagenum);
	if ($sqlnum%$pagenum)$maxpage++;
	$nowpage=(int)$nowpage;
	if($nowpage=="" || $nowpage<1 || $nowpage>$maxpage)$nowpage=1;
	$offset=$pagenum*($nowpage-1);
	$limit = " LIMIT {$offset},{$pagenum} ";
	
	
	$res = $sql("SELECT * FROM log_admin_money ".$where.$orderby.$limit,"list");
	
	$json = array();
	$i=0;
	foreach($res as $val){
		
		if($val['money']>0)$val['money'] = '+'.$val['money'];
		
		$json[$i++] = [
			'id'=>$val['id'],
			'money'=>$val['money'],
			'message'=>$val['message'],
			'addtime'=>$val['addtime']
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
	
	
	
	
	
	
