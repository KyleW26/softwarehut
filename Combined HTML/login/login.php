<?php
require (__DIR__ . "/user_system.php");

// Test cases
function test() {
	global $DB_server_name, $DB_username, $DB_password, $DB_name, $DB_user_table;
	/* TEST 1 */
	$conn = mysqli_connect($DB_server_name, $DB_username, $DB_password, $DB_name);
	if(!$conn){
 		die('Could not connect: ' . mysqli_error($conn));
	}
	echo("Expected: full contents of users table <br>");
	echo("Actual:");
	$result = mysqli_query($conn, "SELECT * FROM `$DB_user_table`");
	if ($result === false) {
		die('Couldn\'t access user db');
	}
	while ($row = $result->fetch_assoc()) {
		echo var_dump($row) . "<br>";
	}
	
	mysqli_close($conn);
	/* END OF TEST 1 */
}

function page_load() {
	global $DB_server_name, $DB_username, $DB_password, $DB_name, $DB_user_table, $username, $password, $id, $session;
	
	if (isset($_POST["t"])) {
		$conn = mysqli_connect($DB_server_name, $DB_username, $DB_password, $DB_name);
		if(!$conn){
			die('Could not connect: ' . mysqli_error());
		}
		
		if ($_POST["t"] === "login") {
			if (attempt_login($username, $password, $conn)) {
				echo("Logged in successfully.");
			} else {
				echo("Failed to log in.");
			}
			exit();
		} else if ($_POST["t"] === "logout") {
			logout($id, $session, $conn);
			echo("Successfully logged out.");
			exit();
		}
		
		mysqli_close($conn);
	}
	
}

page_load();


?>
<!DOCTYPE html>
<html>
	<head>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="../js/login.js"></script>
	</head>
	<body>
		<form method="POST">
			<input type="text" id="username-input">
			<input type="password" id="password-input">
			<a href="#" onclick="attemptLogin()">Login</a>
		</form>
		
		<div style="background-color: #AAA">
			<?php test() ?>
		</div>
		
		<a href="#" onclick="logout()">Logout</a>
		<span id="username-display"></span>
		
		<div style="background-color: #A44" id="output-message">
		
		</div>
	
	
	</body>
</html>