<br/>

<div id="pm">
	<form method="POST" action="{{ php_self }}?action=delete&pmid={{ pmid }}&location={{ location }}">

		<table class="content" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr align="center">
				<td width="50%" colspan="0" class="contentHead"><a href="/plugin/pm/">{{ lang['pm:inbox'] }}</a></td>
				<td width="50%" colspan="0" class="contentHead">
					<a href="/plugin/pm/?action=outbox">{{ lang['pm:outbox'] }}</a></td>
			</tr>
		</table>

		<br/>

		<table class="content" border="0" cellspacing="0" cellpadding="0" align="center">

			<tr>
				<td width="100%" class="contentHead">
					<img src="{admin_url}/plugins/pm/img/nav.gif" hspace="8">{{ subject }} {% if (ifinbox) %}от{% endif %} {% if not (ifinbox) %}для{% endif %} {{ author }} {{ pmdate|date('Y-m-d H:i') }}
				</td>
			</tr>

			<tr>
				<td width="100%">
					<blockquote>{{ content }}</blockquote>
				</td>
			</tr>

			<tr>
				<td>&nbsp;</td>
			</tr>

			<tr align="center">
				<td width="100%" class="contentEdit">
					<input class="button" type="submit" value="{{ lang['pm:delete_one'] }}">
	</form>
	{% if (ifinbox == 1) %}
	<form name="pm" method="POST" action="{{ php_self }}?action=reply&pmid={{ pmid }}">
		<input class="button" type="submit" value="{{ lang['pm:reply'] }}"></form>
	{% endif %}</td>
	</tr>
	</table>
</div>