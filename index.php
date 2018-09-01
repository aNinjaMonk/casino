	<?php include('_header.php'); ?>
	
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
		<canvas id="mycan" width="300" height="300" style="border:0px solid #000000;"></canvas>
		<br>
		<div id="credit"></div><img src="img/add.png" width="30" height="30" id="add"/>
		<div id="potamt"></div><br>
		Next Round Begins in: <div id="countdown"></div><br>
		<div id="counter"></div>
		<form id="myform">
			<input type="number" placeholder=" BET Amount" name="amount" id="amt" required/>
			<input type="submit" value="BET"/>
		</form>
		<select>
			<option>--</option>
			<option value="1">1</option>
			<option value="2">2</option>
			<option value="3">3</option>
			<option value="4">4</option>
		</select>
		<input type="text" id="selnum"></input>
		<input type="button" value="Click" id="pay"/><br>
		<input type="button" value="Start Game" id="start"/>
	</div>
	
	<?php include('_footer.php'); ?>