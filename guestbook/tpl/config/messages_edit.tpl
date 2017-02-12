<style>
	.btn-link {
		margin-left: 3px;
		vertical-align: middle;
		text-decoration: none;
	}
</style>
<form method="post" action="" name="form">
	<fieldset class="admGroup">
		<legend class="title">{{ lang['gbconfig']['message_edit_title'] }} {{ field.name }}</legend>
		<table class="content" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr class="contRow1">
				<td><label>{{ lang['gbconfig']['message_date'] }}</label></td>
				<td><input type="text" id="cdate" name="cdate" value="{{ postdate|date('j.m.Y H:i') }}"/></td>
			</tr>
			<tr class="contRow1">
				<td><label>{{ lang['gbconfig']['message_ip'] }}</label></td>
				<td>{{ ip }}</td>
			</tr>
			<tr class="contRow1">
				<td><label>{{ lang['gbconfig']['message_author'] }}
						<b style="color:red">{{ lang['gbconfig']['message_required'] }}</b></label></td>
				<td><input type="text" name="author" value="{{ author }}"/></td>
			</tr>
			{% for field in fields %}
				<tr class="contRow1">
					<td><label>{{ field.name }} {% if field.required %}
								<b style="color:red">{{ lang['gbconfig']['message_required'] }}</b>{% endif %}</label>
					</td>
					<td>
						<input type="text" name="{{ field.id }}" value="{{ field.value }}" {% if field.required %}required{% endif %} />
					</td>
				</tr>
			{% endfor %}
			<tr class="contRow1">
				<td><label>{{ lang['gbconfig']['message_content'] }}
						<b style="color:red">{{ lang['gbconfig']['message_required'] }}</b></label></td>
				<td><textarea type="text" name="message" rows="8" cols="100">{{ message }}</textarea></td>
			</tr>
			<tr class="contRow1">
				<td><label>{{ lang['gbconfig']['message_answer'] }}</label></td>
				<td><textarea type="text" name="answer" rows="8" cols="100">{{ answer }}</textarea></td>
			</tr>
			<tr class="contRow1">
				<td><label>{{ lang['gbconfig']['message_status'] }}</label></td>
				<td>
					<select name="status" class="bfstatus">
						<option value="1" {% if status == '1' %}selected{% endif %}>{{ lang['gbconfig']['message_active'] }}</option>
						<option value="0" {% if status == '0' %}selected{% endif %}>{{ lang['gbconfig']['message_inactive'] }}</option>
					</select>
				</td>
			</tr>
			<tr class="contRow1">
				<td colspan="2">
          <span class="right_s">
            <input type="reset" class="button" value="{{ lang['gbconfig']['message_reset'] }}"/>&nbsp;
            <input name="submit" type="submit" class="button" value="{{ lang['gbconfig']['message_submit'] }}"/>
            <a onclick="return confirm('{{ lang['gbconfig']['message_confirm'] }}');" class="button btn-link" href="?mod=extra-config&plugin=guestbook&action=delete_message&id={{ id }}">
              <span>{{ lang['gbconfig']['message_delete'] }}</span>
            </a>
          </span>
				</td>
			</tr>
		</table>
	</fieldset>
	{% if social %}
	<fieldset class="admGroup">
		<legend class="title">{{ lang['gbconfig']['message_social_title'] }}</legend>
		<table class="content" border="0" cellspacing="0" cellpadding="0" align="center">
			{% if social.Vkontakte %}
				<tr>
					<td class="contentEntry1"><label>{{ lang['gbconfig']['message_vkontakte'] }}</label></td>
					<td class="contentEntry2">
						<a href="{{ social.Vkontakte.link }}">{{ lang['gbconfig']['message_social_profile'] }}</a>
						<a href="{{ social.Vkontakte.photo }}">{{ lang['gbconfig']['message_social_avatar'] }}</a>
						<a onclick="return confirm('{{ lang['gbconfig']['message_social_confirm'] }}');" href="{{ php_self }}?mod=extra-config&plugin=guestbook&action=delete_social&id={{ id }}&soc=Vkontakte">
							{{ lang['gbconfig']['message_social_delete'] }}
						</a>
					</td>
				</tr>
			{% endif %}
			{% if social.Facebook %}
				<tr>
					<td class="contentEntry1"><label>{{ lang['gbconfig']['message_facebook'] }}</label></td>
					<td class="contentEntry2">
						<a href="{{ social.Facebook.link }}">{{ lang['gbconfig']['message_social_profile'] }}</a>
						<a href="{{ social.Facebook.photo }}">{{ lang['gbconfig']['message_social_avatar'] }}</a>
						<a onclick="return confirm('{{ lang['gbconfig']['message_social_confirm'] }}');" href="{{ php_self }}?mod=extra-config&plugin=guestbook&action=delete_social&id={{ id }}&soc=Facebook">
							{{ lang['gbconfig']['message_social_delete'] }}
						</a>
					</td>
				</tr>
			{% endif %}
			{% if social.Google %}
				<tr>
					<td class="contentEntry1"><label>{{ lang['gbconfig']['message_google'] }}</label></td>
					<td class="contentEntry2">
						<a href="{{ social.Google.link }}">{{ lang['gbconfig']['message_social_profile'] }}</a>
						<a href="{{ social.Google.photo }}">{{ lang['gbconfig']['message_social_avatar'] }}</a>
						<a onclick="return confirm('{{ lang['gbconfig']['message_social_confirm'] }}');" href="{{ php_self }}?mod=extra-config&plugin=guestbook&action=delete_social&id={{ id }}&soc=Google">
							{{ lang['gbconfig']['message_social_delete'] }}
						</a>
					</td>
				</tr>
			{% endif %}
			{% if social.Instagram %}
				<tr>
					<td class="contentEntry1"><label>{{ lang['gbconfig']['message_instagram'] }}</label></td>
					<td class="contentEntry2">
						<a href="{{ social.Instagram.link }}">{{ lang['gbconfig']['message_social_profile'] }}</a>
						<a href="{{ social.Instagram.photo }}">{{ lang['gbconfig']['message_social_avatar'] }}</a>
						<a onclick="return confirm('{{ lang['gbconfig']['message_social_confirm'] }}');" href="{{ php_self }}?mod=extra-config&plugin=guestbook&action=delete_social&id={{ id }}&soc=Instagram">
							{{ lang['gbconfig']['message_social_delete'] }}
						</a>
					</td>
				</tr>
			{% endif %}
		</table>
		{% endif %}
</form>
<script type="text/javascript">
	$("#cdate").datetimepicker({currentText: "DD.MM.YYYY HH:MM"});
</script>
