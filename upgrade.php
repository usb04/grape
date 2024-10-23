<?php

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"])) {
	if ($_POST["action"] == "RANDOM_USER_IDS") {
		echo "NYI";
	}
	else if ($_POST["action"] == "BOGUS_SESSION") {
		$_SESSION["id"] = "wenomechainsama";
		echo "your session id is now ".$_SESSION['id'];
	}
}
else {
	echo "amen";
}

?>

<form method="POST" action="">
	<button type="submit" name="action" value="RANDOM_USER_IDS">Change user IDs to random IDs</button>
	<button type="submit" name="action" value="BOGUS_SESSION">Set bogus session data</button>
</form>
