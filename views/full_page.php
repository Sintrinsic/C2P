<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>C2P Demo</title>
<link rel="stylesheet" type="text/css" href="css/layout.css">
<link href="../css/layout.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.js"></script>
<script src="js/jquery-ui.js"></script>
<script src="js/c2p.js"></script>
</head>
<body>
<div id="contentFrame" class="centered">
    <div id="header">
        C2P - Demo
    </div>
	<div id="contentBody" class="content-body">
		<div id="chatFrame" class="primary-element left">
        	<ul id="chatTypes" class="horizontal-menu div-title">
			<li id="viewQueueButton">Queue</li>
            <li id="viewPassedButton">Passing</li>
            <li id="viewReceivedButton">Received</li>
			</ul>
			<div id="chatTypeContainer">
				<ul id="chatList" class="chat-list">
                </ul>
			</div>
			<div id="actionPassButton" class="bottom div-title clickable">Pass A Chat</div>
		</div>
        <div id="actionContainer" class="primary-element">
            <div id="actionPass">
                <div id="passTitle" class="div-title">
                    Pass chat form
                </div>
                <div id="passForm" class="action-content">
                    <table id="passFormTable">
                    <tr>
                        <td>Cust Name:</td>
                        <td><input id="passName" size=32></td>
                    </tr>
                    <tr>
                        <td>Chat ID: </td>
                        <td><input id="passID" size=32></td>
                    </tr>
                    <tr>
                        <td>Billing URL: </td>
                        <td><input id="passURL" size=32></td>
                    </tr>
                    <tr>
                        <td >Deets:</td>
                        <td><textarea id="passDeets" class="big-text"></textarea></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><button id="passButton">Pass it!</button></td>
                    </tr>
                    </table>
                </div>
            </div>
            <div id="actionDetails" class="hidden">
                <div id="detailsTitle" class="div-title">
                    Details
                </div>
                <div id="chatDetails" class="action-content">
                    <table id="viewChatTable">
                    <tr>
                        <td width="100px">Passer Name: </td>
                        <td id="viewChatPasser"></td>
                    </tr>
                    <tr>
                        <td>Receiver Name: </td>
                        <td id="viewChatReceiver"></td>
                    </tr>
                    <tr>
                        <td>Customer Name: </td>
                        <td id="viewChatCustomer"></td>
                    </tr>
                    
                    <tr>
                    <td>Chat ID: </td>
                        <td id="viewChatID"></td>
                    </tr>
                    <tr>
                        <td>Billing URL: </td>
                        <td id="viewChatURL"></td>
                    </tr>			
                    <tr>
                        <td>Deets: </td>
                        <td id="viewChatDeets"></td>
                    </tr>
                    </table>
                </div>
            </div>
        </div>
	</div>
</div>


<div id="messageBox" class="popup hidden">
    <div id="messageTitle" class="div-title">
        You have Messages!
    </div>
    <ul id="messages" class="div-list">	
    
    </ul>
</div>
<body>
</body>
</html>