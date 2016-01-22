CSC Curling
<div>
	<?php
	if(!class_exists('mysqli')){
		echo <<<EOL
<div class="notice errorbox"><p>Error: mysqli does not exist. Please install it and try again. User signup and login will not work!</p></div>
EOL;

	}
	?>
	<p>Welcome to your website! You have successfully set it up.</p>
</div>