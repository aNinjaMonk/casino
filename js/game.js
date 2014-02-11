//Facebook login... 
//...Store the user id into the database.
//...Show view 

//Fetch the last winner account info,pic and put in the frame.

//Rotate the wheel every hour.
//Show the clock and current timing.

//Allow someone to bid on the possible numbers.

	window.addEventListener("load", init); 
	
	var counter = 0,
		logoImage = new Image(),
		dealer = new Image(),
		TO_RADIANS = Math.PI/180;
	
	logoImage.src = 'img/wheel.jpg';
	dealer.src = 'img/dealer.png';
	var canvas = document.getElementById('mycan');
    canvas.width = 600; 
	canvas.height = 400;
	
	var context = canvas.getContext('2d');	
	var img=document.getElementById("image");
	var myVar;
	var userid;
	
	function init(){
		/*$('#counter').countdown({
			stepTime: 60,
			format: 'hh:mm:ss',
			startTime: "12:32:55",
			digitImages: 6,
			digitWidth: 53,
			digitHeight: 77,
			timerEnd: function() {  },
			image: "img/digits.png"
		  });
		 */
		 /*
		var liftoffTime = 5;
		$('#counter').countdown({until: liftoffTime, format: 'S', 
					onTick: everyFive, tickInterval: 5});
		
		function everyFive(periods) {
			$('#counter').text(periods[4] + ':' + (periods[5]) + 
				':' + (periods[6])); 
		}*/
		$("select").change(function(){			
			$("#selnum").val($("select").val());
		});
		$("#pay").click(function(){
			FB.ui({
				method: 'pay',
				action: 'purchaseitem',
				product: 'http://getricheveryhour.herokuapp.com/og/coin.html', 
				quantity: 10
			},function(data){
				console.log(data);
			});
		});
		
		FB.getLoginStatus(function(response) {
		  if (response.status === 'connected') {
			// the user is logged in and has authenticated your
			// app, and response.authResponse supplies
			// the user's ID, a valid access token, a signed
			// request, and the time the access token 
			// and signed request each expire
			var uid = response.authResponse.userID;
			var accessToken = response.authResponse.accessToken;			
			userid = uid;
			//myVar = setInterval(loop, 1000/30);
			GetUserData();			
		  } else if (response.status === 'not_authorized') {
			// the user is logged in to Facebook, 
			// but has not authenticated your app
			alert('not authorizd');
			FB.login(function(response){
				if (response.status === 'connected') {
					alert('connected');
				}
			},{scope: 'email,user_likes,read_friendlists,user_online_presence,publish_actions,publish_stream'});
		  } else {
			// the user isn't logged in to Facebook.
			
			FB.login(function(response){
				if (response.status === 'connected') {
					
					myVar = setInterval(loop, 1000/30);
				}
			},{scope: 'email,user_likes,read_friendlists,user_online_presence,publish_actions,publish_stream'});
			/*FB.Event.subscribe('auth.authResponseChange', function(response) {
				if (response.status === 'connected') {
				  console.log('Logged in');
				} else {
				  FB.login();
				}
			  });*/
		  }
		 });
	}
	function logout(){
		FB.logout(function(response) {
			// Person is now logged out
		});
	}
	function loop() { 
		context.clearRect(0,0,canvas.width, canvas.height); 
		drawRotatedImage(logoImage,150,150,counter);		
		context.translate(400,0);
		context.scale(-1,1);
		context.drawImage(dealer,0,0);
		counter+=2;	
		if(counter == 360)
			counter = 0;
	}	
	
	function drawRotatedImage(image, x, y, angle) { 

		// save the current co-ordinate system 
		// before we screw with it
		context.save(); 

		// move to the middle of where we want to draw our image
		context.translate(x, y);

		// rotate around that point, converting our 
		// angle from degrees to radians
		context.rotate(angle * TO_RADIANS);

		// draw it up and to the left by half the width
		// and height of the image 
		context.drawImage(image, -(image.width/2), -(image.height/2));
		
		// and restore the co-ords to how they were when we began
		context.restore(); 
	}
	function GetUserData(){
		//TO-DO : Get user photo from facebook.
		FB.api('/me',function(response){			
			$.get("db.php",{queryid : 3,email: response.email,fbid: response.id,firstname: response.first_name,lastname: response.last_name},function(data,status){
				if(status == "success"){
					//alert(data);
				}
				else{
					alert('error');
				}
			});			
			$.get("db.php",{queryid : 4,fbid: response.id},function(data,status){
				if(status == "success"){
					$("#credit").html("CREDITS :" + data);
				}
				else{
					alert('error');
				}
			});	
			$.get("db.php",{queryid : 5,fbid: response.id},function(data,status){
				if(status == "success"){
					$("#potamt").html("POT AMOUNT :" + data);
				}
				else{
					alert('error');
				}
			});	
		});
	}
	function GetDateTime(){
		var d = new Date();
		//fetch time from db. - lastTime
		//substract from current time.. currentTime. If  last + required > current then 
		//put and 
		//I have to run this time thing even if no one has opened the application.. hence it can not be done in client side. 
		//Needs to be done periodically on the server.. db updation part - lastTime + required into db.
		//
		
		//1970 - now
		
		//Algorithm
		/*
			India - 5 PM 30 Jan 17:06 | New York - 30 Jan 06:35
			get time from the server.
			Get previous round time and then calculate hte time which has passed from the last time. if it is equal to the timegap provided then start the round.
			
			get NextRound from gameon. If I put datetime then it is gonna be different for all places - china,india. 
			Get Datetime from server.. that should do!
			
			function getDateTime(){
				//get time from system clock.. and check how much time remains till 
				var d = new date();
				alert(d.getTime());
				
				var lastTimerStop = 1391402644677;
				var timeGap = 60*30;
				var timerStop = lastTimerStop + timeGap;
				
				//get system time. 
				var d = new date();
				alert(d.getTime());
				
				//Get the time remaining till next timerStop and show the time countdown.
				
				
			}
		*/
	
	
	}
	function StartRound(){
		myVar = setInterval(loop, 1000);
		
	}
	$(document).ready(function(){
		//$("#countdown").val("abhi");
		//1391402644677
		
		GetDateTime();
		$("#myform").submit(function(e){			
			event.preventDefault();
			if($('#selnum').val() == ''){
				alert('select number you want to bet on');
				return;
			}
			else{				
				$.get("db.php",{queryid : 2,betamt:$("#amt").val(),fbid:userid},function(data,status){
					if(status == "success"){
						GetUserData();
						$("#amt").val('');						
					}
					else{
						alert('error');
					}
				});
			}
		});
		$("#add").click(function(e){
			alert('Buy Credits here!');
		});
		$("#start").click(function(e){
			StartRound(); 
		});
	});
	
	//login failing... some error .login called before fb being initialized.
	//Instead of the popup for login it should show up in hte same window.
	
	//Get a fucking timer which keeps track of the time and automatically starts the game once it's the right time.
	//Eveyr hour play the roll the wheel.
	
	//Rule of the game: 
	
	//Get the bet number...
	//Think from perspective of a facebook app.
	
	//Lights on and off around the wheel portions.
	//Bets terms. Amount betted X number betted on. 
	//Add Joker.
	//
	//No registration.. no pay terms, no graphics complexity, 
	//Focus on one thing.. Addiction!!
	
	//