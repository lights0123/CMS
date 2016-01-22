<?php
error_reporting(E_ERROR);
global $functions;
$functions=[];
include 'template.php';
include 'contentGlobals/contentGlobals.php';
$_GET['from']="html";
foreach (glob("contentGlobals/*.php") as $filename)
{
	if(!endsWith($filename,'contentGlobals')){
		include $filename;
	}
}
try {
	$_GET['rawpage'] = $_GET['page'];
	if ($_GET['page'] == '') {
		$_GET['page'] = "index";
	}
	chdir(__DIR__);
	chdir("../content");
	$cwd=getcwd();
	$_GET['page'] = trim($_GET['page'], '/');
	foreach($functions as $f){
		if(startsWith($_GET['page'],$f[0])){
			$f[1]($_GET['page'],$_GET['rawpage']);
			chdir($cwd);
		}
	}
	getPage($_GET['page']);
	getPage($_GET['page'] . ".php");
	chdir("../customcontent");
	getPage($_GET['page'], false);
	getPage($_GET['page'] . ".php", false);
	throw new Exception();
} catch (Exception $e) {
	http_response_code(404);
	createPage(DOCUMENT_ROOT . '/content/error.php', 'error');
}

function location($loc)
{
	header("Location: " . $loc);
	exit;
}