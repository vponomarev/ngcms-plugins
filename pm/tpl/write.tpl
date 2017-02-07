<script type="text/javascript" src="{{ scriptLibrary }}/libsuggest.js"></script>
<br/>

<div id="pm">
	<form method=post name=form action="{{ php_self }}?action=send">

		<table border="0" width="553px" cellspacing="0" cellpadding="0">

			<tr align="center">
				<td width="50%" colspan="1" class="contentHead"><a href="/plugin/pm/">{{ lang['pm:inbox'] }}</a></td>
				<td width="50%" colspan="1" class="contentHead">
					<a href="/plugin/pm/?action=outbox">{{ lang['pm:outbox'] }}</a></td>
			</tr>

			<tr>
				<td width=100% colspan="2">&nbsp;</td>
			</tr>

			<tr>
				<td width=100% colspan="2" class="contentHead">
					<img src="{{ admin_url }}/plugins/pm/img/nav.gif" hspace="8" align="absmiddle">{{ lang['pm:new'] }}
				</td>
			</tr>

			<tr>
				<td width=50% class="contentEntry1">{{ lang['pm:subject'] }}</td>
				<td width=50% class="contentEntry2">
					<input type="text" class="pm" size="40" name="title" tabindex="2" maxlength="50"/></td>
			</tr>

			<tr>
				<td width="50%" class="contentEntry1">{{ lang['pm:too'] }}<br/>
					<small>{{ lang['pm:to'] }}</small>
				</td>
				<td width="50%" class="contentEntry2">
					<input type="text" class="pm" name="to_username" id="to_username" size="40" tabindex="3" autocomplete="off" maxlength="70" value="{{ username }}"/><span id="suggestLoader" style="width: 20px; visibility: hidden;"><img src="{{ skins_url }}/images/loading.gif"/></span>
				</td>
			</tr>
		</table>

		<br/>

		<table border="0" cellspacing="0" cellpadding="0" class="content" align="center">
			<tr>
				<td width="100%" class="contentHead">
					<img src="{{ admin_url }}/plugins/pm/img/nav.gif" hspace="8" alt=""/>{{ lang['pm:textmessage'] }}
				</td>
			</tr>

			<tr>
				<td width="100%"><br/>{{ quicktags }}<br/><br/>{{ smilies }}
					<br/><textarea name="content" id="pm_content" rows="10" cols="60" tabindex="1" maxlength="3000"/></textarea>
					<br/><br/><input name="saveoutbox" class="check" type="checkbox"/> {{ lang['pm:saveoutbox'] }}
					<br/><br/></td>
			</tr>

			<tr align="center">
				<td width="100%" colspan="2" class="contentEdit" valign="top">
					<input class="button" type="submit" value="{{ lang['pm:send'] }}" accesskey="s"/>
				</td>
			</tr>
		</table>

	</form>
</div>

<script language="javascript" type="text/javascript">

	function systemInit() {
		new ngSuggest('to_username',
			{
				'iMinLen': 1,
				'stCols': 1,
				'stColsClass': ['cleft'],
				'lId': 'suggestLoader',
				'hlr': 'true',
				'stColsHLR': [true],
				'reqMethodName': 'pm_get_username',
			}
		);
	}

	if (document.body.attachEvent) {
		document.body.onload = systemInit;
	} else {
		systemInit();
	}
</script>