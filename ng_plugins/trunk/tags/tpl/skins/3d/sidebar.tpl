<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td>
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
	<tr>
	<td><img border="0" src="{tpl_url}/images/2z_35.gif" width="7" height="36" /></td>
	<td style="background-image:url('{tpl_url}/images/2z_36.gif');" width="100%"><b>
		<font color="#FFFFFF">Облако тегов</font></b></td>
	<td><img border="0" src="{tpl_url}/images/2z_38.gif" width="7" height="36" /></td>
	</tr>
	</table>
</td></tr>
<tr><td>
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
	<tr>
	<td style="background-image:url('{tpl_url}/images/2z_56.gif');" width="7">&nbsp;</td>
	<td bgcolor="#FFFFFF" id="insertTagCloud"><ul>{entries}</ul></td>
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
<script type="text/javascript" src="/engine/plugins/tags/tpl/skins/3d/swfobject.js"></script>
<script language="javascript">
var insertCloudElementID = 'insertTagCloud';
var insertCloudClientWidth = document.getElementById(insertCloudElementID).clientWidth;
var insertCloudClientHeight = 140;
var tagLine = '{cloud3d}';
var rnumber = Math.floor(Math.random()*9999999);
var widget_so = new SWFObject("/engine/plugins/tags/tpl/skins/3d/tagcloud.swf?r="+rnumber, "tagcloudflash", insertCloudClientWidth, insertCloudClientHeight, "9", "#ffffff");
widget_so.addParam("allowScriptAccess", "always");
widget_so.addVariable("tcolor", "0x333333");
widget_so.addVariable("tspeed", "115");
widget_so.addVariable("distr", "true");
widget_so.addVariable("mode", "tags");
widget_so.addVariable("tagcloud", tagLine);
widget_so.write(insertCloudElementID);
</script>