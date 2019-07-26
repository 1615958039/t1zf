<?php
	include '../function.php';
	$key = $_GET["key"];
	$_GET['key']='';
	if($key=='')exit('fail');
	$orderid = $_REQUEST['pay_id'];
	$admin = $sql("SELECT * FROM admin WHERE apikey='".$key."' ");
	if(!$admin)code(["code"=>"0","message"=>"key错误！"]);
	$config = $sql("SELECT * FROM pay_config WHERE admin_id='".$admin['id']."' ");
	if(!$config)code(["code"=>"0","message"=>"管理员还未开启支付系统"]);
	
	
	//error_reporting(E_ALL & ~E_NOTICE);
	date_default_timezone_set('PRC');
	$codepay_config['id'] = $config['m_pid'];
	$codepay_config['key'] = $config['m_key'];
	$codepay_config['chart'] = strtolower('utf-8');
	header('Content-type: text/html; charset=' . $codepay_config['chart']);
	$codepay_config['act'] = '0';
	$codepay_config['page'] = 4;
	$codepay_config['style'] = 1;
	$codepay_config['outTime'] = 360;
	$codepay_config['min'] = 0.01;
	$codepay_config['pay_type'] = 1;
	$codepay_config['user'] = 'admin';
	$codepay_config['userOff'] = false;
	define('HTTPS', false);
	$codepay_config['gateway'] = '';
	$codepay_config['go_time'] = 3;
	$codepay_config['go_url'] = $codepay_config['return_url']; 
	define('ROOT_PATH', dirname(__FILE__)); //这是程序目录常量
	define('DEBUG', true);  //调试模式启用
	define('LOG_PATH', ROOT_PATH . '/log.txt');
	define('DB_PREFIX', 'codepay');
	
	
	function createLinkstring($data){
	    $sign='';
	    foreach ($data AS $key => $val) {
	        if ($sign) $sign .= '&'; //第一个字符串签名不加& 其他加&连接起来参数
	        $sign .= "$key=$val"; //拼接为url参数形式
	    }
	}
	
	
	
	$codepay_key = $codepay_config['key']; //这是您的密钥
	$isPost = true; 
	if (empty($_POST)) { //如果GET访问
	    $_POST = $_GET;  //POST访问 为服务器或软件异步通知  不需要返回HTML
	    $isPost = false; //标记为GET访问  需要返回HTML给用户
	}
	ksort($_POST); //排序post参数
	reset($_POST); //内部指针指向数组中的第一个元素
	
	$sign = ''; //加密字符串初始化
	
	foreach ($_POST AS $key => $val) {
	    if ($val == '' || $key == 'sign') continue; //跳过这些不签名
	    if ($sign) $sign .= '&'; //第一个字符串签名不加& 其他加&连接起来参数
	    $sign .= "$key=$val"; //拼接为url参数形式
	}
	$pay_id = $_POST['pay_id']; //需要充值的ID 或订单号 或用户名
	$money = (float)$_POST['money']; //实际付款金额
	$price = (float)$_POST['price']; //订单的原价
	$param = $_POST['param']; //自定义参数
	$type = (int)$_POST['type']; //支付方式
	$pay_no = $_POST['pay_no'];//流水号
	if (!$_POST['pay_no'] || md5($sign . $codepay_key) != $_POST['sign']) { //不合法的数据
	    if ($isPost) exit('fail');  //返回失败 继续补单
	    if ($type < 1) $type = 1;
	} else {
		$res = $sql("UPDATE  `pay_order` SET ispay='1',pay_time='{$date}' WHERE orderid='{$orderid}' AND admin_id='".$admin['id']."' ");
			if($res){
				if($config['ismail']==1){
					$res = $sql("INSERT INTO `pay_order_mail` (`admin_id`,`orderid`) VALUES ('".$admin['id']."','{$orderid}')");
				}
				echo "success";
			}
		else echo "no";
	}