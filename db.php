<?php
		global $con;
		
		/*
		DB CREATE - CREATE DATABASE ?
		CREATE - CREATE TABLE ?(? ,?,?)
		INSERT - INSERT INTO ? (?, ?) VALUES (?,?,?)
		UPDATE - UPDATE ? SET ?=? WHERE =?
		DELETE - DELETE FROM ? WHERE ? =?
		SELECT - SELECT ? FROM ?
		*/

	function GetConnection(){
		$con=mysqli_connect("localhost","root","","everyhour");	
		if (mysqli_connect_errno()){
		  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
	}
	function GetRoundMoney(){
		$con=mysqli_connect("localhost","root","","everyhour");
		
		$sql = sprintf("SELECT * FROM `rounds` WHERE RID=%d",1);		
		$result = mysqli_query($con,$sql);
		
		while($row = mysqli_fetch_array($result))
		 {
			echo $row['Amount'];
		 }
	}
	function AddRoundMoney($addmoney){
		$con=mysqli_connect("localhost","root","","everyhour");
		
		$sql = sprintf("UPDATE `rounds` SET Amount=%d WHERE RID=1",$addmoney);
		$result = mysqli_query($con,$sql);
		
		alert('added');
	}
	
	if($_REQUEST['queryid'] == 1){
		GetRoundMoney();
	}
	else if($_REQUEST['queryid'] == 2){
		AddRoundMoney($_REQUEST['amt']);
	}
	else if($_REQUEST['queryid'] == 3){
		
	}
	//mysqli_close($con);
?>