<?php

	function is_have($f){
		return function_exists($f)?"<font color='green'>开启</font>":"<font color='red'>关闭</font>";
	}

	

?>

<!DOCTYPE html>
<html>
	<head>
		<title>T1支付 - 网站安装检测程序</title>
		<meta charset="utf-8">
	</head>

	<body>

		<h3>环境相关内容检测:</h3>
		<table border="1">	
		  	<tr>
		    	<th>项目</th>
		    	<th>状态</th>
		    	<th>说明</th>
		  	</tr>

		  	<tr>
		    	<td>php版本</td>
		    	<td><?=substr(PHP_VERSION,0,3)?></td>
		    	<td>php版本必须大于7.0</td>
		  	</tr>


		  	<tr>
		    	<td>GD2库</td>
		    	<td><?=is_have("imagecreate")?></td>
		    	<td>验证码图像生成处理库</td>
		  	</tr>

		  	<tr>
		  		<td>Mysqli库</td>
		  		<td><?=is_have("mysqli_connect")?></td>
		  		<td>数据库链接函数</td>
		  	</tr>

		  	<tr>
		  		<td>Curl库</td>
		  		<td><?=is_have("curl_init")?></td>
		  		<td>网络请求相关函数</td>
		  	</tr>

		  	<tr>
		  		<td>mb_strlen函数</td>
		  		<td><?=is_have("mb_strlen")?></td>
		  		<td>中文字符串处理函数</td>
		  	</tr>
			
			<tr>
		  		<td>伪静态</td>
		  		<td>
		  			<img src="randcode.img" style="height: 30px;width: 50px">
		  		</td>
		  		<td>若看得见验证码说明伪静态配置成功！</td>
		  	</tr>		  	

		</table>

		<br>
		<br>
		<h3>服务器配置状态检测：</h3>
		<table border="1">
			<tr>
		    	<th>项目</th>
		    	<th>状态</th>
		    	<th>说明</th>
		  	</tr>

			<tr>
		  		<td>数据库链接</td>
		  		<td>
		  			<?php
		  				@include("lib/config.php");
		  				$mysql = @mysqli_connect($loca,$user,$pass,$name);
		  				if($mysql)echo "<font color='green'>已链接</font>";
		  				else echo "<font color='red'>未连接</font>";
		  			?>
		  		</td>
		  		<td>未链接数据库网站将无法运行</td>
		  	</tr>

		  	<tr>
		  		<td>文件上传最大值</td>
		  		<td>
		  			<?php
		  				echo ini_get('upload_max_filesize');
		  			?>
		  		</td>
		  		<td>建议设置为20M，网盘系统限制为1k~20m</td>
		  	</tr>


		</table>

	</body>
</html>
	