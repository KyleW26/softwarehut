<?php
	if (!empty($_POST["setup_key"]) && $_POST["setup_key"] == "gc3thI7PuUNY1l3p") {
		require (__DIR__ . "/../login/user_system.php");
		$query = "CREATE DATABASE IF NOT EXISTS $DB_name;";
		$conn = mysqli_connect($DB_server_name, $DB_username, $DB_password);
		if(!$conn){
			die('Could not connect: ' . mysqli_error($conn));
		}

		if (!mysqli_query($conn, $query)) {
			die("Error: " . $query . "" . mysqli_error($conn));
		}

		$query = "USE `$DB_name`;";

		if (!mysqli_query($conn, $query)) {
			die("Error: " . $query . "" . mysqli_error($conn));
		}

		$query = "CREATE TABLE IF NOT EXISTS `users` (
		  `username` varchar(32) NOT NULL,
		  `password` varchar(255) NOT NULL,
		  `authorisation` int(11) NOT NULL,
		  `session` varchar(32) DEFAULT NULL,
		  `role` varchar(32) NOT NULL
		);";

		if (!mysqli_query($conn, $query)) {
			die("Error: " . $query . "" . mysqli_error($conn));
		}


		$query = "INSERT INTO `users` (`username`, `password`, `authorisation`, `session`, `role`) VALUES
		('admin', '$2y$10$2uwsqugngtY/YtYLCWNuA.5rHQWgLM24sivqOUMnLqS0BJw8a52jq', 3, '2119aa60f55ab8c3055dcc44f4bd6b02', 'administrator');";

		if (!mysqli_query($conn, $query)) {
			die("Error: " . $query . "" . mysqli_error($conn));
		} else {
			echo "Database setup correctly.";
		}

		mysqli_close($conn);
	} else {
		echo "<form method=\"post\">Setup Key: <input type=\"text\" name=\"setup_key\"><br><input type=\"submit\"></form>";
	}


?>
