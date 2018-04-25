<?php
	/* columns
	VisitID INT UNSIGNED ZEROFILL NOT NULL PRIMARY KEY AUTO_INCREMENT,
	HostingSchool TEXT,
	HostingAcademic TEXT,
	VisitorName TEXT,
	HomeContactDetails TEXT,
	CvCopyLocation TEXT,
	VisitStartDate DATE,
	VisitEndDate DATE,
	VisitorUndergraduateExperience TEXT,
	VisitorPhdExperience TEXT,
	TypeOfVisitorAcademic TEXT,
	TypeOfVisitorOther TEXT,
	HomeInstitutionName TEXT,
	HomeInstitutionPosition TEXT,
	RegistrationTypeStaff TEXT,
	RegistrationTypeHR TEXT,
	RegistrationTypeStudent TEXT,
	IPRIssues TEXT,
	VisitActivitySummary TEXT,
	RoomAllocation TEXT,
	ComputingFacilities TEXT,
	EmailLibraryAccess TEXT,
	FinancialDetails TEXT,
	HoSApproved TEXT,
	RecordDateHR DATE,
	RecordDateAcademicRegistry DATE
	*/

	require (__DIR__ . "/../login/user_system.php");
	block_user_below($id, $session, 3);

	
	



	if (!empty($_POST["setup_key"]) && $_POST["setup_key"] == "gc3thI7PuUNY1l3p") {
		$conn = mysqli_connect($DB_server_name, $DB_username, $DB_password, $DB_name);
		if(!$conn){
			die('Could not connect: ' . mysqli_error());
		}

		$query = "DROP TABLE IF EXISTS visits;";
		if (!mysqli_query($conn, $query)) {
			die("Error: " . $query . "" . mysqli_error($conn));
		}


		$query = "CREATE TABLE visits (
			VisitID INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
			username varchar(32) NOT NULL,
			HostingSchool TEXT,
			HostingAcademic TEXT,
			VisitorName TEXT,
			HomeContactDetails TEXT,
			CvCopyLocation TEXT,
			VisitStartDate DATE,
			VisitEndDate DATE,
			VisitorUndergraduateExperience TEXT,
			VisitorPhdExperience TEXT,
			TypeOfVisitorAcademic TEXT,
			TypeOfVisitorOther TEXT,
			HomeInstitutionName TEXT,
			HomeInstitutionPosition TEXT,
			RegistrationTypeStaff TEXT,
			RegistrationTypeHR TEXT,
			RegistrationTypeStudent TEXT,
			IPRIssues TEXT,
			VisitActivitySummary TEXT,
			RoomAllocation TEXT,
			ComputingFacilities TEXT,
			EmailLibraryAccess TEXT,
			FinancialDetails TEXT,
			HoSApproved TEXT,
			RecordDateHR DATE,
			RecordDateAcademicRegistry DATE,
			FOREIGN KEY (username)
			REFERENCES users(username)
			ON UPDATE CASCADE ON DELETE CASCADE
		);";
		if (!mysqli_query($conn, $query)) {
			die("Error: " . $query . "" . mysqli_error($conn));
		}

		$query = "INSERT INTO `visits` (`VisitID`, `username`, `HostingSchool`, `HostingAcademic`, `VisitorName`, `HomeContactDetails`, `CvCopyLocation`, `VisitStartDate`, `VisitEndDate`, `VisitorUndergraduateExperience`, `VisitorPhdExperience`, `TypeOfVisitorAcademic`, `TypeOfVisitorOther`, `HomeInstitutionName`, `HomeInstitutionPosition`, `RegistrationTypeStaff`, `RegistrationTypeHR`, `RegistrationTypeStudent`, `IPRIssues`, `VisitActivitySummary`, `RoomAllocation`, `ComputingFacilities`, `EmailLibraryAccess`, `FinancialDetails`, `HoSApproved`, `RecordDateHR`, `RecordDateAcademicRegistry`) VALUES

		(1, 'admin', 'hostingschoolvalue', 'hostingacademicvalue', 'visitornamevalue', 'homecontactdetailsvalue', 'cvcopylocationvalue', '1970-01-01', '1970-01-02', 'visitorundergraduateexperiencevalue', 'visitorphdexperiencevalue', 'typeofvisitoracademicvalue', 'typeofvisitorothervalue', 'homeinstutitionnamevalue', 'homeinstutionpositionvalue', 'registrationtypestaffvalue', 'registrationtypehrvalue', 'registrationtypestudentcvalue', 'iprissuesvalue', 'visitactivitysummaryvalue', 'roomallocationvalue', 'computingfacilitiesvalue', 'emaillibraryaccessvalue', 'financialdetailsvalue', 'hosapprovedvalue', '1970-01-01', '1970-01-02'),

		(3, 'admin', 'computer science', 'Dr John Smith', 'Dr Jane Doe', 'email@sheffield.ac.uk', 'cvlocation.co.uk', '2019-04-16', '2019-04-18', 'MSC', 'Phd', 'yes', NULL, 'Sheffield Uni - CS dept.', 'Researcher', 'yes', 'no', 'no*split*', '*split*no', 'comparison of research', 'yes', 'yes', 'no', NULL, 'yes', '2019-04-04', '2019-04-04'),

		(4, 'admin', 'Bangor University', 'Cameron Gray', 'Andrew Griffin', '111111111', 'No', '2018-04-19', '2018-04-19', 'None', 'No', 'Andrew Griffin', 'None', 'Student', '...', 'Steve', '...', 'Yes*split*...', '.*split*No', '...', 'Please 319', 'Yes', 'No', NULL, 'Yes', '2018-04-26', '2018-04-26'),

		(5, 'admin', NULL, 'john smith', 'david smith', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),

		(6, 'admin', NULL, NULL, 'Dr Jane Doe', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);";
		if (!mysqli_query($conn, $query)) {
			die("Error: " . $query . "" . mysqli_error($conn));
		}

		$query = "ALTER TABLE `visits` ADD FULLTEXT KEY `HostingAcademic` (`HostingAcademic`);
		ALTER TABLE `visits` ADD FULLTEXT KEY `HostingAcademic_2` (`HostingAcademic`,`VisitorName`);
		ALTER TABLE `visits` ADD FULLTEXT KEY `VisitorName` (`VisitorName`);";
		if (!mysqli_multi_query($conn, $query)) {
			die("Error: " . $query . "" . mysqli_error($conn));
		} else {
			echo "Database set up correctly. Displaying values:<br><br>";
		}

		$result = mysqli_query($conn, "SELECT * FROM visits");
		foreach ($result->fetch_assoc() as $key => $value) {
			echo $key . " - " . $value . "<br>";
		}


		mysqli_close($conn);
	} else {
		echo "<form method=\"post\">Setup Key: <input type=\"text\" name=\"setup_key\"><br><input type=\"submit\"></form>";
	}



?>