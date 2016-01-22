<?php
include_once '../main.php';
if (!function_exists('getPage')) {
	function getPage($page, $formatted = true, $redirect = false)
	{
		$page = realpath($page);
		if (!$page) return false;
		if ($formatted) {
			$filename = end(explode('/', $page));
			if (startsWith($page, DOCUMENT_ROOT . "/content")) {
				if (!is_dir($page)) {
					createPage($page, $filename == 'index.php' ? '/' : explode('.', $filename)[0]);
					exit;
				}
			}
			return false;
		} else {
			if (startsWith($page, DOCUMENT_ROOT . "/customcontent")) {
				if (!is_dir($page)) {
					if ($redirect) {
						echo $_GET['page'];
						exit;
					}
					chdir(dirname($page));
					header('Content-Type: ' . getMimeType($page));
					if(getChars($page,-4)===".php"){
						include($page);
					}else {
						Download($page);
					}
					exit;
				} else {
					if (file_exists($page . '/index.php')) {
						if ($redirect) {
							echo getSelf($page);
							exit;
						}
						if (substr($_GET['rawpage'], -1) != '/') {
							location($_GET['rawpage'] . '/');
						}
						chdir($page);
						header('Content-Type: ' . getMimeType($page . '/index.php'));
						include($page . '/index.php');
						exit;
					} else {
						//TODO: add file viewer
						return false;
					}
				}
			}
			return false;
		}
	}
}
if (!function_exists('getMimeType')) {
	function getMimeType($page)
	{
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime = finfo_file($finfo, $page);
		switch (end(explode('.', $page))) {
			case 'appcache':
				$mime = 'text/cache-manifest';
				break;
			case 'php':
				$mime = 'text/html';
				break;
			case 'js':
				$mime = 'application/javascript';
				break;
			case 'css':
				$mime = 'text/css';
				break;
			default:
				break;
		}
		finfo_close($finfo);
		return $mime;
	}
}
if (!function_exists('addHandler')) {
	function addHandler($case, $function)
	{
		global $functions;
		$functions[] = array($case, $function);
	}
}
# http://stackoverflow.com/a/7591130/4471524
if (!function_exists('Download')) {
	function Download($path, $speed = 1024, $multipart = true)
	{
		if (is_file($path = realpath($path)) === true) {
			$file = @fopen($path, 'rb');
			$size = sprintf('%u', filesize($path));
			$speed = (empty($speed) === true) ? 1024 : floatval($speed);

			if (is_resource($file) === true) {
				set_time_limit(0);

				if (strlen(session_id()) > 0) {
					session_write_close();
				}

				if ($multipart === true) {
					$range = array(0, $size - 1);

					if (array_key_exists('HTTP_RANGE', $_SERVER) === true) {
						$range = array_map('intval', explode('-', preg_replace('~.*=([^,]*).*~', '$1', $_SERVER['HTTP_RANGE'])));

						if (empty($range[1]) === true) {
							$range[1] = $size - 1;
						}

						foreach ($range as $key => $value) {
							$range[$key] = max(0, min($value, $size - 1));
						}

						if (($range[0] > 0) || ($range[1] < ($size - 1))) {
							header(sprintf('%s %03u %s', 'HTTP/1.1', 206, 'Partial Content'), true, 206);
						}
					}

					header('Accept-Ranges: bytes');
					header('Content-Range: bytes ' . sprintf('%u-%u/%u', $range[0], $range[1], $size));
				} else {
					$range = array(0, $size - 1);
				}

				header('Pragma: public');
				header('Cache-Control: public, no-cache');
				/** @noinspection PhpWrongStringConcatenationInspection */
				header('Content-Length: ' . sprintf('%u', $range[1] - $range[0] + 1));
				if (startsWith(getMimeType($path), 'video')) {
					header('Content-Disposition: attachment; filename="' . basename($path) . '"');
				}
				//header('Content-Transfer-Encoding: binary');

				if ($range[0] > 0) {
					fseek($file, $range[0]);
				}

				while ((feof($file) !== true) && (connection_status() === CONNECTION_NORMAL)) {
					echo fread($file, round($speed * 1024));
					flush();
				}

				fclose($file);
			}

			exit;
		} else {
			header(sprintf('%s %03u %s', 'HTTP/1.1', 404, 'Not Found'), true, 404);
		}

		return false;
	}
}