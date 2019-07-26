<?php
	include("iapp.php"); //程序入口文件，函数库


	//读取文本 fr
	$file = fr("./demo.txt");
	echo "文件内容为 -> ".$file;


	//写入文本
	// fw("./demo.txt","这里是file的具体内容");

