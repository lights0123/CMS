Error
<?php
$status = (!isset($_SERVER['REDIRECT_STATUS'])) ? http_response_code() : $_SERVER['REDIRECT_STATUS'];
$codes = array(
	200 => array('404 Not Found', 'Uh oh! Your page was not found.'),
	403 => array('403 Forbidden', 'For some reason, you are forbidden to view this content.'),
	404 => array('404 Not Found', 'Uh oh! Your page was not found.'),
	405 => array('405 Method Not Allowed', 'The method specified in the Request-Line is not allowed for the specified resource.'),
	408 => array('408 Request Timeout', 'Your browser failed to sent a request in the time allowed by the server.'),
	500 => array('500 Internal Server Error', 'The request was unsuccessful due to an unexpected condition encountered by the server.'),
	502 => array('502 Bad Gateway', 'The server received an invalid response from the upstream server while trying to fulfill the request.'),
	504 => array('504 Gateway Timeout', 'The upstream server failed to send a request in the time allowed by the server.'),
	0 => array($status, 'An unknown error occurred.')
);
if ($codes[$status] == null) {
	$status = 0;
}
?>
<div>
	<h1><?php echo $codes[$status][0]; ?></h1>

	<p><?php echo $codes[$status][1]; ?></p>
</div>