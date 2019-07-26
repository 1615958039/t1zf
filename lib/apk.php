<?php
	include('function.php');
	
	function di404(){
		echo "404";
		die("");
	}
	
	if(count($_GET)>1)di404();
	$url='';
	foreach($_GET as $key => $val){
		$url = $key;
	}
	
	
	
	$res = $sql("SELECT * FROM apk WHERE url='{$url}' ");
	if(!$res || $url=='')di404();
	
	
	
	$file = $sql("SELECT * FROM file WHERE id='".numdecode($res['fileid'])."' ");
	
	if($res['page']==0){
		if($res['is_downsee']=='1'){
			$file['download']='';
		}else{
			$file['download']=' 下载：'.$file['download'].'次'; 
		}
		$file['filesize'] = getSize($file['filesize']);
		
		echo '
			<!DOCTYPE html>
			<html>
				<head>
					<meta itemprop="image" content="icon.png">
					<title>'.$res['title'].'</title>
					<meta itemprop="name" content="'.$res['title'].'">
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
					<meta name="viewport" content="width=device-width; initial-scale=1.0; minimum-scale=1.0; maximum-scale=1.0">
					<meta name="keywords" content="'.$res['title'].'">
					<meta name="description" content="'.$res['title'].'">
					<link rel="stylesheet" type="text/css" href="src/css/down1.css"/>
				</head>
				<body>
					<div class="show" align="center">
						<img class="icon" src="'.$res['icon'].'">
						<div class="title"><b>'.$res['apkname'].'</b></div>
						<div class="p">
							版本：'.$res['apkbb'].' 大小:'.$file['filesize'].$file['download'].'
						</div>
						<button class="down" onclick="window.location.href =\'http://t1zf.com/d?v='.$res['fileid'].'\'">下载</button>
					</div>
					<div class="show" style="margin-top: 15px;padding-left: 15px;padding-right: 5px;">
						<p>软件介绍:</p>
						<div class="content">
							'.$res['message'].'
						</div>
						<p>软件截图</p>
						<div class="picture">
							<a href="'.$res['img1'].'">
								<img src="'.$res['img1'].'" width="49%" height="100%" />
							</a>
							<a href="'.$res['img2'].'">
								<img src="'.$res['img2'].'" width="49%" height="100%" />
							</a>
							<a href="'.$res['img3'].'">
								<img src="'.$res['img3'].'" width="49%" height="100%" />
							</a>
							<a href="'.$res['img4'].'">
								<img src="'.$res['img4'].'" width="49%" height="100%" />
							</a>
						</div>
					</div>
				</body>
			</html>
		';
		
		
	}
	
	
	else if($res['page']=='1'){
		if($res['is_downsee']=='1'){
			$file['download']='';
		}else{
			$file['download']='下载:'.$file['download']; 
		}
		$file['filesize'] = getSize($file['filesize']);
		echo '
			<html>
				<head>
				<title>'.$res['title'].'</title>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
				<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0">
				<meta name="description" content="'.$res['title'].'">
				<meta name="keywords" content="'.$res['title'].'">
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
				<style>
					body{ background:#efefef; margin:0 auto;Max-width:600px;}
					*{margin:0;padding:0;border:0;text-decoration:none;-webkit-tap-highlight-color:rgba(0,0,0,0);background-color:rgba(0,0,0,0);color:#6c6c6c}
					.top {padding-bottom:20px;text-align:center;box-shadow:0px 0px 5px 1px #adadad;background:url(src/img/appbj.png);background-size:100% 100%}
					.top img{width:60px;margin-top:100px}
					.top span {display:block;margin:20px;color:#009688;font-size:14px}
					.top p{padding-bottom:20px;font-size:14px}
					.top a{background:#009688;display:block;padding:10px;margin:0px 40px 0px 40px;color:#ffffff;box-shadow:0px 0px 10px #cccccc;border-radius:3px;font-size:14px}
					.top a:hover{background:#008080;}
					.show {background:#ffffff;margin:15px;border-radius:3px;padding:10px;box-shadow:0px 1px 3px #ADADAD;min-height:50px}
					.show p{border-left:solid #009688 3px;font-size:14px;padding-left:10px;color:#009688}
					.show span {font-size:14px;display:block;margin-top:10px;line-height:25px}
					.pic {padding-top:10px;margin:-5px}
					.pic td{padding:2px;width:33.33%}
					.pic img{width:100%;height:150px;box-shadow:0px 3px 3px #e0e0e0}
				</style>
			 	</head>
				<body>
					<div class="top">
						<img src="'.$res['icon'].'">
						<span>'.$res['apkname'].'</span>
						<p> 版本:'.$res['apkbb'].'　大小:'.$file['filesize'].'　'.$file['download'].'</p>
						<a href="http://t1zf.com/d?v='.$res['fileid'].'">免费下载</a>
					</div>
					<div class="show">
						<p>应用介绍</p>
						'.$res['message'].'
					</div>
					
					<div class="show">
						<p>应用截图</p>
						<table class="pic">
							<tbody>
								<tr>
									<td>
										<img src="'.$res['img1'].'">
									</td>
									<td>
										<img src="'.$res['img2'].'">
									</td>
									<td>
										<img src="'.$res['img3'].'">
									</td>
									<td>
										<img src="'.$res['img4'].'">
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</body>
			</html>
		';
		
		
	}
	
	
	
	
	
	
	
function getSize($size) { 
    $sizes = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB"); 
    if ($size == 0) {  
        return('n/a');  
    } else { 
      return (round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $sizes[$i]);  
    } 
}
	
	