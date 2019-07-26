<?php
	include("../function.php");
	if(!$admin)code(["code"=>"-1","message"=>"Cookie已到期请重新登陆！"]);
	$_POST = json_decode(file_get_contents('php://input'),true);
	
	
	if($_POST["type"]=="del"){
		/* 删除记录 */
		$id=(int)$_POST["id"];
		$res = $sql("SELECT * from pay_goods WHERE id='{$id}' AND admin_id = '".$admin['id']."' ");
		if($res){
			if($res['dowhat']=='自动发货'){
				$a = $sql("SELECT count(*) FROM pay_goods_km WHERE goodsid='{$id}' AND issell='0' ");
				if($a>0)code(["code"=>"0","message"=>"删除失败！您还有卡密未出售"]);
				$res = $sql("DELETE FROM pay_goods_km WHERE goodsid='{$id}' AND admin_id = '".$admin['id']."' ");
			}
			
			
			$res = $sql("DELETE FROM pay_goods WHERE id='{$id}' AND admin_id = '".$admin['id']."' ");
			if($res)code(["code"=>"1","message"=>"删除成功！"]);
			else code(["code"=>"0","message"=>"删除失败，权限不足"]);
		}else{
			code(["code"=>"0","message"=>"无权限！"]);
		}
	}else if($_POST['type']=="getinfo"){
		$id=(int)$_POST["id"];
		$res = $sql("SELECT * from pay_order WHERE id='{$id}' AND admin_id = '".$admin['id']."' ");
		if(!$res)code(["code"=>"0","message"=>"找不到该订单"]);
		unset($res["admin_id"]);
		
		code(["code"=>"1","message"=>"获取成功！","info"=>$res]);
		
		
	}
	
	
	$search=$_POST["search"];
	$keyword=$_POST["keyword"];
	$nowpage=$_POST["nowpage"];
	$pagenum=$_POST["pagenum"];
	$orderby=$_POST["orderby"];
	
	if($search=="下单备注"){
		$where = " WHERE add_msg like '%".$keyword."%' AND admin_id='".$admin['id']."'";
	}else if($search=="支付方式"){
		$where = " WHERE paytype like '%".$keyword."%' AND admin_id='".$admin['id']."'";
	}else if($search=="订单号"){
		$where = " WHERE orderid like '%".$keyword."%' AND admin_id='".$admin['id']."'";
	}else if($search=="金额"){
		$where = " WHERE money='".$keyword."' AND admin_id='".$admin['id']."'";
	}else if($search=="下单参数"){
		$where = " WHERE goodsval like '%".$keyword."%' AND admin_id='".$admin['id']."'";
	}else if($search=="商品ID"){
		$where = " WHERE goodsid like '%".$keyword."%' AND admin_id='".$admin['id']."'";
	}else if($search=="状态"){
		if($keyword=='待处理'){
			$where = " WHERE ispay='1' AND isdo='0' AND goodsid>=1 AND admin_id='".$admin['id']."'";
		}else if($keyword=='待支付'){
			$where = " WHERE ispay='0' AND admin_id='".$admin['id']."'";
		}else{
			$where = " WHERE ((ispay='1' AND goodsid='0')OR(ispay='1' AND isdo='1'))  AND admin_id='".$admin['id']."'";
		}
	}else if($search=="时间"){
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
	
	
	$sqlnum = $sql("SELECT count(*) from pay_order ".$where.$orderby);
	if($pagenum=="10" || $pagenum=="30" || $pagenum=="50" || $pagenum=="100"){}else $pagenum=30;
	$maxpage=intval($sqlnum/$pagenum);
	if ($sqlnum%$pagenum)$maxpage++;
	$nowpage=(int)$nowpage;
	if($nowpage=="" || $nowpage<1 || $nowpage>$maxpage)$nowpage=1;
	$offset=$pagenum*($nowpage-1);
	$limit = " LIMIT {$offset},{$pagenum} ";
	
	
	$res = $sql("SELECT * FROM pay_order ".$where.$orderby.$limit,"list");

	$json = array();
	$i=0;
	foreach($res as $val){
		
		if($val['ispay']=='0')$zt='待支付';
		else if($val['goodsid']=='0' && $val['ispay']=='1')$zt='已结单';
		else if($val['goodsid']>0 && $val['ispay']=='1' && $val['isdo']=='0')$zt='待处理'; 
		else if($val['goodsid']>0 && $val['ispay']=='1' && $val['isdo']=='1')$zt='已结单'; 
		else $zt = "未知";
		
		if($val['goodsid']=='0')$val['goodsid']='非商品订单';
		
		if($val['add_msg']=='')$val['add_msg']='未添加备注';
		else $val['add_msg']=htmlentities(mb_substr($val['add_msg'],0,10,'utf-8')).'...';
		
		$val['goodsval'] = str_replace("{","",$val['goodsval']);
		$val['goodsval'] = str_replace("}","",$val['goodsval']);
		$val['goodsval'] = str_replace('"',"",$val['goodsval']);
		$val['goodsval'] = str_replace('"',"",$val['goodsval']);
		
		
		if($val['goodsval']=='')$val['goodsval']='';
		else $val['goodsval'] = htmlentities(mb_substr($val['goodsval'],0,10,'utf-8')).'...';
		
		if($val['pay_time']=='0000-00-00 00:00:00')$val['pay_time']='';
		
		$goods = $sql("SELECT * FROM pay_goods WHERE id='".$val['goodsid']."' ");
		
		
		
		
		$json[$i] = array(
			'id'=>$val['id'],
			'orderid'=>$val['orderid'],
			'add_msg'=>$val['add_msg'],
			'paytype'=>$val['paytype'],
			'paysys'=>$val['paysys'],
			'money'=>$val['money'],
			'add_time'=>$val['add_time'],
			'pay_time'=>$val['pay_time'],
			'goodsid'=>$val['goodsid'].' - '.$goods['dowhat'],
			'goodsval'=>$val['goodsval'],
			'zt'=>$zt,
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
	
	
	
	
	
	
