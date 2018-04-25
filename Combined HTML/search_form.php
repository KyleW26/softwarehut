
<?php
	/*** Example Search Query 
	// 1.Search academic name Dr John Smith
	SELECT *, MATCH(`HostingAcademic`) AGAINST('Dr John Smith') AS score FROM visits WHERE MATCH(`HostingAcademic`) AGAINST('Dr John Smith') ORDER BY score DESC
	// 2.Search visitor name Dr Jane Doe
	SELECT *, MATCH(`VisitorName`) AGAINST('Dr Jane Doe') AS score FROM visits WHERE MATCH(`VisitorName`) AGAINST('Dr Jane Doe') ORDER BY score DESC
	// 3.Search academic name Dr John Smith search visitor name Dr Jane Doe
	SELECT *, MATCH(`HostingAcademic`) AGAINST('Dr John Smith') AS academicscore, MATCH(`VisitorName`) AGAINST('Dr Jane Doe') AS visitorscore  FROM visits HAVING MATCH(`HostingAcademic`) AGAINST('Dr John Smith') OR MATCH(`VisitorName`) AGAINST('Dr Jane Doe') ORDER BY academicscore DESC, visitorscore DESC
	// 4.Search for visits occurring on
	SELECT * FROM visits WHERE '1970-01-01' BETWEEN VisitStartDate AND VisitEndDate
	// 5.Search for visits starting on
	SELECT * FROM visits WHERE VisitStartDate = '1970-01-01'
	// 6.Search for visits ending on
	SELECT * FROM visits WHERE VisitsEndDate = '1970-01-01'
	// 7.Search for visits starting on and ending on
	SELECT * FROM visits WHERE VisitStartDate = '1970-01-01' AND VisitsEndDate = '1970-01-04'
	
	1. search without academic name
	SELECT *, MATCH(`HostingAcademic`) AGAINST('') AS score FROM visits WHERE MATCH(`HostingAcademic`) AGAINST('') OR 1 = 1 ORDER BY score DESC
	2. search without visitor name
	SELECT *, MATCH(`VisitorName`) AGAINST('Dr Jane Doe') AS score FROM visits WHERE MATCH(`VisitorName`) AGAINST('Dr Jane Doe') OR 1 = 1 ORDER BY score DESC
	3. search without date
	SELECT * FROM visits WHERE '' BETWEEN VisitStartDate AND VisitEndDate OR 1 = 1
	4. search without start date
	SELECT * FROM visits WHERE VisitStartDate = '' OR 1 = 1
	5. search without end date
	SELECT * FROM visits WHERE VisitsEndDate = '' OR 1 = 1
	
	Search both names
	SELECT *, MATCH(`HostingAcademic`) AGAINST('HOSTINGACADEMIC') AS academicscore, MATCH(`VisitorName`) AGAINST('VISITORNAME') AS visitorscore  FROM visits HAVING (MATCH(`HostingAcademic`) AGAINST('HOSTINGACADEMIC') OR 1 = 1) AND (MATCH(`VisitorName`) AGAINST('VISITORNAME') OR 1 = 1) ORDER BY academicscore DESC, visitorscore DESC

	*/

	

	require (__DIR__ . "/login/user_system.php");
	block_user_below($id, $session, 1);

	function visit_summary($row) {
		/* Combinations
			visitor name and hosting academic name
			visitor name and hosting academic name and dates
			visitor name and hostingschool and hosting academic name and homeinstitutionname
			visitor name and hostingschool and hosting academic name and homeinstitutionname and dates
		*/
		global $auth_level;
		
		$summary = "";
		if (!empty($row['VisitorName']) && !empty($row['HostingAcademic'])) {
			if ($auth_level >= 2) {
				$summary = $row['username'] . " - ";
			}
			
			if (!empty($row['HostingSchool']) && !empty($row['HomeInstitutionName'])) {
				$summary .= $row['VisitorName'] . " from " . $row['HomeInstitutionName'] . " is visiting " . $row['HostingAcademic'] . " from " . $row['HostingSchool'];
			} else {
				$summary .= $row['VisitorName'] . " is visiting " . $row['HostingAcademic'];
			}
			
			
			if (!empty($row['VisitStartDate'])) {
				if (!empty($row['VisitEndDate'])) {
					$summary .= " between " . $row['VisitStartDate'] . " and " . $row['VisitEndDate'];
				}
			}
		}		
		return $summary;
	}

	// Open connection to MySQL server with details defined in user_system.php
	$conn = mysqli_connect($DB_server_name, $DB_username, $DB_password, $DB_name);
	if(!$conn){
		die('Could not connect: ' . mysqli_error($conn));
	}

	// Escape username from user_systemp.php to prevent SQL injection
	$escaped_id = mysqli_real_escape_string($conn, $id);
	// No need to execute query if no values have been entered
	$run_query = false;
	if (!empty($_GET['hosting_academic'])) {
		// User has attempted to search for a hosting academic name
		
		// At least one value has been entered so query should be run
		$run_query = true;
		if (!empty($_GET['VisitorName'])) {
			// Searching for hosting academic and visitor name
			$visitor_name = mysqli_real_escape_string($conn, $_GET['VisitorName']);
			$academic_name = mysqli_real_escape_string($conn, $_GET['hosting_academic']);
			if ($auth_level > 1) {
				// Priveleged user can see all users forms
				$search_query = "
				SELECT *, 
					MATCH(`HostingAcademic`) AGAINST('$academic_name') AS academicscore,
					MATCH(`VisitorName`) AGAINST('$visitor_name') AS visitorscore
				FROM visits 
				HAVING (MATCH(`HostingAcademic`) AGAINST('$academic_name')) AND (MATCH(`VisitorName`) AGAINST('$visitor_name')) 
				ORDER BY academicscore DESC, visitorscore DESC";
			} else {
				// Low level user can only see their own forms
				$search_query = "
				SELECT *, 
					MATCH(`HostingAcademic`) AGAINST('$academic_name') AS academicscore,
					MATCH(`VisitorName`) AGAINST('$visitor_name') AS visitorscore
				FROM visits 
				WHERE username = '$escaped_id'
				HAVING (MATCH(`HostingAcademic`) AGAINST('$academic_name')) AND (MATCH(`VisitorName`) AGAINST('$visitor_name')) 
				ORDER BY academicscore DESC, visitorscore DESC";
			}
			
		} else {
			// Search for just hosting academic
			$academic_name = mysqli_real_escape_string($conn, $_GET['hosting_academic']);
			if ($auth_level > 1) {
				// Priveleged user can see all users forms
				$search_query = "
				SELECT *, 
					MATCH(`HostingAcademic`) AGAINST('$academic_name') AS academicscore,
					MATCH(`VisitorName`) AGAINST('') AS visitorscore
				FROM visits 
				HAVING (MATCH(`HostingAcademic`) AGAINST('$academic_name')) AND (MATCH(`VisitorName`) AGAINST('') OR 1 = 1) 
				ORDER BY academicscore DESC, visitorscore DESC";
			} else {
				// Low level user can only see their own forms
				$search_query = "
				SELECT *, 
					MATCH(`HostingAcademic`) AGAINST('$academic_name') AS academicscore,
					MATCH(`VisitorName`) AGAINST('') AS visitorscore
				FROM visits 
				WHERE username = '$escaped_id'
				HAVING (MATCH(`HostingAcademic`) AGAINST('$academic_name')) AND (MATCH(`VisitorName`) AGAINST('') OR 1 = 1) 
				ORDER BY academicscore DESC, visitorscore DESC";
			}

		}
		
	} else {
		if (!empty($_GET['VisitorName'])) {
			// Search for just visitor name
			$run_query = true;
			$visitor_name = mysqli_real_escape_string($conn, $_GET['VisitorName']);
			if ($auth_level > 1) {
				// Priveleged user can see all users forms
				$search_query = "
				SELECT *, 
					MATCH(`HostingAcademic`) AGAINST('') AS academicscore,
					MATCH(`VisitorName`) AGAINST('$visitor_name') AS visitorscore
				FROM visits
				HAVING (MATCH(`HostingAcademic`) AGAINST('') OR 1 = 1) AND (MATCH(`VisitorName`) AGAINST('$visitor_name')) 
				ORDER BY academicscore DESC, visitorscore DESC";
			} else {
				// Low level user can only see their own forms
				$search_query = "
				SELECT *, 
					MATCH(`HostingAcademic`) AGAINST('') AS academicscore,
					MATCH(`VisitorName`) AGAINST('$visitor_name') AS visitorscore
				FROM visits 
				WHERE username = '$escaped_id'
				HAVING (MATCH(`HostingAcademic`) AGAINST('') OR 1 = 1) AND (MATCH(`VisitorName`) AGAINST('$visitor_name')) 
				ORDER BY academicscore DESC, visitorscore DESC";
			}
			
		} else {
			// No name entered, just create base query to use as sub-query for another field
			if ($auth_level > 1) {
				// Priveleged user can see all users forms
				$search_query = "SELECT * FROM visits";
			} else {
				// Low level user can only see their own forms
				$search_query = "SELECT * FROM visits WHERE username = '$escaped_id'";
			}
		}
	}

	if (!empty($_GET['date'])) {
		// User has attempted to search for visits happening on a certain date
		
		// Execute query since there is entered data to search for
		$run_query = true;
		// Escape date string to prevent SQL injection
		$date = mysqli_real_escape_string($conn, $_GET['date']);
		$search_query = "SELECT * FROM ($search_query) AS a WHERE '$date' BETWEEN VisitStartDate AND VisitEndDate";
	}
	if (!empty($_GET['start_date'])) {
		// User has attempted to search for visits starting on a certain date
		
		// Execute query since there is entered data to search for
		$run_query = true;
		// Escape date string to prevent SQL injection
		$start_date = mysqli_real_escape_string($conn, $_GET['start_date']);
		$search_query = "SELECT * FROM ($search_query) AS b WHERE VisitStartDate = '$start_date'";
	}
	if (!empty($_GET['end_date'])) {
		// User has attempted to search for visits ending on a certain date
		
		// Execute query since there is entered data to search for
		$run_query = true;
		// Escape date string to prevent SQL injection
		$end_date = mysqli_real_escape_string($conn, $_GET['end_date']);
		$search_query = "SELECT * FROM ($search_query) AS c WHERE VisitEndDate = '$end_date'";
	}
	$results = 0;
	if ($run_query) {
		// Some data has been entered in the search form so query should be run
		$result = mysqli_query($conn, $search_query);
		if ($result !== false) {
			// Count results
			$results = mysqli_num_rows($result);
		} else {
			die('Unable to get a result: ' . mysqli_error($conn));
		}
	}
	mysqli_close($conn);

?>

<!DOCTYPE html>
<html>
	<head>
		<link type="text/css" rel="stylesheet" href="css/all.css">
		<link type="text/css" rel="stylesheet" href="css/forms.css">
		<link type="text/css" rel="stylesheet" href="css/search.css">
	</head>
	<body>
		
		<div id="search-form">
			<div class="padding"></div>
			<a class="button low-pad-button" href="index.php">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Home&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
			<a class="button low-pad-button" href="submit_form.php">Submit a form</a><br><br>
			<form>
				<h3>Find visits being hosted by a certain person:</h3>
				<input type="text" name="hosting_academic" placeholder="Hosting Academic">
				<br>

				<h3>Or find visits by the name of the person visiting:</h3>
				<input type="text" name="VisitorName" placeholder="Visitor Name">
				<br>

				<h3>Or find visits that will be ongoing on a certain day</h3>
				<input type="date" name="date">
				<br>

				<h3>Or find visits starting on a certain day:</h3>
				<input type="date" name="start_date">
				<br>

				<h3>Or find visits ending on a certain day:</h3>
				<input type="date" name="end_date">
				<br><br>
				<input type="submit" class="button green-button" value="Search">
				<input type="reset" class="button red-button">
				<br>
				<h3>0 Results</h3>
			</form>
		</div>
		<div id="current-results">
			<?php
				// If results exist, format data and display as a list item inside an unordered list
				if ($results > 0) {
					echo "<ul>";
					for ($i = 0; $i < $results; $i++) {
						$row = mysqli_fetch_assoc($result);
						echo "<li><a href=\"view_form.php?visit_id=". $row['VisitID'] . "\">" . visit_summary($row) . "</a></li>";
					}
					echo "</ul>";
				} else {
					echo "<h3>No results to display</h3>";
				}
			
			?>
		</div>
	</body>
</html>

