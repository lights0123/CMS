<?php
require_once("main.php");
function createPage($path, $menu = null)
{
	ob_start();
	chdir(dirname($path));
	include($path);
	$content = ob_get_contents();
	ob_end_clean();
	page_create(strtok($content, "\n"), $menu);
	ob_start();
	footer();
	$footer = ob_get_contents();
	ob_end_clean();
	$content = substr($content, strpos($content, "\n") + 1);
	echo <<<EOF
<div id="content">
	$content
</div>
<div id="footer">
	$footer
</div>
</body>
</html>
EOF;

}
