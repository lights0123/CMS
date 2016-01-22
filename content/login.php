CSC Curling | Log In
<?php
require_once '../scripts/main.php';
if(isset($_SESSION['auth'])||isset($_SESSION['uid'])||isset($_SESSION['un'])||isset($_SESSION['email'])){
	gotopage("/");
}
global $error;
global $conn;
$error = [];
$errorMappings = [
	"password" => "Please enter a password",
	"username" => "Please enter a username or email address",
	"invalid" => "Your email address, username, and/or password is incorrect",
	"dberror" => "Uh-Oh, we're having an issue with our database. Please try again later"
];
check_cond(!DBCheck($conn), "dberror");
cCS($_POST['password'], "password");
cCS($_POST['username'], "username");
function check_cond($assertion, $errort)
{
	global $error;
	$error[$errort] = $assertion;
	return $assertion;
}

function cCS($assertion, $error)
{
	return check_cond(isset($assertion), $error);
}

if ($error['password'] || $error['username']) {
	$formattedError = "";
	foreach ($error as $key => $value) {
		if (!$value && isset($errorMappings[$key])) {
			$formattedError .= $errorMappings[$key] . "\n";
		}
	}
	$useJSON = isset($_POST['from']) && $_POST['from'] == 'jquery';
	ob_end_clean();
	if (strlen(utf8_decode($formattedError)) == 0) {
		$password = $_POST['password'];
		$username = trim($_POST['username']);
		$query = sprintf("SELECT id,perm,username,email,password FROM users WHERE username = '%s' OR email = '%s'",
			$conn->real_escape_string($username),
			$conn->real_escape_string($username));
		$result = $conn->query($query);
		if($result->num_rows===0){
			check_cond(true,"invalid");
			$formattedError.=$errorMappings["invalid"]."\n";
		}else {
			$data = $result->fetch_assoc();
			if(password_verify($password,$data['password'])) {
				$_SESSION['auth'] = $data['perm'];
				$_SESSION['uid'] = $data['id'];
				$_SESSION['un'] = $data['username'];
				$_SESSION['email'] = $data['email'];
				if ($useJSON) {
					echo json_encode([
						"success" => true,
						"redirect" => "/"
					]);
				} else {
					gotopage("/");
				}
				exit;
			}else{
				check_cond(false,"invalid");
				$formattedError.=$errorMappings["invalid"]."\n";
			}
		}
	}
	if ($useJSON) {
		$mapped = array_filter($errorMappings, function ($key) {
			global $error;
			return in_array($key, $error) && !$error[$key];
		}, ARRAY_FILTER_USE_KEY);
		foreach ($mapped as $key => $value) {
			switch (substr($key, 0, -1)) {
				case "password":
					$mapped["password"] = $mapped[$key];
					unset($mapped[$key]);
					break;
				case "username":
					$mapped["username"] = $mapped[$key];
					unset($mapped[$key]);
					break;
			}
		}
		echo json_encode([
			'success' => false,
			'error' => $mapped]);
		exit;
	} else {
		echo array_filter($errorMappings, function ($key) {
			global $error;
			return in_array($key, $error) && !$error[$key];
		}, ARRAY_FILTER_USE_KEY);
		//TODO: add support for non-js login error handling
	}
}
?>
<script src="/js/jquery.validate.min.js"></script>
<div>
	<p>Please enter your information below:</p>

	<form id="signup_form" action="<?php echo getSelf(); ?>"
		  method="POST" enctype="multipart/form-data">
		<fieldset>
			<div>
				<input type="text" id="username" name="username" size="20" placeholder="Username or Email Address"/>
			</div>

			<div>
				<input type="password" id="password" name="password" size="20" placeholder="Password"/>
			</div>
		</fieldset>
		<br/>
		<fieldset class="center">
			<input type="submit" value="Sign In"/>
			<br/>
		</fieldset>
	</form>
	<script>
		$("#signup_form").validate({
			rules: {
				username: {
					required: true
				},
				password: {
					required: true
				}
			},
			messages: {
				password: {
					required: "Please enter a password"
				},
				username: {
					required: "Please select a username"
				}
			},
			submitHandler: function (form) {
				$.post($(form).attr('action'), $(form).serialize() + "&from=jquery", function (data) {
					var jData = JSON.parse(data);
					if (jData['success'] === true) {
						window.location = jData['redirect'];
					} else if (jData['success'] === false) {
						var miscError = "";
						for (var index in jData['error']) {
							if (jData['error'].hasOwnProperty(index)) {
								switch (index) {
									case 'password':
										var password = $('#password');
										password.siblings().remove();
										password.after('<label for="password" class="error" id="password-error"></label>');
										$('#password-error').text(jData['error'][index]);
										password.addClass('error');
										break;
									case 'username':
										var username = $("#username");
										username.siblings().remove();
										username.after('<label for="username" class="error" id="username-error"></label>');
										$('#username-error').text(jData['error'][index]);
										username.addClass('error');
										break;
									default:
										miscError+=jData['error'][index]+".\n";
								}
							}
						}
						$("#signup_form").parent().prepend('<div class="notice errorbox"><p></p></div>');
						$("#signup_form").parent().find("div.notice.errorbox p").text(miscError);
					}
				});
			}
		});
	</script>
</div>