<?php
if (isset($_POST['Submit'])) {
	// Get that input
	$target = $_REQUEST['ip'];

	//validation check
	if (!filter_var($target, FILTER_VALIDATE_IP)) {
		die("Invalid IP address.");
	}

	//Determine OS for ping
	if (stristr(php_uname('s'), 'Windows NT')) {
		//Windows
		$cmd = shell_exec('ping ' . escapeshellarg($target));
	} else {
		//Unix
		$cmd = shell_exec('ping -c 4 ' . escapeshellarg($target));
	}

	//Reply to user
	echo "<pre>{$cmd}</pre>";
} elseif (isset($_GET['host'])) {
	$host = $_GET['host'];

	//validate IP
	if (!filter_var($host, FILTER_VALIDATE_IP)) {
		die("Invalid IP address.");
	}

	$pingResult = [];
	exec("ping -c 4 " . escapeshellarg($host), $pingResult);
	echo "<pre>" . implode("\n", $pingResult) . "</pre>";
} else {
	echo "Please provide a host parameter either via GET (host) or POST (ip with Submit)";
}
?>

