<!DOCTYPE html>
<html>
	<head>
		<title>Get rich - every hour</title>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="css/bootstrap.css"></link>
		<link rel="stylesheet" type="text/css" href="css/style.css"></link>
	</head>
<body>
<?php include('config.php'); ?>

 <?php 
	
	$con=mysqli_connect(DB_SERVER,DB_USER,DB_PASSWORD,DB_NAME);
	
	if (mysqli_connect_errno()){
	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
	} 
 ?>
	