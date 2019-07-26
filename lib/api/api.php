<?php
	include("../function.php");
	
	function microtime_float(){
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
	
	
	$type = $_REQUEST['type'];
	
	if($type=='ping'){
		$url = $_REQUEST['url'];
		if(!$url)code(["code"=>"0","message"=>"无URL参数"]);
		$url = str_replace("-","",$url);
		$url = str_replace("/","",$url);
		$url = str_replace("http://","",$url);
		$url = str_replace("https://","",$url);
		if(mb_strlen($url,'utf-8')>30 || mb_strlen($url,'utf-8')<1)code(["code"=>"0","message"=>"请提交正确的url"]);
		exec("ping ".$url." -n 1",$out);
		$rt = iconv('GB2312', 'UTF-8',$out[2]);
		$ip = sj($rt, "来自 ", " 的回复");
		$time = sj($rt, "时间=", "ms");
		if(!$rt || !$ip || !$time)code(["code"=>"0","message"=>"找不到主机信息，无法连接该地址"]);
		code(["code"=>"1","message"=>"ping成功","ip"=>$ip,"time"=>$time,"text"=>$rt]);
	}
	
	else if($type=='get_ip'){
		code(["code"=>"1","message"=>"获取成功！","ip"=>ip()]);
	}
	
	else if($type=='ipinfo'){
		$ip=$_REQUEST['ip'];
		$a = hs("http://ip.taobao.com/service/getIpInfo.php?ip=".$ip);
		$b = json_decode($a,TRUE);
		if(!$b || $b['code']=='1')code(["code"=>"0","message"=>"获取IP信息失败！"]);
		if($b['data']['county']=='XX')$b['data']['county']="未知";
		if($b['data']['country']=='XX')$b['data']['country']="未知";
		if($b['data']['region']=='XX')$b['data']['region']="未知";
		if($b['data']['city']=='XX')$b['data']['city']="未知";
		if($b['data']['isp']=='XX')$b['data']['isp']="未知";
		code([
			"code"=>"1",
			"message"=>"获取成功！",
			"ip"=>$ip,
			"country"=>$b['data']['country'],
			"region"=>$b['data']['region'],
			"city"=>$b['data']['city'],
			"county"=>$b['data']['county'],
			"isp"=>$b['data']['isp'],
		]);
	}
		
	else if($type=='weather'){
		$city = $_REQUEST['city'];
		$cities = require_once 'lib/city.php';
		if(!$cities[$city] || $cities[$city]=='')code(["code"=>"0","message"=>"城市名称不正确！"]);
    	$url = "http://www.weather.com.cn/weather/".$cities[$city].".shtml";
		$data = hs($url);
		$data = sj($data,'<ul class\=\"t clearfix\">',"</ul>");
		$data = explode("</li>",$data);
		$d = array();
		$i = 0;
		foreach($data as $val){
			$time = sj($val, '<h1>', '</h1>');
			if($time){
				$weather = sj($val,'class\=\"wea\">','</p>');
				$temp = sj($val,'<p class\=\"tem\">','</p>');
				$temp = str_replace("<i>","",$temp);
				$temp = str_replace("</i>","",$temp);
				$temp = str_replace("<span>","",$temp);
				$temp = str_replace("</span>","",$temp);
				$temp = str_replace("\n","",$temp);
				$wind = sj($val,'<em>\n<span title\="','"');
				$wind2 = sj($val,'</span>\n<span title\="','"');
				if($wind2)$wind = $wind.'转'.$wind2;
				$windpower = sj($val,'</em>\n<i>','</i>');
				$d[$i++] = array(
					"date"=>$time,
					"weather"=>$weather,
					"temperature"=>$temp,
					"wind"=>$wind,
					"windpower"=>$windpower
				);
			}
		}
		code(["code"=>"1","message"=>"获取成功","city"=>$city,"data"=>$d]);
	} 
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	