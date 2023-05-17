<?php

header('HTTP/1.0 403 Forbidden');

echo '
		<h1>403 Forbidden</h1>
		You don\'t have permission to access this website.<br><br>
		<hr>';

do_action('log_403');

exit();