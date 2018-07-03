<?php
	$somecontent=date("Y-m-d H:i:s");
	$somecontent.="\r\n";
	$post = file_get_contents('php://input');
	if(!empty($post)){
		//$str = json_decode($post,true);
		$somecontent .= $post;
	}
	$handle = fopen("2.txt", 'a');
	fwrite($handle, $somecontent);
	fclose($handle);
	
?>