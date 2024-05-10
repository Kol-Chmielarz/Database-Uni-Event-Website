const urlBase = 'http://www.collegeeventwebsitecot4710.biz/LAMPAPI';
const extension = 'php';

let userId = localStorage.getItem("clientId");
let firstName = "";
let lastName = "";
let type = localStorage.getItem("typeAccess");
let email = localStorage.getItem("email");
let phone = localStorage.getItem("phone");
let uid = localStorage.getItem("uniId");
let rid = [];
let eid = [];
let editContactID = 0;

function commentCreation()
{
	
	let eid= document.getElementById("eventId").value;
	let comment= document.getElementById("comment").value;
	let rating= document.getElementById("rating").value;
	let temp = {eid:eid, pid:userId, comment:comment, rating:rating};
	let jsonPayload = JSON.stringify(temp);
	
	let url = urlBase + '/CreateComment.' + extension;
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try{
		xhr.onreadystatechange = function(){
			let jsonObject = JSON.parse(xhr.responseText);
			document.getElementById("message").innerHTML = jsonObject;
		};
		xhr.send(jsonPayload);
	}
	catch(err){
		document.getElementById("message").innerHTML = "err.message";
	}
}
function doLogin()
{
	userId = 0;
	firstName = "";
	lastName = "";
	type = "";
	email = "";
	phone = "";
	uid = 0;
	rid = [];
	eid = [];
	let username = document.getElementById("user_id").value;
	let password = document.getElementById("password").value;

	var hash = md5( password );
	
	document.getElementById("message").innerHTML = "";

	let tmp = {username:username,password:hash};

	let jsonPayload = JSON.stringify( tmp );
	
	let url = urlBase + '/Login.' + extension;

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	
	try
	{
		xhr.onreadystatechange = function() 
		{
			
			//if (this.readyState == 4 && this.status == 200) 
			//{
				let jsonObject = JSON.parse( xhr.responseText );
				userId = jsonObject.pid;
				
				if( userId < 1 )
				{		
					document.getElementById("message").innerHTML = jsonObject.error;
					return;
				}
				
		
				firstName = jsonObject.first_name;
				lastName = jsonObject.last_name;
				type = jsonObject.access;
				email = jsonObject.email;
				phone = jsonObject.phone;
				uid = jsonObject.uid;
				localStorage.setItem("user_name", username);
				localStorage.setItem("clientId", userId);
				localStorage.setItem("uniId", uid);
				localStorage.setItem("email", email);
				localStorage.setItem("phone", phone);
				localStorage.setItem("typeAccess", type);

				saveCookie();
				document.getElementById("message").innerHTML = type;
				if(type == "student"){
					window.location.href = "stud_home.html";
				}
				else if(type == "admin"){
					if(uid == 0){
						window.location.href = "Admin_ss.html";
					}
					else{
						window.location.href = "AA_home.html";
					}
				}
				else{
					window.location.href = "SA_home.html";
				}
				
			//}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("message").innerHTML = err.message;
	}

}
function createUniversity(){
	let name= document.getElementById("Uni_name").value;
	let description = document.getElementById("description").value.toString();
	let stdCount= document.getElementById("students").value;
	//let picture = document.getElementById("university_image").value;
	let location = document.getElementById("location").value;
	let temp = {name:name, description:description, stdCount:stdCount, location:location, pid:userId};
	let jsonPayload = JSON.stringify(temp);
	let url = urlBase + '/CreateUniversity.' + extension;
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try{
		xhr.onreadystatechange = function(){
			let jsonObject = JSON.parse(xhr.responseText);
			document.getElementById("message").innerHTML = jsonObject.id;
		};
		xhr.send(jsonPayload);
	}
	catch(err){
		document.getElementById("message").innerHTML = "University already exists";
	}
}
function createRSO(){
	let name= document.getElementById("RSO_name").value;
	let description = document.getElementById("RSO_description").value.toString();
	let temp = {name:name, description:description, pid:userId};
	let jsonPayload = JSON.stringify(temp);
	let url = urlBase + '/CreateRSO.' + extension;
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try{
		xhr.onreadystatechange = function(){
			let jsonObject = JSON.parse(xhr.responseText);
			document.getElementById("message").innerHTML = jsonObject.id;
			if(jsonObject.id > 0){
				document.getElementById("message").innerHTML = "RSO already exists";
			}
			else{
				document.getElementById("message").innerHTML = "RSO Created";
				window.location.href = "/AA_home.html";
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err){
		document.getElementById("message").innerHTML = err.message;
	}
}
function doRSOSignUp()
{
	let r = document.getElementById("RSO").value;
	let tmp = {pid:userId,rid:r};
	let jsonPayload = JSON.stringify( tmp );
	
	let url = urlBase + '/SignUpRSO.' + extension; 
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			// if (this.readyState == 4 && this.status == 200) 
			// {
				let jsonObject = JSON.parse(xhr.responseText);
				let d= jsonObject.id;
		
				if(d <1 )
				{		
					document.getElementById("message").innerHTML = "Joined RSO succesfully";
					rid.push(r);
				}
				else{
					document.getElementById("message").innerHTML = "You are already in this RSO";
				}

			// }
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("message").innerHTML = err.message;
	}

}
function adminUniSignUp(){
	uid = document.getElementById("university").value;
	if(uid == 0)
	{
		document.getElementById("message").innerHTML ="Fill in all SignUp information" ;
		return;
	}
	let tmp = {pid:userId, uid:uid};
	let jsonPayload = JSON.stringify( tmp );
	let url = urlBase + '/SignUpAdminUniversity.' + extension; 
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	document.getElementById("message").innerHTML = userId + " " + uid ;
	try
	{
		xhr.onreadystatechange = function() 
		{
			// if (this.readyState == 4 && this.status == 200) 
			// {
				window.location.href =  "/AA_home.html";
				
			// }
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("message").innerHTML = err.message;
	}
}
function doUniversitySignUp()
{
	let tmp = {pid:userId, uid:uid, access:type};
	let jsonPayload = JSON.stringify( tmp );
	
	let url = urlBase + '/SignUpUniversity.' + extension; 
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	document.getElementById("message").innerHTML = userId + " " + uid + " " + type;
	try
	{
		xhr.onreadystatechange = function() 
		{
			// if (this.readyState == 4 && this.status == 200) 
			// {
				let jsonObject = JSON.parse(xhr.responseText);
				let d = jsonObject.id;
				document.getElementById("message").innerHTML = d;
				if(d <1 )
				{		
					window.location.href = "/stud_home.html"; 
					
				}
				else{
					window.location.href =  "/AA_home.html";
				}

			// }
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("message").innerHTML = err.message;
	}

}
function getUniversities(){
	let url = urlBase + '/DisplayUniversities.' + extension;
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (xhr.readyState === XMLHttpRequest.DONE) {
				let jsonObject = JSON.parse(xhr.responseText);
				
				for(x=0; x< jsonObject.results.length;x++)
				{
					let name = jsonObject.results[x].name;
					var newOp = document.createElement("option");
					newOp.value = jsonObject.results[x].uid;
					newOp.textContent = name;
					document.getElementById("university").appendChild(newOp);
				}
			}
		}
        xhr.send();
	}
	catch(err)
	{
		document.getElementById("message").innerHTML = err.message;
	}
				
}
function getRSO(){
	let url = urlBase + '/DisplayRSO.' + extension;
	let tmp = {uid:uid};
	let jsonPayload = JSON.stringify( tmp );
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (xhr.readyState === XMLHttpRequest.DONE) {
				
				let jsonObject = JSON.parse(xhr.responseText);
				if (jsonObject.error != ""){
					document.getElementById("message").innerHTML = jsonObject.error;
				}
				
				for(x=0; x< jsonObject.results.length;x++)
				{
					let name = jsonObject.results[x].name;
					var newOp = document.createElement("option");
					newOp.value = jsonObject.results[x].rid;
					newOp.textContent = name;
					document.getElementById("RSO").appendChild(newOp);
				}
			}
		}
        xhr.send();
	}
	catch(err)
	{
		document.getElementById("message").innerHTML = err.message;
	}
				
}
function getYourRSOAdmin(){
	let url = urlBase + '/DisplayYourRSO.' + extension;
	let tmp = {pid:userId};
	let jsonPayload = JSON.stringify( tmp );
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	
	
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (xhr.readyState === XMLHttpRequest.DONE) {
				
				let jsonObject = JSON.parse(xhr.responseText);
				if (jsonObject.error != ""){
					document.getElementById("message").innerHTML = "";
				}
				else{
					for(x=0; x< jsonObject.results.length;x++)
					{
						let name = jsonObject.results[x].name;
						var newOp = document.createElement("option");
						newOp.value = jsonObject.results[x].rid;
						newOp.textContent = name;
						document.getElementById("eventCategory").appendChild(newOp);
					}
				}
				
			}
		}
        xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("message").innerHTML = err.message;
	}
				
}
function doSignUp(t)
{
	userId = 0;
	username = "";
	firstName ="";
	lastName="";
	type = "";


	firstName = document.getElementById("name").value;
	lastName = document.getElementById("lastname").value;
	let login = document.getElementById("user_id").value;
	let password = document.getElementById("password").value;
	email = document.getElementById("email").value;
	phone = document.getElementById("phone").value;
	

	if(login == "" || password == "" || firstName == "" || lastName == "" || email == "" || phone == "")
	{
		document.getElementById("message").innerHTML ="Fill in all SignUp information";
		return;
	}
	if(t==1  && (login.length < 3 || (login.substring(0,2) != "AA" && login.substring(0,2) != "SA"))){
		document.getElementById("message").innerHTML ="Useraname must start with AA for Admin or SA for Super Admin";
		return;
	}
	else if (t!=1){
		uid = document.getElementById("university").value;
		if(uid == 0)
		{
			document.getElementById("message").innerHTML ="Fill in all SignUp information" ;
			return;
		}
	}
	
	var hash = md5( password );
	
	if(login.substring(0,2) == "AA" &&t==1 ){
		type = "admin";
	}
	else if(login.substring(0,2) == "SA" && t==1 ){
		type = "super admin";
	}
	else {
		type = "student";
	}
	
	let tmp = {login:login, password:hash, firstName:firstName, lastName:lastName , email:email, phone:phone, access:type};
	let jsonPayload = JSON.stringify( tmp );
	
	let url = urlBase + '/SignUp.' + extension; 
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	
	
	try
	{
		xhr.onreadystatechange = function() 
		{
			// if (this.readyState == 4 && this.status == 200) 
			// {
				document.getElementById("message").innerHTML = type + " " + phone + " " +lastName+" " + email + " " + firstName +" "+ hash + " "+ login;
				let jsonObject = JSON.parse(xhr.responseText);
				
				userId = jsonObject.id;
		
				if( userId > 1 )
				{		
					document.getElementById("message").innerHTML = "Username already taken";
					document.getElementById("message").style.display = "block";
					return;
					
				}
				else{
					if(type != "super admin"){
						userId = userId*-1;
						document.getElementById("message").innerHTML = "raaa";
						doUniversitySignUp();
					}
					window.location.href =  "/index.html";

				}
			// }
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("message").innerHTML = err.message;
	}
	
}

function joinEvent(eeid)
{
	let d = document.getElementById("publicEventDropdown");
	if (eeid == "public"){
		d = document.getElementById("publicEventDropdown");
		
	}
	else if (eeid == "private"){
		d = document.getElementById("privEventDropdown");
	}
	else{
		d = document.getElementById("RSOEventDropdown");
	}
	
	let tmp = {pid:userId,eid:d.value};
	let jsonPayload = JSON.stringify( tmp );
	let url = urlBase + '/JoinEvent.' + extension; 
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			// if (this.readyState == 4 && this.status == 200) 
			// {
				let jsonObject = JSON.parse(xhr.responseText);
				document.getElementById("message").innerHTML = jsonObject.error;

			// }
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("message").innerHTML = err.message;
	}

}
function createEventPls()
{	
	let name= document.getElementById("eventName").value;
	let description = document.getElementById("description").value;
	let category= document.getElementById("eventCategory").value;
	let time = document.getElementById("date").value + " " + document.getElementById("time").value + ":00 ";
	let emailCon = document.getElementById("contactEmail").value;
	let phoneCon = document.getElementById("contactPhone").value;
	let location = document.getElementById("Location").value;
	let temp = {name:name, description:description, category:category, time:time, location:location, pid:userId, email:emailCon, phone:phoneCon};
	let jsonPayload = JSON.stringify(temp);
	let url = urlBase + '/CreateEvent.' + extension;
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try{
		xhr.onreadystatechange = function() {
			if (this.readyState === XMLHttpRequest.DONE) {
				if (this.status === 200) {
					document.getElementById("message").innerHTML = "Event Created";
				} else {
					// Handle HTTP errors here
					document.getElementById("message").innerHTML = "Failed to create event. Server responded with status: " + this.status;
				}
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err){
		document.getElementById("message").innerHTML = err.message;
	}

}
function eventPage(){
	
	var urlParams = new URLSearchParams(window.location.search);
	var eventId = urlParams.get('eid');
	let temp = {eid:eventId};
	
	let jsonPayload = JSON.stringify(temp);
	let url = urlBase + '/GetEvent.' + extension;
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try{
		xhr.onreadystatechange = function(){
			let jsonObject = JSON.parse(xhr.responseText);
			
			document.getElementById("eventName").innerHTML = jsonObject.name;
			document.getElementById("eventCategory").innerHTML = jsonObject.category;
			document.getElementById("eventDescription").innerHTML = jsonObject.description;
			var [date, time] = jsonObject.time.split(" ");
			document.getElementById("eventDate").innerHTML = date;
			document.getElementById("eventTime").innerHTML = time;
			document.getElementById("contactPhone").innerHTML = jsonObject.phone;
			document.getElementById("contactEmail").innerHTML = jsonObject.email;
			document.getElementById("eventLocation").innerHTML = jsonObject.location;
			document.getElementById("eventId").value = eventId;
			displayComments(eventId);
		};
		xhr.send(jsonPayload);
	}
	catch(err){
		document.getElementById("message").innerHTML = err.message;
	}

}

function editComment(cid)
{	
	let comment= document.getElementById("editCommentText").value;
	document.getElementById("message").innerHTML = comment +" "+cid;
	let temp = {cid:cid, pid:userId, comment:comment};
	let jsonPayload = JSON.stringify(temp);
	let url = urlBase + '/EditComment.' + extension;
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try{
		xhr.onreadystatechange = function(){
			let jsonObject = JSON.parse(xhr.responseText);
		};
		xhr.send(jsonPayload);
	}
	catch(err){
		document.getElementById("message").innerHTML = err.message;
	}

}
function deleteComment(cid)
{	
	let temp = {cid:cid, pid:userId};
	let jsonPayload = JSON.stringify(temp);
	let url = urlBase + '/DeleteComment.' + extension;
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try{
		xhr.onreadystatechange = function(){
			let jsonObject = JSON.parse(xhr.responseText);
		};
		xhr.send(jsonPayload);
	}
	catch(err){
		document.getElementById("message").innerHTML = err.message;
	}
}
function displayComments()
{
	var urlParams = new URLSearchParams(window.location.search);
	var eventId = urlParams.get('eid');
	
	let xhr = new XMLHttpRequest();
	let url = urlBase + '/DisplayComments.' + extension;
	let tmp = {pid:userId, eid:eventId};
	let jsonPayload = JSON.stringify( tmp );
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	
	try
	{
		xhr.onreadystatechange = function() 
		{
			
			if (this.readyState == 4 && this.status == 200) 
			{		
				myObj = JSON.parse( xhr.responseText );
				
				let text = "";
				if(myObj.error == "No Records Found")
				{
					document.getElementById("commentsList").innerHTML= text;
				}
				else
				{
					
					text = "<table class='no-border-table'>"; // Opening table tag moved outside the loop

					for (var x = 0; x < myObj.results.length; x++) {
						var cid = myObj.results[x].cid.toString();
						var pid = myObj.results[x].username.toString();
						var comment = myObj.results[x].comments.toString();
						var rating = myObj.results[x].rating.toString();
						var time = myObj.results[x].timestamp.toString();
						// Rest of the table structure
						text += "<tr>";
						text += "<th style='text-align: center; padding-right: 50px;'>" +  pid + "'s comment:</th>";
						text += "<th style='text-align: center; padding: 10px;'>Rating</th>";
						text += "<th style='text-align: center; padding: 10px;'>Time</th>";
						text += "</tr>";
						text += "<td style='text-align: left'>" + comment + "</td>"; // rating
						text += "<td style='text-align: center'>" + rating + "</td>"; // rating
						text += "<td style='text-align: center'>" + time + "</td>"; // Time
						text += "</tr>";
						if(myObj.results[x].pid == userId){
							text += "<tr>";
							text += "<td colspan='3'>";
							text += "<button class='join' onclick='deleteComment(" + cid + ");window.location.reload();'>Delete Comment</button>";
							text += "<button class='edit' onclick='editCommentPop(" + cid + ", \""+comment+"\")'>Edit Comment</button>";
							text += "</td>";
							text += "</tr>";
							
						}
						
						
					}

					text += "</table>"; // Closing table tag after the loop

					document.getElementById("commentsList").innerHTML = text;

				}
					
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("message").innerHTML = err.message;
	}
	
}
function editCommentPop(cid, commentText) {
    // Assuming you have access to the comment text, you can pre-fill the textarea with it
	document.getElementById("message").innerHTML = cid +" "+commentText;
    document.getElementById("editCommentId").value = cid;
    document.getElementById("editCommentText").value = commentText;

    // Show the edit comment form
    document.getElementById("editCommentForm").style.display = "block";
}
function cancelEdit() {
	document.getElementById("editCommentForm").style.display = 'none';
}

function displayEvents(category)
{
	let d;
	if (category == "public"){
		d = document.getElementById("publicEventDropdown");
	}
	else if (category == "private"){
		d = document.getElementById("privEventDropdown");
	}
	else{
		d = document.getElementById("RSOEventDropdown");
	}
	let xhr = new XMLHttpRequest();
	let url = urlBase + '/DisplayEvent.' + extension;
	let tmp = {pid:userId, category:category, rid:rid, uid:uid};
	let jsonPayload = JSON.stringify( tmp );
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

	try
	{
		xhr.onreadystatechange = function() 
		{

			jsonObject = JSON.parse( xhr.responseText );
				
			if(jsonObject.error == "No Records Found")
			{
				document.getElementById("message").innerHTML= "No Records Found";
			}
			else
			{
				// Clear existing options
				d.innerHTML = "";
				var defaultOption = document.createElement("option");
				defaultOption.value = "0";
				defaultOption.disabled = true;
				defaultOption.selected = true;
				defaultOption.textContent = "Select a "+category+" event";
				d.appendChild(defaultOption);
				for (var x = 0; x < jsonObject.results.length; x++) {
					let name = jsonObject.results[x].name;
					var newOp = document.createElement("option");
					
					newOp.value = jsonObject.results[x].eid;
					newOp.textContent = name;
					
					d.appendChild(newOp);
				}

			}
					
			
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("message").innerHTML = err.message;
	}
	
}
function leaveRSO(eeid)
{	
	let temp = {rid:eeid, pid:userId};
	let jsonPayload = JSON.stringify(temp);
	let url = urlBase + '/LeaveRSO.' + extension;
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try{
		xhr.onreadystatechange = function(){
			let jsonObject = JSON.parse(xhr.responseText);
		};
		xhr.send(jsonPayload);
	}
	catch(err){
		document.getElementById("message").innerHTML = err.message;
	}
}
function leaveEvent(eeid)
{	
	let temp = {eid:eeid, pid:userId};
	let jsonPayload = JSON.stringify(temp);
	let url = urlBase + '/LeaveEvent.' + extension;
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try{
		xhr.onreadystatechange = function(){
			let jsonObject = JSON.parse(xhr.responseText);
		};
		xhr.send(jsonPayload);
	}
	catch(err){
		document.getElementById("message").innerHTML = err.message;
	}
	
}
function getMyEvents()
{
	let xhr = new XMLHttpRequest();
	let url = urlBase + '/DisplaySavedEvents.' + extension;
	let tmp = {pid:userId};
	let jsonPayload = JSON.stringify( tmp );
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	
	try
	{
		xhr.onreadystatechange = function() 
		{
			
			myObj = JSON.parse( xhr.responseText );
			let text = "";
			if(myObj.error == "No Records Found")
			{
				document.getElementById("eventsList").innerHTML= text;
			}
			else
			{
				
				
				text = "<table class='no-border-table'>"; // Opening table tag moved outside the loop

				for (var x = 0; x < myObj.results.length; x++) {
					var eid = myObj.results[x].eid.toString();
					var name = myObj.results[x].name.toString();
					var location = myObj.results[x].location.toString();
					var eventEmail = myObj.results[x].email.toString();
					var eventPhone = myObj.results[x].phone.toString();
					var time = myObj.results[x].time.toString();

					// Start of new table row
					text += "<tr>";
					text += "<td><b>" + name + "</b></td>"; // Name
					text += "</tr>";

					// Start of another new table row
					text += "<tr>";
					text += "<td>" + myObj.results[x].description + "</td>"; // Description
					text += "</tr>";
					
					// Rest of the table structure
					text += "<tr><th style='text-align: center'>Phone</th>";
					text += "<th style='text-align: center'>Email</th>";
					text += "<th style='text-align: center'>Location</th>";
					text += "<th style='text-align: center'>Time</th></tr>";
					
					text += "<tr>";
					text += "<td style='text-align: center'>" + eventPhone + "</td>"; // Phone
					text += "<td style='text-align: center'>" + eventEmail + "</td>"; // Email
					text += "<td style='text-align: center'>" + location + "</td>"; // Location
					text += "<td style='text-align: center'>" + time + "</td>"; // Time
					text += "</tr>";
					text += "<tr>";
					text += "<td colspan='4'><button class='join' onclick='leaveEvent(" + eid + ");window.location.reload();'>Leave Event</button></td>"; // Spanning 4 columns for join button
					text += "</tr>";
				}
				
				text += "</table>"; // Closing table tag after the loop

				document.getElementById("eventsList").innerHTML = text;
				

			}
			getMyRSO();
					
			
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("message").innerHTML = err.message;
	}
	
}
function getMyRSO()
{
	let xhr = new XMLHttpRequest();
	let url = urlBase + '/DisplayPageRSO.' + extension;
	let tmp = {pid:userId};
	let jsonPayload = JSON.stringify( tmp );
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	
	try
	{
		xhr.onreadystatechange = function() 
		{
			myObj = JSON.parse( xhr.responseText );
			let text = "";
			if(myObj.error == "No Records Found")
			{
				document.getElementById("myRSOList").innerHTML= text;
			}
			else
			{
				
				
				text = "<table class='no-border-table'>"; // Opening table tag moved outside the loop

				for (var x = 0; x < myObj.results.length; x++) {
					var rid = myObj.results[x].rid.toString();
					var name = myObj.results[x].name.toString();
					var statusRSO = myObj.results[x].status.toString();
					text += "<tr><th style='text-align: center'>"+name+"</th>";
					text += "<th style='text-align: center'>Status</th></tr>";
					
					text += "<tr>";
					text += "<td style='text-align: center; padding-right: 50px;'>" + myObj.results[x].description + "</td>"; // Phone
					text += "<td style='text-align: center'>" + statusRSO + "</td>"; // Phone
					text += "</tr>";

					text += "<tr>";
					text += "<td colspan='4'><button class='join' onclick='leaveRSO(" + rid + ");window.location.reload();'>Leave RSO</button></td>"; // Spanning 4 columns for join button
					text += "</tr>";
				}
				
				text += "</table>"; // Closing table tag after the loop

				document.getElementById("myRSOList").innerHTML = text;

			}
					
			
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("message").innerHTML = "err.message";
	}
	
}
function saveCookie()
{
	let minutes = 20;
	let date = new Date();
	date.setTime(date.getTime()+(minutes*60*1000));	
	document.cookie = "firstName=" + firstName + ",lastName=" + lastName + ",userId=" + userId + ";expires=" + date.toGMTString();
}

function readCookie()
{
	userId = -1;
	let data = document.cookie;
	let splits = data.split(",");
	for(var i = 0; i < splits.length; i++) 
	{
		let thisOne = splits[i].trim();
		let tokens = thisOne.split("=");
		if( tokens[0] == "firstName" )
		{
			firstName = tokens[1];
		}
		else if( tokens[0] == "lastName" )
		{
			lastName = tokens[1];
		}
		else if( tokens[0] == "userId" )
		{
			userId = parseInt( tokens[1].trim() );
		}
	}
	
	if( userId < 0 )
	{
		window.location.href = "index.html";
	}
	else
	{
		document.getElementById("userName").innerHTML = "Logged in as " + firstName + " " + lastName;
	}
}

function doLogout()
{
	userId = 0;
	firstName = "";
	lastName = "";
	type = "";
	email = "";
	phone = "";
	uid = 0;
	rid = [];
	eid = [];
	document.cookie = "firstName= ; expires = Thu, 01 Jan 1970 00:00:00 GMT";
	window.location.href = "/index.html";
}




