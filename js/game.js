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
		TO_RADIANS = Math.PI/180;
	
	logoImage.src = 'img/wheel.jpg';
	
	var canvas = document.getElementById('mycan');
    canvas.width = 300; 
	canvas.height = 300;
	
	var context = canvas.getContext('2d');	
	var img=document.getElementById("image");
	var myVar;
	var userid;
	
	function init(){	
		//$("#timeleft").html("Time Left: " + "hi" + "<br> Total pot amount : 100");
		
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
					alert(uid);
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
		$.get("db.php",{queryid : 2,betamt:$("#amt").val(),fbid:userid},function(data,status){
				if(status == "success"){
					alert(data);
				}
				else{
					alert('error');
				}
			});	
	}
	function StartRound(){
		myVar = setInterval(loop, 1000/30);
		
	}
	$(document).ready(function(){
	
		GetDateTime();
		$("#myform").submit(function(e){
			event.preventDefault();
			
			$.get("db.php",{queryid : 2,betamt:$("#amt").val(),fbid:userid},function(data,status){
				if(status == "success"){
					GetUserData();
					$("#amt").val('');
					StartRound();
				}
				else{
					alert('error');
				}
			});			
		});
		$("#add").click(function(e){
			alert('Buy Credits here!');
		});
	});
	
	//login failing... some error
	//Instead of the popup for login it should show up in hte same window.
	
	//Get a fucking timer which keeps track of the time and automatically starts the game once it's the right time.
	//Eveyr hour play the roll the wheel.
	
	//Algorithm
	/*
		India - 5 PM 30 Jan 17:06 | New York - 30 Jan 06:35
		get time from the server.
		Get previous round time and then calculate hte time which has passed from the last time. if it is equal to the timegap provided then start the round.
		
		get NextRound from gameon. If I put datetime then it is gonna be different for all places - china,india. 
		Get Datetime from server.. that should do!
			
	*/
	
	//Get the bet number... 
	//