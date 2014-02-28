//Ugly Ugly Ugly Proof of concept for C2P. 
$(document).ready(function(){
	$("#passFormTable textArea").val("How verified:\nIS?:\nReplicated yourself?:\nProblem address:\nDecription of the issue:\n\n\n\nTroubleshooting so far:\n\n\n");
	document.sessionObj = new viewSession();
	$("#passButton").bind("click", function(){
		document.sessionObj.submitChat();
	});
	$("#viewQueueButton").bind("click", function(){
		document.sessionObj.displayChatType("queued");
	});
	$("#viewPassedButton").bind("click", function(){
		document.sessionObj.displayChatType("passing");
	});
	$("#viewReceivedButton").bind("click", function(){
		document.sessionObj.displayChatType("received");
	});
	$("#actionPassButton").bind("click", function(){
		document.sessionObj.actionSelect($('#actionContainer'),'actionPass')	
		
	});
})


//container class to simplify scope.
function viewSession(){
	this.name = null;
	this.queued = []; //chats in queue
	this.received = []; //passed chats that you've accepted
	this.passing = []; //chats that you placed in queue
	this.messages = []; //message list, displaying notices when a chat you'e passed is accepted. 
	this.selectedType = "queued"; //Determines which chat type is displayed in the chat window for refreshes.
	

	
	//takes a clicked chat and displays its details in the right info pane. 
	this.selectChat = function(type, id){
		var _this = this;
		var typeList = [];
		switch(type){
		case "queued":
			typeList = _this.queued;
			break;
		case "passing":
			typeList = _this.passing;
			break;
		case "received":
			typeList = _this.received;
			break;
		default:
			alert("Error: Chat type not recognized. Contact the government.");
		}
		
		var chat = typeList[id];
		if(id in typeList){
			$("#viewChatPasser").html(chat['passer_name']);
			$("#viewChatReceiver").html(chat['taker_name']);
			$("#viewChatCustomer").html(chat['cust_name']);
			$("#viewChatID").html(chat['chat_id']);
			$("#viewChatURL").html(this.urlToLink(chat['billing_url']));
			$("#viewChatDeets").html(chat['deets'].replace(/\n/gi,"<br/>"));

			_this.actionSelect($('#actionContainer'), 'actionDetails');
		}
	}
	
	//Display queued, passing, or received in the chat list. FIX THIS
	this.displayChatType = function(type){
		this.selectedType = type;
		var _this = this;
		$("#chatList").empty();
		var items = [];
		switch(type){
			case "queued":
			   if(_this.queued){
				$.each( _this.queued , function( key, chat ) {
					var timeStr = _this.timestampToString(chat["time_submitted"])
					var liAttrs = "type='queued'";
					var spanL = "<span>"+chat["passer_name"]+" </span><span>"+timeStr+"</span>";
					var spanLClicked = "document.sessionObj.selectChat(\"queued\",\""+ chat["chat_id"]+"\")";
					var spanR = "<button onClick='document.sessionObj.takeChat(\""+chat["id"]+"\","+chat["chat_id"]+")'>Accept</button>";
					_this.__makeChatItem(liAttrs, spanL, spanLClicked, spanR);
				});
			   }
				break;
			case "passing":
				if(_this.passing){
					$.each( _this.passing , function( key, chat ) {
						var liAttrs = "type='passing' id='"+ chat["chat_id"]+"'";
						var spanL = chat["cust_name"];
						var spanLClicked = 'document.sessionObj.selectChat("passing","'+ chat["chat_id"]+'")';
						var spanR = "<button onClick='document.sessionObj.cancelChat(\""+ chat["id"]+"\")'>Cancel</button>";
						_this.__makeChatItem(liAttrs, spanL, spanLClicked, spanR);
					});
				}
				break;
			case "received":
				if(_this.received){
					$.each(_this.received , function( key, chat ) {
						var liAttrs = "type='received' id='"+ chat["chat_id"]+"'";
						var spanL = chat["cust_name"];
						var spanLClicked = 'document.sessionObj.selectChat("received","'+chat["chat_id"]+'")';
						_this.__makeChatItem(liAttrs, spanL, spanLClicked, "");
					});
				}
				break;
			default:
				alert("Error: Chat type not recognized. Contact the government.");
		}
	}
	
	//Puts chats into or
	this.__makeChatItem = function(liAttrs, spanL, spanLClicked, spanR){
		$("#chatList").append("<li "+liAttrs+"><span style='left' onClick='"+spanLClicked+"'>"+spanL+"</span><span style='right'>"+spanR+"</span></li>");		
	}

	 
	 //Pull all messages for your name from the DB. 
	 this.getMessages = function(){
		 var _this = this;
		 this.doAjax("getMessages", {name:_this.name}, function( returnVal ) {
			 _this.messages = returnVal;
			 $("#messages").empty();
			 var  msgs = [];
			 $.each( _this.messages , function( id, msg ) {
				 $("#messages").append("<li id='"+id+"'><span>"+msg['msg']+"</span><button class='right' onClick='document.sessionObj.confirmMsg(\""+id+"\")'>It's Passed!</button></li> ");
			 });
			 if($("#messages li").length > 0){
				 $("#messageBox").show();
			 }else{
				 $("#messageBox").hide();
			 }

		 });

	 }
	 
	//pulls all queued and passed chats from c2p.php, and displays them in their appropriate list
	this.getChatData = function(){
		var _this = this
		 this.doAjax("getChats", {name:_this.name}, function( returnVal ) {
			_this.queued = returnVal['queued'];
			_this.passing = returnVal['passing'];
			_this.received = returnVal['received'];
			_this.getMessages();
			_this.displayChatType(_this.selectedType);
			});
	}	 
	 
	 
	 //deletes the associated message from the DB when confirmed. 
	 this.confirmMsg = function(msgId){
		 var _this = this;
			this.doAjax("deleteMsg", {id: msgId }, function( returnVal ) {
				_this.getMessages();
			});
	 }	 
	 
	// Submit a new chat to the queue
	this.submitChat = function(){
		var _this = this;
		if(!_this.validateSubmit()){
			return
		}
	   this.doAjax("submitChat", {sender: _this.name, passName: $("#passName").val(), passID: $("#passID").val(), passURL: $("#passURL").val(), passDeets: $("#passDeets").val() }, function( returnVal ) {
			$("#passFormTable input").each(function(e){$(this).val("")});
	  		$("#passFormTable textArea").each(function(e){$(this).val("")});
			$("#passFormTable textArea").val("How verified:\nIS?:\nReplicated yourself?:\nProblem address:\nDecription of the issue:\n\n\n\nTroubleshooting steps so far:\n\n\n");
			_this.getChatData();
	  		_this.displayChatType("passing");
	  	});
	 }	
	 
	 //Remove a chat from queue that you have passed. 
	this.cancelChat = function(chatID){
		var _this = this;
		this.doAjax("deletePassing", {id: chatID }, function( returnVal ) {
			_this.getChatData();
		});
	}	

	//Accept a chat, removing it from the queue, placing it passed chats, and sending a message to the passer. 
	this.takeChat = function(rowID, chatID){
		var _this = this;

		//A chat is being taken, so the receiver is this person, and the sender is the person passing the chat. 
		this.doAjax("takeChat", {id:rowID, receiver:_this.name, passer:_this.queued[chatID]["passer_name"], customer: _this.queued[chatID]["cust_name"] }, function( returnVal ) {
			_this.selectChat("queued",chatID);//Async getchatdata return causes the chat to still be queued when this fires
			_this.selectChat("received",chatID);//Only succeeds if getchatdata happens to run at the exact right time. Does nothing if non-existent. 
			_this.selectedType = "received";
			_this.getChatData();
		});
	}	 
	 
	 //c2p.php ajax handler. Throws error transmission/php errors and error responses from c2p.php, or calls callback with data component of return. 
	 this.doAjax = function(action, args, callback){
	 		 args.a = action; //The function takes args and actions separately for clarity, but must be a single arg list for Passing. 
			 var _this = this;
		 	$.getJSON( "php/c2p.php", args)
		 		.success( function( returnVal ) {
				if(returnVal["status"] == "success"){
					 callback(returnVal['data']);
				}else{
					 throw new Error(returnVal);
				 }
				})
				.error( function(returnVal){
					throw new Error(returnVal);
				});
	 }
	 
	 
	 
	 
	 //Validation of the submit chat form. Returns true or false after flashing any invalid fields. FIX THIS
	 this.validateSubmit = function(){
		 var returnValue = true;
		 var validObj = {};
		 returnValue &= this.validiateElement($("#passURL"),"[-a-zA-Z0-9@:%_\+.~#?&\/\/=]{2,256}\.[a-z]{2,4}\\b(\/[-a-zA-Z0-9@:%_\+.~#?&\/\/=]*)?");
		 returnValue &= this.validiateElement($("#passName"),".{1,32}");
		 returnValue &= this.validiateElement($("#passID"),"[0-9]{8,11}");
		 returnValue &= this.validiateElement($("#passDeets"),".{10,500}");
		 return returnValue;
	 }
	
	this.validiateElement = function(ele, pattern){
		if(!ele.val().match(new RegExp(pattern.replace("[\n\r]","<br>"), "ig"))){
			 this.__errorFlash(ele);
			 return false;
		}
		return true;
	}
	
	
	//displays the selected div in the right info pane. 
	this.actionSelect = function(container, selectedActionID){
		container.children().each(
				function(){
					if(this.id == selectedActionID){
						$(this).show();
					}else{
						$(this).hide();
					}
				});
	}
	
	//Initialization of the ajax  loop once login is completed
	this.__init = function(){
		this.name = this.__getCookie("name");
		this.getChatData();
		setInterval("document.sessionObj.getChatData()",3000);
		$("#nameInput").val(this.name);
		return;
	}
	
	//checks the cookie for the name key and sets its value as the object's name property
	this.__getCookie = function(cName){
		var name = cName + "=";
		var ca = document.cookie.split(';');
		for(var i=0; i<ca.length; i++)
		  {
		  var c = ca[i].trim();
		  if (c.indexOf(name)==0) return c.substring(name.length,c.length);
		  }
		return "";
	}
	
	//generates an html link from a URL. 
	this.urlToLink = function(url){
		 var newUrl = "";
		 var urlPattern = /^http/gi;		
		 var urlRegex = new RegExp(urlPattern);		 
		 if (!url.match(urlRegex) ){
			 newUrl = "http://"+url;
		 }
		 var linkUrl = "<a href='"+newUrl+"'>"+url+"</a>";
		 return linkUrl
	}
	
	//parses a timestamp from the DB into a string
	this.timestampToString = function(stamp){
		var ap = " AM";
		var sDate = new Date(stamp*1000);
		var h = sDate.getHours();		
		if(h > 12){
			h = h-12;
			ap = "PM";
		}
		var m = sDate.getMinutes().toString();
		if(m.length == 1){
			m += "0";
		}
		return h+":"+m+" "+ap;
	}
	
	//flashes the target element red (for form validation) 
	this.__errorFlash = function(ele){
		ele.css("background-color","red").fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100);
		ele.animate({backgroundColor:"white"}, 100)
	}
	
	//initializes everything. 
	this.__init();
}




