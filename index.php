<!DOCTYPE html>
<html>
<head>
<title>Get rich - every hour</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta charset="UTF-8">
<!--<link rel="stylesheet" type="text/css" href="css/bootstrap.css"></link>-->
<link rel="stylesheet" type="text/css" href="css/style.css"></link>
</head>
<body>
	<div id="fb-root"></div>
    <script>
      window.fbAsyncInit = function() {
        FB.init({
          appId      : '399235116842918',
          status     : true,
          xfbml      : true
        });
      };
      (function(d, s, id){
         var js, fjs = d.getElementsByTagName(s)[0];
         if (d.getElementById(id)) {return;}
         js = d.createElement(s); js.id = id;
         js.src = "//connect.facebook.net/en_US/all.js";
         fjs.parentNode.insertBefore(js, fjs);
       }(document, 'script', 'facebook-jssdk'));
    </script>
	<div id="content">
		<?php require('db.php'); ?>
		<canvas id="mycan" width="300" height="300" style="border:1px solid #000000;"></canvas>
		<br>
		<div id="credit"></div><img src="img/add.png" width="30" height="30" id="add"/>
		<div id="potamt"></div>
		<form id="myform">
			<input type="number" placeholder=" BET Amount" name="amount" id="amt" required/>
			<input type="submit" value="BET"/>
		</form>
		<input type="button" value="Click" id="pay"/>
	</div>
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/game.js"></script>
</body>
</html>