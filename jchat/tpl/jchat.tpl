<!-- STYLE DEFINITION ((( YOU CAN CHANGE IT ))) -->
<style>
.jchat_ODD  TD { background-color: #FFFFFF; width: 100%; text-align: left; font-size: 12px;  border-bottom: 1px solid #DDDDDD; }
.jchat_EVEN TD { background-color: #FBFBFB; width: 100%; text-align: left; font-size: 12px;  border-bottom: 1px solid #DDDDDD; }
.jchat_INFO TD { background-color: #FFFFFF; width: 100%; text-align: left; font: 10px arial; border-bottom: 1px solid #DDDDDD; }
</style>

<!-- SCRIPTS INTERNALS BEGIN ((( DO NOT CHANGE ))) -->
<script language="javascript">
function chatSubmitForm() {
	var formID = document.getElementById('jChatForm');
	CHATTER.postMessage(formID.name.value, formID.text.value);
}

function jChat(maxRows, refresh, tableID) {
	var thisObject = this;

	this.init = function(maxRows, refresh, tableID) {
		this.timerInterval = ((refresh < 5)?5:refresh) * 1000;
		this.timerActive   = false;
		this.scanActive    = false;
		this.timerID       = 0;
		this.tickCount     = 0;
		this.maxLoadedID   = 0;
		this.idleStart     = 0;

		this.maxRows       = maxRows?maxRows:40;
		this.tableRef      = document.getElementById(tableID);
		this.fatalError    = (this.tableRef == null)?true:false;
		this.linkTX	   = new sack();
		this.linkRX	   = new sack();
		this.linkRX.onComplete = function() {
			if (this.responseStatus[0] != "200")
				return;

			var data = eval(this.response);
			
			if (typeof(data) == 'object')
				thisObject.loadData(data);
		}	
		if (!this.fatalError) {
		        while(this.tableRef.rows.length) this.tableRef.deleteRow(-1);
		} else {
			alert('fatal error:' + tableID);
		}	
		return this.fatalError;
	}

	//
	this.timerStart = function() {
		this.timerActive = true;
		this.scanActive = true;
		dateTime = new Date();
		thisObject.idleStart     = Math.round(dateTime.getTime() / 1000);
		thisObject.timerID = setInterval(
			function() {
				thisObject.tickCount++;
				//document.getElementById('timerDebug').innerHTML = thisObject.tickCount;
				if (thisObject.scanActive) {
					dateTime = new Date();
					thisObject.linkRX.requestFile = '/plugin/jchat/';
					thisObject.linkRX.setVar('plugin_cmd', 'show');
					thisObject.linkRX.setVar('start', thisObject.maxLoadedID);
					thisObject.linkRX.setVar('timer', thisObject.timerInterval /1000);
					thisObject.linkRX.setVar('idle', Math.round((dateTime.getTime()/1000) - thisObject.idleStart));
					thisObject.linkRX.method='GET';
					thisObject.linkRX.runAJAX();
				}

			}, this.timerInterval);
	}

	//
	this.timerStop = function() {
		this.timerActive = false;
		clearInterval(this.timerID);
	}

	//
	this.timerRestart = function() {
		this.timerStop();
		this.timerStart();
	}
		
	//
	this.loadData = function(bundle) {
		if (this.fatalError)
			return false;
		
		// Extract passed commands
		var cmdList = bundle[0];
		var cmdLen = cmdList.length;
		for (var i=0; i<cmdLen; i++) {
			var cmd = cmdList[i];
			if (cmd[0] == 'settimer') {
				this.timerInterval = cmd[1] * 1000;
				alert('new timer interval: '+this.timerInterval);
				this.timerRestart();
			}
			if (cmd[0] == 'reload') {
				document.location = document.location;
				return;
			}
			if (cmd[0] == 'stop') {
				this.timerStop();
			}
		}

		// Extract passed data
		var data = bundle[1];

		// Add rows
		var len = data.length;
		var lastRow = this.tableRef.rows.length;
		for (var i=0; i<len; i++) {
			var rec  = data[i];

			// Skip already loaded data
			if (thisObject.maxLoadedID >= rec['id']) {
				//alert('DUP: '+thisObject.maxLoadedID+' >= '+rec['id']);
				continue;
			}	

			var row  = this.tableRef.insertRow(lastRow++);
			row.className = ((rec['id'] % 2) == 0)?'jchat_ODD':'jchat_EVEN';

			var cell = row.insertCell(0);
        		cell.innerHTML = ((rec['author_id']>0)?('<b>'+rec['author']+'</b>'):('<i>'+rec['author']+'</i>'))+': '+rec['text'];
        		thisObject.maxLoadedID = rec['id'];
		}
		if (len>0) {
			// Clear old rows from chat [ if needed ]
			while (thisObject.tableRef.rows.length > thisObject.maxRows)
				thisObject.tableRef.deleteRow(0);

			thisObject.tableRef.parentNode.scrollTop = thisObject.tableRef.parentNode.scrollHeight;
		}	
	}

	//
	this.addMessage = function(msg, className) {
		if (this.fatalError)
			return false;

		var lastRow = this.tableRef.rows.length;
		var row  = this.tableRef.insertRow(lastRow);
		row.className = className;

		var cell = row.insertCell(0);
       		cell.innerHTML = msg;
		this.tableRef.parentNode.scrollTop = this.tableRef.parentNode.scrollHeight;

	}

	//
	this.postMessage = function(name, text) {
		var TX = this.linkTX;

		TX.requestFile = '/plugin/jchat/';
		TX.setVar('plugin_cmd', 'add');[not-logged]
		TX.setVar('name', name);[/not-logged]
		TX.setVar('start', this.maxLoadedID);
		TX.setVar('text', text);
		TX.method='POST';
		TX.onComplete = function() { 
			var data = eval('('+this.response+')');
			
			if (typeof(data) == 'object') {
				if (data['status']) {
					thisObject.addMessage('<i>message posted</i>', 'jchat_INFO'); 
				} else {
					thisObject.addMessage('<i>ERROR: <b>'+data['error']+'</b></i>', 'jchat_INFO'); 
				}
				if (typeof(data['bundle']) == 'object')	{
					thisObject.loadData(data['bundle']);
				}	
			} else {
				thisObject.addMessage('<i><b>Bad reply from server</b></i>', 'jchat_INFO'); 
			}
		}	
		TX.runAJAX();

		// Restart idle timer
		dateTime = new Date();
		thisObject.idleStart     = Math.round(dateTime.getTime() / 1000);

		// Restart scanner if it's turned off
		if (!this.timerActive)
			this.timerStart();
	}

	this.init(maxRows, refresh, tableID);
}
</script>
<!-- SCRIPTS INTERNALS END -->

<!-- Display data definition (( YOU CAN CHANGE IT )) -->
<table border="0" width="230" cellspacing="0" cellpadding="0">
<tr><td>
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
	<tr>
	<td><img border="0" src="{tpl_url}/images/2z_35.gif" width="7" height="36" /></td>
	<td style="background-image:url('{tpl_url}/images/2z_36.gif');" width="100%">&nbsp;<b><font color="#FFFFFF">„ат-бокс</font></b></td>
	<td><img border="0" src="{tpl_url}/images/2z_38.gif" width="7" height="36" /></td>
	</tr>
	</table>
</td></tr>
<tr><td>
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
	<tr>
	<td style="background-image:url('{tpl_url}/images/2z_56.gif');" width="7">&nbsp;</td>
	<td bgcolor="#FFFFFF">
	<div class="block_cal" align="left">

<!-- THIS IS REQUIRED BLOCK. PLEASE SAVE IT -->
<div style="overflow: auto; height: 300px;">
<table id="jChatTable" cellspacing="0" cellpadding="0"><tr><td>Loading chat...</td></tr></table>
</div>
[post-enabled]
<form method="post" name="jChatForm" id="jChatForm" onsubmit="chatSubmitForm(); return false;">
<table align="left">[not-logged]
<tr><td>Name:</td><td><input type="text" name="name" /></td></tr>[/not-logged]
<tr><td>Text:</td><td><input type="text" name="text" maxlength="{maxlen}"/></td></tr>
<tr><td colspan="2"><input type="submit" value="Post"/></td></tr>
</table>
</form>
[/post-enabled]
<!-- END OF REQUIRED BLOCK -->

</div></td>
	<td style="background-image:url('{tpl_url}/images/2z_58.gif');" width="7">&nbsp;</td>
	</tr>
	</table>
</td></tr>
<tr><td>
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
	<tr>
	<td><img border="0" src="{tpl_url}/images/2z_60.gif" width="7" height="11" /></td>
	<td style="background-image:url('{tpl_url}/images/2z_61.gif');" width="100%"></td>
	<td><img border="0" src="{tpl_url}/images/2z_62.gif" width="7" height="11" /></td>
	</tr>
	</table>
</td></tr>
</table>

<!-- SCRIPTS INTERNALS BEGIN ((( DO NOT CHANGE ))) -->
<script language="javascript">
var CHATTER = new jChat({history}, {refresh}, 'jChatTable');
CHATTER.loadData({data});
CHATTER.timerStart();
</script>
<!-- SCRIPTS INTERNALS END -->
