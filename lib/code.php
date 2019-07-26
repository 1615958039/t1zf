<?php
	session_start();
	$image = imagecreatetruecolor(100, 30);
	$bgcolor = imagecolorallocate($image, 255, 255, 255);
	imagefill($image, 0, 0, $bgcolor);
	$captcha = "";
	for ($i = 0; $i < 4; $i++) {
		$fontsize = 30;
	    $fontcolor = imagecolorallocate($image, mt_rand(0, 120), mt_rand(0, 120), mt_rand(0, 120));
	    $fontcontent = rand("0","9");
	    $captcha .= $fontcontent;
	    $x = ($i * 100 / 4) + mt_rand(5, 10);
	    $y = mt_rand(5, 10);
	    imagestring($image, $fontsize, $x, $y, $fontcontent, $fontcolor);
	}
	$_SESSION['code_i'] = 1;
	$_SESSION["code"] = $captcha;
	$_SESSION['downtime'] = time()+300;
	for ($$i = 0; $i < 100; $i++) {
	    $pointcolor = imagecolorallocate($image, mt_rand(50, 200), mt_rand(50, 200), mt_rand(50, 200));
	    imagesetpixel($image, mt_rand(1, 99), mt_rand(1, 29), $pointcolor);
	}
	for ($i = 0; $i < 4; $i++) {
	    $linecolor = imagecolorallocate($image, mt_rand(50, 200), mt_rand(50, 200), mt_rand(50, 200));
	    imageline($image, mt_rand(1, 99), mt_rand(1, 29), mt_rand(1, 99), mt_rand(1, 29), $linecolor);
	}
	header('content-type:image/png');
	imagepng($image);
?>