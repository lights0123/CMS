<?php
include('globals.php');
include('dbconnect.php');
$conn=DBConnect(DB_NAME);
session_start();
$auth = isset($_SESSION['auth']) ? $_SESSION['auth'] : -1;
$uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : -1;
function page_create($title = "CSC Bonspiel App Webpage", $menu)
{
	?>
	<html>
	<head>
		<title><?php echo $title ?></title>
		<link rel='stylesheet' type='text/css' href='/css/main.css'/>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
		<?php
		if(USES_HTTPS) {
			?>
			<script>
				if ('serviceWorker' in navigator) {
					navigator.serviceWorker.register('/sw.js', {scope: '/'}).then(function (reg) {
						// registration worked
						//console.log('Registration succeeded. Scope is ' + reg.scope);
					}).catch(function (error) {
						// registration failed
						//console.log('Registration failed with ' + error);
					});
				}
			</script>
			<?php
		}
		?>
		<!--[if lt IE 9]>
		<META http-equiv="refresh" content="0;URL=unsupported">
		<body>
		<![endif]-->
		<script src="/js/menu.js"></script>
	</head>
	<div id="container">
	<?php
	menu($menu);
}

function menu($activeItem)
{
	$menu = array(
		'/' => array('Home', null),
		'/data' => array('View Data', null),
		'/contact' => array('Contact', null),
		'/about' => array('About', null)
	);
	global $auth;
	if ($auth>-1) {
		$menu['/signout'] = array('Sign Out', 'right');
		$menu['/settings'] = array('Settings', 'right');
	} else {
		$menu['/login'] = array('Login', 'right');
		$menu['/signup'] = array('Sign Up', 'right');
	}

	echo <<<EOF
<div id='menu'>
<ul>
EOF;
	foreach ($menu as $link => $data) {
		$active = ($activeItem == "/" && $link == "/") || (trim($link, '/') == $activeItem);
		$classes = "";
		if ($data[1] != null) {
			$classes = $data[1];
		}
		if ($active) {
			if ($data[1]) {
				$classes .= ' ';
			}
			$classes .= "active";
		}
		if ($classes != "") {
			$classes = ' class="' . $classes . '"';
		}
		echo <<<EOF
<li$classes><a href='$link'><span>$data[0]</span></a></li>
EOF;
	}
	echo <<<EOF
</ul>
</div>
EOF;
}

function footer()
{
	$year = date("Y");
	if($year > COPYRIGHT_YEAR){
		$string=COPYRIGHT_YEAR." - " . $year;
	}else{
		$string=COPYRIGHT_YEAR;
	}
	$copyright = COPYRIGHTER;
	echo <<<EOF
<p>Copyright © $string $copyright. Some rights reserved by the <a href="/license">license</a>.</p>
<p><a href="/tos">Terms of Service</a> | <a href="/privacy">Privacy Policy</a></p>
EOF;

}

function startsWith($haystack, $needle)
{
	// search backwards starting from haystack length characters from the end
	return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}

function endsWith($haystack, $needle)
{
	// search forward starting from end minus needle length characters
	return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}

// $amount is the amount to read from either end, for example, with $amount being 5:
// This is a test
// ↑↑↑↑↑
// Now, $amount being -5:
// This is another test
//                ↑↑↑↑↑
function getChars($string, $amount)
{
	if ($amount < 0) {
		return substr($string, $amount);
	} elseif ($amount > 0) {
		return substr($string, 0, $amount);
	} else {
		return '';
	}
}

function getSelf($file = null)
{
	if ($file === null) {
		$file = explode('/', debug_backtrace()[0]['file']);
	} else {
		$file = explode('/', $file);
	}
	$content = array_search('content', $file);
	if ($content == false) {
		$content = array_search('customcontent', $file);
	}
	$index = $content;
	for ($i = 0; $i <= $index; $i++) {
		unset($file[$i]);
	}
	$file = implode('/', $file);
	$file = endsWith($file, '.php') ? substr($file, 0, -4) : $file;
	return $file;
}
function createBox($type,$message){
	switch($type){
		case BOX_CREATE_ERROR:
			?>
	<div class="notice errorbox"><p><?php echo htmlspecialchars($message)?></p></div>
		<?php
	}
}

function gotopage($loc)
{
	if($_GET['from']==="jquery"){
		echo $loc;
	}else{
    	header("Location: " . $loc);
	}
    exit;
}