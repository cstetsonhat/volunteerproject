<?php

// mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
// $mysqli = new mysqli("localhost", "root", "", "world");

// $city = "Amersfoort";

// /* create a prepared statement */
// $stmt = $mysqli->prepare("SELECT District FROM City WHERE Name=?");

// /* bind parameters for markers */
// $stmt->bind_param("s", $city);

// /* execute query */
// $stmt->execute();

// /* bind result variables */
// $stmt->bind_result($district);

// /* fetch value */
// $stmt->fetch();

// printf("%s is in district %s\n", $city, $district);

// mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$link = mysqli_connect("localhost", "root", "", "foodbankvolunteers");

$city = "Amersfoort";

/* create a prepared statement */
$stmt = mysqli_prepare($link, "SELECT District FROM City WHERE Name=?");

/* bind parameters for markers */
mysqli_stmt_bind_param($stmt, "s", $city);

/* execute query */
mysqli_stmt_execute($stmt);

/* bind result variables */
mysqli_stmt_bind_result($stmt, $district);

/* fetch value */
mysqli_stmt_fetch($stmt);

printf("%s is in district %s\n", $city, $district );
