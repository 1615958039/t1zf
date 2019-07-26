<?php
	include("../function.php");
	if(!$admin)code(["code"=>"-1","message"=>"Cookie已到期请重新登陆！"]);
	$_POST = json_decode(file_get_contents('php://input'),true);
	
	$id = $_POST["id"];
	$res = $sql("SELECT * FROM pay_goods WHERE id='{$id}' AND admin_id='".$admin['id']."' AND dowhat='自动发货' ");
	if(!$res)code(["code"=>"0","message"=>"无权限！"]);
	
	
	$type=$_POST["type"];
	if($type=='add'){
		if($sql("SELECT count(*) FROM pay_goods_km WHERE id='{$id}' ")>2000)code(["code"=>"0","message"=>"单个商品最多存2000个库存，请删除一些记录再试"]);
		$km = $_POST["km"];
		$arr=explode("\n",$km);
		$i=0;
		foreach($arr as $val){
			if(mb_strlen($val,'utf-8')<2 || mb_strlen($val,'utf-8')>200)code(["code"=>"0","message"=>"每一件商品长度需在2-200之间"]);
			$i++;
		}
		if($i>200)code(["code"=>"0","message"=>"单次最多添加200条数据"]);
		foreach($arr as $val){
			$res = $sql("INSERT INTO `pay_goods_km` (`addtime`,`goodsid`,`km`,`admin_id`) VALUES ('{$date}','{$id}','{$val}','".$admin['id']."')");
		}
		code(["code"=>"1","message"=>"添加成功！"]);
	}else if($type=='del'){
		$kmid = $_POST["kmid"];
		$res = $sql("DELETE FROM pay_goods_km WHERE id='{$kmid}' AND admin_id='".$admin['id']."' ");
		if($res)code(["code"=>"1","message"=>"删除成功！"]);
		else code(["code"=>"0","message"=>"无权限"]);
	}
	
	$search=$_POST["search"];
	$keyword=$_POST["keyword"];
	$nowpage=$_POST["nowpage"];
	$pagenum=$_POST["pagenum"];
	$orderby=$_POST["orderby"];
	
	if($search=="库存" && $keyword!=""){
		$where = " WHERE km like '%".$keyword."%' AND goodsid='{$id}' AND admin_id='".$admin['id']."'";
	}else if($search=="订单号" && $keyword!=""){
		$where = " WHERE orderid='".$keyword."' AND goodsid='{$id}' AND admin_id='".$admin['id']."'";
	}else if($search=="状态" && $keyword!="" && ($keyword=='未售' || $keyword=='已售')){
		if($keyword=='未售')$keyword='0';
		else $keyword='1';
		$where = " WHERE issell='".$keyword."' AND goodsid='{$id}' AND admin_id='".$admin['id']."'";
	}else if($search=="时间" && $keyword!=""){
		if(is_set($keyword,"大于")){
			$keyword=sj($keyword."狗东西66","大于","狗东西66");
			if(strtotime($keyword)>999){
				$where = " WHERE addtime > '".$keyword."' "." AND goodsid='{$id}' AND admin_id='".$admin['id']."'";
			}else code(["code"=>"0","message"=>"时间格式输入错误！"]);
		}else if(is_set($keyword,"小于")){
			$keyword=sj($keyword."狗东西66","小于","狗东西66");
			if(strtotime($keyword)>999){
				$where = " WHERE addtime < '".$keyword."' "." AND goodsid='{$id}' AND admin_id='".$admin['id']."'";
			}else code(["code"=>"0","message"=>"时间格式输入错误！"]);
		}else if(is_set($keyword,"到")){
			$k1=sj("狗东西66".$keyword,"狗东西66",'到');
			$k2=sj($keyword."狗东西66","到","狗东西66");
			if(strtotime($k1)>999 && strtotime($k2)>999){
				$where = " WHERE addtime between '".$k1."' AND goodsid='{$id}' AND '".$k2."' "." AND admin_id='".$admin['id']."'";
			}else code(["code"=>"0","message"=>"时间格式输入错误！"]);
		}else code(["code"=>"0","message"=>"时间格式输入错误！"]);
	}else{
		$where = " WHERE  admin_id='".$admin['id']."' AND goodsid='{$id}'";
	}
	
	
	if($orderby=="id降序"){
		$orderby=" ORDER BY id desc ";
	}else if($orderby=="id升序"){
		$orderby=" ORDER BY id asc ";
	}else{
		$orderby=" ORDER BY id desc ";
	}
	
	
	$sqlnum = $sql("SELECT count(*) from pay_goods_km ".$where.$orderby);
	if($pagenum=="10" || $pagenum=="30" || $pagenum=="50" || $pagenum=="100"){}else $pagenum=30;
	$maxpage=intval($sqlnum/$pagenum);
	if ($sqlnum%$pagenum)$maxpage++;
	$nowpage=(int)$nowpage;
	if($nowpage=="" || $nowpage<1 || $nowpage>$maxpage)$nowpage=1;
	$offset=$pagenum*($nowpage-1);
	$limit = " LIMIT {$offset},{$pagenum} ";
	
	
	$res = $sql("SELECT * FROM pay_goods_km ".$where.$orderby.$limit,"list");
	
	$json = array();
	$i=0;
	foreach($res as $val){
		
		
		if($val['issell']=='1')$zt="已售(oid:".$val['orderid'].")";
		else $zt = "待出售";
		
		$json[$i++] = [
			'id'=>$val['id'],
			'km'=>$val['km'],
			'zt'=>$zt,
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
	
	
	
	
	
	
