<?php
		/*
		DB CREATE - CREATE DATABASE ?
		CREATE - CREATE TABLE ?(? ,?,?)
		INSERT - INSERT INTO ? (?, ?) VALUES (?,?,?)
		UPDATE - UPDATE ? SET ?=? WHERE =?
		DELETE - DELETE FROM ? WHERE ? =?
		SELECT - SELECT ? FROM ?
		*/
	
	//Store fbid globally and use whenever needed!
	//Store connection globally!
	//
	
	function GetConnection(){
		$con=mysqli_connect("localhost","root","","everyhour");	
		if (mysqli_connect_errno()){
		  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
	}
	function GetRoundMoney($roundid){
		$con=mysqli_connect("localhost","root","","everyhour");
		
		$sql = sprintf("SELECT * FROM `rounds` WHERE RID=%d",$roundid);
		$result = mysqli_query($con,$sql);
		
		while($row = mysqli_fetch_array($result))
		 {
			echo $row['Amount'];
		 }
	}
	function PotAmount($amt){
		
	}
	function PlaceBet($addbet){
		//Make sure person has enough credits he is betting with.
		//Give a slider instead of a textinput and set upper limit to the amount available with him.
		//Roll back both transaction if not enough credits.
		//Show buy credits page if he doesn't have enough credits.
		
		
		$con=mysqli_connect("localhost","root","","everyhour");		
		$sql = sprintf("UPDATE `gameon` SET amount = amount + %d",$addbet);
		$result = mysqli_query($con,$sql);
		
		$sql = sprintf("UPDATE `credits` SET amount = amount - %d WHERE fbid=%d",$addbet,625597155);
		$result = mysqli_query($con,$sql);
	}
	function CreatePlayer($email,$fbid,$firstname,$lastname){
		$con=mysqli_connect("localhost","root","","everyhour");
		$sql = sprintf("INSERT INTO `persons`(Email,fbid,FirstName,LastName) VALUES('%s',%d,'%s','%s')",$email,$fbid,$firstname,$lastname);
		$result = mysqli_query($con,$sql);
	}
	function GetCredits(){
		$con=mysqli_connect("localhost","root","","everyhour");
		$sql = sprintf("select amount from credits WHERE fbid=%d",$_REQUEST['fbid']);
		$result = mysqli_query($con,$sql);
		
		while($row = mysqli_fetch_array($result))
		 {
			echo $row[0];
		 }
	}
	function GetAmount(){
		$con=mysqli_connect("localhost","root","","everyhour");
		$sql = sprintf("select amount from gameon");
		$result = mysqli_query($con,$sql);
		
		while($row = mysqli_fetch_array($result))
		 {
			echo $row[0];
		 }
	}	
	function LastRoundTime(){
		$today = getdate();
		print_r($today);
	}
	if(isset($_REQUEST['queryid']) && $_REQUEST['queryid'] == 1){
		GetRoundMoney(2);
	}
	else if(isset($_REQUEST['queryid']) && $_REQUEST['queryid'] == 2){		
		PlaceBet($_REQUEST['betamt']);
	}
	else if(isset($_REQUEST['queryid']) && $_REQUEST['queryid'] == 3){		
		//To DO - Check if previous record of player exists else create it.
		CreatePlayer($_REQUEST['email'],$_REQUEST['fbid'],$_REQUEST['firstname'],$_REQUEST['lastname']);
	}
	else if(isset($_REQUEST['queryid']) && $_REQUEST['queryid'] == 4){
		GetCredits();
	}	
	else if(isset($_REQUEST['queryid']) && $_REQUEST['queryid'] == 5){	
		GetAmount();
	}
	else if(isset($_REQUEST['queryid']) && $_REQUEST['queryid'] == 6){	
		LastRoundTime();
	}
	//mysqli_close($con);
?>