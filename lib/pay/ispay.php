<?php
	//支付状态判断接口
	include("../function.php");
	$key = $_GET['key'];
	$orderid = $_GET['orderid'];
	
	$admin = $sql("SELECT * FROM admin WHERE apikey='".$key."' ");
	if(!$admin)code(["code"=>"0","message"=>"key错误！"]);
	
	
	$order = $sql("SELECT * FROM pay_order WHERE admin_id='".$admin['id']."' AND orderid='".$orderid."' ");
	if(!$order)code(["code"=>"0","message"=>"订单详情获取失败！"]);
	
	if($order['paytype']=='qqpay')$paytype='QQ钱包';
	else if($order['paytype']=='wxpay')$paytype='微信支付';
	else $paytype="支付宝";
	
	if($order['ispay']==1)$ispay='已支付';
	else $ispay='未完成支付';
	
	
	
	if($_GET['type']=="json"){
		
		if($order['goodsid']>=1){
			code(["code"=>"1","message"=>"获取成功",
				"orderid"=>$order['orderid'],
				"paytype"=>$paytype,
				"money"=>$order['money'],
				"addtime"=>$order['add_time'],
				"addmsg"=>$order['add_msg'],
				"ispay"=>$order['ispay'],
				"isgoods"=>"0",
			]);
		}else{
			
			$res = $sql("SELECT * FROM pay_config WHERE admin_id='".$admin['id']."' ");
			if($res['sysmsg']==1)$sysmsg = $order['do_msg'];
			else $sysmsg ="管理员已domsg关闭显示";
			
			code(["code"=>"1","message"=>"获取成功",
				"orderid"=>$order['orderid'],
				"paytype"=>$paytype,
				"money"=>$order['money'],
				"addtime"=>$order['add_time'],
				"addmsg"=>$order['add_msg'],
				"ispay"=>$order['ispay'],
				"isgoods"=>"1",
				"goodsid"=>$order['goodsid'],
				"goodsisdo"=>$order['isdo'],
				"goodsdotime"=>$order['do_time'],
				"goodsdomsg"=>$order['do_msg']
			]);
		}
			
			
			
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	code(["code"=>"1","message"=>"订单获取成功！","money"=>$order['money'],"ispay"=>$ispay,'orderid'=>$orderid,'paytype'=>$paytype,'addtime'=>$order['add_time']]);
	
?>