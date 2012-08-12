<script type="text/javascript" src="{admin_url}/includes/js/libsuggest.js"></script>
<style>
.suggestWindow {
 background:#f6f8fb;
 border: 1px solid #aaaaaa;
 color: #232323;
 width: 274px;
 position: absolute;
 display: block;
 visibility: hidden;
 padding: 0px;
 font: normal 12px  tahoma, sans-serif;
  top: 0px; margin: 0;
 left: 80px; position: absolute;
}

#suggestBlock {
 padding-top: 2px;
 padding-bottom: 2px;  width: 100%;
 border: 0px;
}

#suggestBlock td {
 padding-left: 2px;
}

#suggestBlock tr {
 padding: 3px;
 padding-left: 8px;
 background: white;
}

/* #suggestBlock tr:hover, */
#suggestBlock .suggestRowHighlight {
 background: #59a6ec url(images/1px.png) repeat-x;
 color: white;
 cursor: default;
}

#suggestBlock .cleft {
 padding-left: 5px;
}

#suggestBlock .cright {
 text-align: right;
 padding-right: 5px;
}

.suggestClose {
 display: block;
 text-align: right;
 font: normal 10px verdana, tahoma, sans-serif;
 background:#3c9c08;
 color: white;
 padding:3px; cursor: pointer;
}
</style>
<br />

<div id="pm">
<form method=post name=form action="{php_self}?action=send">

<table border="0" width="100%" cellspacing="0" cellpadding="0">

	<tr align="center">
		<td width="50%" colspan="1" class="contentHead"><a href="/plugin/pm/">{l_pm:inbox}</a></td>
		<td width="50%" colspan="1" class="contentHead"><a href="/plugin/pm/?action=outbox">{l_pm:outbox}</a></td>
	</tr>
	
	<tr>
		<td width=100% colspan="2">&nbsp;</td>
	</tr>
	
	<tr>
		<td width=100% colspan="2" class="contentHead"><img src="{admin_url}/plugins/pm/img/nav.gif" hspace="8" align="absmiddle">{l_pm:new}</td>
	</tr>
	
	<tr>
		<td width=50% class="contentEntry1">{l_pm:subject}</td>
		<td width=50% class="contentEntry2"><input type="text" class="pm" size="40" name="title" tabindex="2" maxlength="50" /></td>
	</tr>
	
	<tr>
		<td width="50%" class="contentEntry1">{l_pm:too}<br /><small>{l_pm:to}</small></td>
		<td width="50%" class="contentEntry2" align="left"><input type="text" class="pm" name="sendto" id="sendto" size="20" tabindex="3" autocomplete="off" maxlength="70" value="{username}" /><span id="suggestLoader" style="width: 20px; visibility: hidden;"><img src="{skins_url}/images/loading.gif"/></span></td>
	</tr>
</table>

<br />

<table border="0" cellspacing="0" cellpadding="0" class="content" align="center">
	<tr>
		<td width="100%" class="contentHead"><img src="{admin_url}/plugins/pm/img/nav.gif" hspace="8" alt="" />{l_pm:textmessage}</td>
	</tr>
	
	<tr>
	<td width="100%"><br />{quicktags}<br /><br />{smilies}<br /><textarea name="content" id="content" rows="10" cols="60" tabindex="1" maxlength="3000" /></textarea>
	<br /><br /><input name="saveoutbox" class="check" type="checkbox"/> {l_pm:saveoutbox}<br /><br /></td>
	</tr>

	<tr align="center">
		<td width="100%" colspan="2" class="contentEdit" valign="top">
		<input class="button" type="submit" value="{l_pm:send}" accesskey="s" />
		</td>
	</tr>
</table>

</form>
</div>

<script language="javascript" type="text/javascript">

function systemInit() {
	new ngSuggest('sendto', 
								{ 
									'iMinLen'	: 1,
									'stCols'	: 1,
									'stColsClass': ['cleft'],
									'lId'		: 'suggestLoader',
									'hlr'		: 'true',
									'stColsHLR'	: [ true ],
									'reqMethodName' : 'pm_get_username',
								}
							);
}

if (document.body.attachEvent) {
	document.body.onload = systemInit;
} else {
	systemInit();
}
</script>