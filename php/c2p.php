<?php session_start();
/**
Chat manipulation and retreival for use in Ajax requests.
**/
date_default_timezone_set("America/Chicago");
mysql_connect("localhost", "", "") or die(mysql_error());
mysql_select_db("pwns_c2p")or die("Error Connecting to DB");

if(isset($_GET["a"])){
	$action = $_GET["a"];
	$validArgs = array();
	switch($action){
		case("getMessages"):
			$validArgs = array("name"=>"[a-zA-Z ]{1,32}");
			break;
		case("submitChat"):
			$validArgs = array(
			"sender" => "[a-zA-Z ]{4,32}", 
			"passName" => ".{1,32}", 
			"passURL" => "[-a-zA-Z0-9@:%_\+.~#?&\/\/=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&\/\/=]*)?", 
			"passDeets" => ".{3,500}", 
			"passID" => "[0-9]{8,11}");
			break;
		case("takeChat"):
			$validArgs = array(
			"receiver"=>"[a-zA-Z ]{1,32}",
			"id"=>"[0-9]{1,16}",
			"passer"=>"[a-zA-Z ]{1,32}",
			"customer"=>".{4,32}");
			break;
		case("getChats"):
			$validArgs = array("name"=>"[a-zA-Z ]{4,32}");
			break;
		case("deleteMsg"):
			$validArgs = array("id"=>"[0-9]{1,10}");
			break;
		case("deletePassing"):
			$validArgs = array("id"=>"[0-9]{1,10}");
			break;
		default:
			return_error("action", "", 2);//Exits with error data if action isn't valid. 
	}
	
	$args = validateInput($_GET, $validArgs); //Exits with error data if args aren't valid. 
	$outputArray = array("action"=>$action, "status" => "success", "data" => $action($args));
	echo preg_replace("/\r\n|\r|\n/",'<br/>',json_encode($outputArray));
}


//Query to get the waiting messages for a user (just chat acceptance messages for now)
//Input vars: name
function getMessages($varArray){
	$out = array();
	$return = doQuery("select id, sender, receiver, msg from messages where receiver = '".$varArray["name"]."'", "getMessages.select");
	while($msg = mysql_fetch_assoc($return)){
		$out[$msg['id']] = $msg;
	}
	return $out;
}

//Insert a new c2p into the queue
//Input vars: sender, passName, passURL, passDeets, passTime, passID
function submitChat($varArray){
	$queryString = "insert into chats (passer_name,cust_name,billing_url,deets,chat_id) values('".$varArray["sender"]."','".$varArray["passName"]."','".$varArray["passURL"]."','".$varArray["passDeets"]."',".$varArray["passID"].");";
	doQuery($queryString, "submitChat.insert");
	return "";
}

//Accept a chat from the queue. Returns error if already taken. Receiver and sender roles are inverted in the msg because it's the chat being sent. 
//Input vars: id, passer, receiver, customer
function takeChat($varArray){
	$queryStr = "UPDATE chats SET receiver_name = '".$varArray["receiver"]."', time_accepted = current_timestamp() WHERE id = ".$varArray["id"]." AND receiver_name = ''";
	doQuery($queryStr,"takechat.update");
	if(mysql_affected_rows()<1){
		 return_error("TooSlow", array("msg"=>"That chat is already taken.\n"), 2);
	}

	$msg = $varArray["receiver"]." has accepted your chat with ".$varArray['customer'].". Pass it! ";
	$queryString = "INSERT INTO messages (sender, receiver, msg) VALUES('".$varArray['receiver']."','".$varArray['passer']."','".$msg."');";
	doQuery($queryString, "takeChat.msgInsert");

}


//Get a list of queued chats
//Input vars: name
function getChats($varArray){
	$types = array();
	$types["queued"] = array();
	$types["passing"] = array();
	$types["received"] = array();
	$result = doQuery("SELECT id, passer_name, cust_name, billing_url, deets, chat_id, receiver_name,  UNIX_TIMESTAMP(time_submitted) as time_submitted FROM `chats` ORDER BY time_submitted DESC limit 15;","getChats.queue");
	while($chat = mysql_fetch_assoc($result)){
		if($chat["receiver_name"]==$varArray['name']){
			$types["received"][$chat["chat_id"]] = $chat;
		}
		elseif(!$chat["receiver_name"]){
			if($chat["passer_name"]==$varArray['name']){
				$types["passing"][$chat["chat_id"]] = $chat;
			}else{
				$types["queued"][$chat["chat_id"]] = $chat;
			}
		}
	}
	return $types;
}

//Delete a message from the queue (provided by JS with previous getMessages call)
//Input vars: id
function deleteMsg($varArray){
	doQuery("delete from messages where id = ".$varArray["id"],"deleteMsg.delete");
	return "";
}

//Cancel a chat you had previously placed in the queue (provided by JS with previous getChats call)
//Input vars: id
function deletePassing($varArray){
	doQuery("delete from chats where id = ".$varArray["id"],"deletePassing.delete");
	return "";	
}

//Performs a query and either returns the result or prints/logs error and exits. Tag is a meta string to identify the problem in the logs. 
function doQuery($queryStr, $tag){
	$result =  mysql_query($queryStr);	
	if(!$result){
		return_error("MySql", $tag.": ".mysql_error()."\n".$queryStr, 1);
	}
	return $result;
}

/**
  Validate a set of args for required fields and regex match. If valid, return the escaped fields. 
  Format:
	$varArray = input array from GET or POST
	$requied = [required_key => regex_pattern, ...]
**/
function validateInput($varArray,$required){
	$errors = array();
	$escaped = array();
    foreach($required as $key => $pattern){
        if(!array_key_exists($key,$varArray) || !preg_match("/^".$pattern."$/is", $varArray[$key])){
			$errors[] = $key;
        }else{
			$escaped[$key] = mysql_escape_string($varArray[$key]);
		}
    }
	if(count($errors)>0){
		return_error("validation", array("status"=>"error","error_type"=>"validation","keys"=>$errors), 2);
	}
	return $escaped;
}

/**
  Primary Error Handler: logs error if the significance meets the current logging level, then exits, returning the error data.
  Note: Logs but does not echo mysql errors. 
**/
function return_error($type, $infoArray, $errorLevel){
	$log = fopen("./c2p_errors","a+");
	
	if($errorLevel <= $_SESSION['log_level']){
		$errorStr = date("m/d/y H:i:s",time())."	".$type." : ".json_encode($infoArray)."\n\n";
		fwrite($log,$errorStr);
	}
	fclose($log);
	exit(json_encode(array("status"=>"error","error_type"=>$type,"msgs"=>(($_SESSION['log_level'] == 3 || $errorLevel > 1 ) ? $infoArray : "Critical Error"))));
}


?>






