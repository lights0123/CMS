CSC Curling | Sign Up
<?php
require_once '../scripts/main.php';
global $error;
global $conn;
$error = [];
$errorMappings = [
	"password" => "Please enter a password",
	"passwordl" => "Please enter no more than 64 characters",
	"passwords" => "Passwords must be at least 6 characters",
	"username" => "Please select a username",
	"usernamel" => "Please enter a username between 4 and 64 characters",
	"usernames" => "Please enter a username between 4 and 64 characters",
	"usernamet" => "Sorry, but this username is already taken",
	"email" => "Please enter an email address",
	"emaill" => "Your email address must be less than 65 characters",
	"emailv" => "Please enter a valid email address",
	"emailt" => "Sorry, but this email address is already used.",
	"dberror" => "Uh-Oh, we're having an issue with our database. Please try again later"
];
check_cond(!DBCheck($conn), "dberror");
if (cCS($_POST['password'], "password")) {
	$password = $_POST['password'];
	cCL($password, 65, false, "passwordl");
	cCL($password, 6, true, "passwords");
}
if (cCS($_POST['username'], "username")) {
	$username = trim($_POST['username']);
	cCL($username, 65, false, "usernamel");
	cCL($username, 3, true, "usernames");
	if (!DBCheck($conn)) {
		$query = sprintf("SELECT * FROM users WHERE username ='%s';",
			$conn->real_escape_string($username));
		$result = $conn->query($query);
		$rows = $result === false ? !check_cond(false, "dberror") : $result->num_rows;
		check_cond($rows < 1, "usernamet");
	}
}
if (cCS($_POST['email'], "email")) {
	$email = trim($_POST['email']);
	cCL($email, 65, false, "emaill");
	check_cond(!!filter_var($email, FILTER_VALIDATE_EMAIL), "emailv");
	if (!DBCheck($conn)) {
		$query = sprintf("SELECT * FROM users WHERE email ='%s';",
			$conn->real_escape_string($email));
		$result = $conn->query($query);
		$rows = $result === false ? !check_cond(false, "dberror") : $result->num_rows;
		check_cond($rows < 1, "emailt");
	}
}
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

function cCL($assertion, $length, $greater, $error)
{
	return check_cond($greater ? strlen(utf8_decode($assertion)) > $length : strlen(utf8_decode($assertion)) < $length, $error);
}

if ($error['password'] || $error['username'] || $error['email']) {
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
		$email = trim($_POST['email']);
		$query = sprintf("INSERT INTO users (username, password, email) VALUES ('%s', '%s', '%s');",
			$conn->real_escape_string($username),
			$conn->real_escape_string(password_hash($password, PASSWORD_DEFAULT)),
			$conn->real_escape_string($email));
		$conn->query($query);
		$query = sprintf("SELECT id,perm,username,email FROM users WHERE username = '%s'",
			$conn->real_escape_string($username));
		$result = $conn->query($query);
		$data = $result->fetch_assoc();
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
				case "email":
					$mapped["email"] = $mapped[$key];
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
				<input type="text" id="username" name="username" size="20" placeholder="Username"/>
			</div>

			<div>
				<input type="email" id="email" name="email" size="30" placeholder="Email Address"/>
			</div>

			<div>
				<input type="password" id="password" name="password" size="20" placeholder="Password"/>
			</div>
		</fieldset>
		<br/>
		<fieldset class="center">
			<input type="submit" value="Sign Up"/>
			<br/>
			<small>By signing up, you agree to the CSC Curling's <a href="/tos">Terms of Service</a> and <a
					href="/privacy">Privacy Policy</a>.
			</small>
		</fieldset>
	</form>
	<script>
		$("#signup_form").validate({
			rules: {
				username: {
					required: true,
					minlength: 4,
					maxlength: 64
				},
				password: {
					required: true,
					minlength: 6,
					maxlength: 64
				},
				email: {
					required: true,
					email: true,
					maxlength: 64
				}
			},
			messages: {
				password: {
					minlength: "Passwords must be at least 6 characters",
					required: "Please enter a password"
				},
				username: {
					minlength: "Please enter a username between 4 and 64 characters",
					required: "Please select a username",
					maxlength: "Please enter a username between 4 and 64 characters"
				},
				email: {
					email: "Please enter a valid email address",
					required: "Please enter an email address",
					maxlength: "Your email address must be less than 65 characters"
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
									case 'email':
										var email = $("#email");
										email.siblings().remove();
										email.after('<label for="email" class="error" id="email-error"></label>');
										$('#email-error').text(jData['error'][index]);
										email.addClass('error');
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