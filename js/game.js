//Facebook login... 
//...Store the user id into the database.
//...Show view 

//Fetch the last winner account info,pic and put in the frame.

//Rotate the wheel every hour.

//Show the clock and current timing.

//Allow someone to bid on the possible numbers.

// Store the facebook id of the user in the database.

// 
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
	
	function init(){	
		//$("#timeleft").html("Time Left: " + "hi" + "<br> Total pot amount : 100");
		
		//myVar = setInterval(loop, 1000/30);
		FB.getLoginStatus(function(response) {
		  if (response.status === 'connected') {
			// the user is logged in and has authenticated your
			// app, and response.authResponse supplies
			// the user's ID, a valid access token, a signed
			// request, and the time the access token 
			// and signed request each expire
			var uid = response.authResponse.userID;
			var accessToken = response.authResponse.accessToken;
			//alert(uid);
		  } else if (response.status === 'not_authorized') {
			// the user is logged in to Facebook, 
			// but has not authenticated your app
			//alert('not authorizd');
			FB.login(function(response){
				if (response.status === 'connected') {
					alert('connected');
				}
			},{scope: 'email,user_likes,read_friendlists,user_online_presence,publish_actions,publish_stream'});
		  } else {
			// the user isn't logged in to Facebook.
			FB.login(function(response){
				if (response.status === 'connected') {
					alert('connected');
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
	
	$(document).ready(function(){
		$("#myform").submit(function(e){
			event.preventDefault();
			
			$.get("db.php",{queryid : 1,amt:$("#amt").val()},function(data,status){
				if(status == "success")
				{
					var obj = JSON.parse(data) || $.parseJSON(data);				
					alert(obj);
				}
				else
				{
					alert('error');
				}
			});
		});		
	});
	  