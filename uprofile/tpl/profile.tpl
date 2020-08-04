<script type="text/javascript">
	function ChangeOption(selectedOption) {
		document.getElementById('maincontent').style.display = "none";
		document.getElementById('additional').style.display = "none";

		if (selectedOption == 'maincontent') {
			document.getElementById('maincontent').style.display = "";
		}

		if (selectedOption == 'additional') {
			document.getElementById('additional').style.display = "";
		}
	}

	function validate_form() {
		var f = document.getElementById('profileForm');

		// ICQ
		var icq = f.editicq.value;
		if ((icq.length > 0) && (!icq.match(/^\d{4,10}$/))) {
			alert("{{ lang.uprofile.wrong_icq }}");
			return false;
		}

		// Email
		var email = f.editmail.value;
		if ((email.length > 0) && (!emailCheck(email))) {
			alert("{{ lang.uprofile.wrong_email }}");
			return false;
		}

		// About
		var about = f.editabout.value;
		if (({{ info_sizelimit }} > 0) && (about.length > {{ info_sizelimit }})) {
			alert("{{ info_sizelimit_text }}");
			return false;
		}
		return true;
	}
</script>
<form id="profileForm" method="post" action="{{ form_action }}" enctype="multipart/form-data">
	<input type="hidden" name="token" value="{{ token }}"/>
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td>
				<table border="0" width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td>
						<td width="100%" align="center">{{ lang.uprofile.profile_of }} {{ user.name }}</td>
						<td>
					</tr>
				</table>
				<br/>
			</td>
		</tr>
		<tr>
			<td>
				<table border="0" width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td width="7">&nbsp;</td>
						<td bgcolor="#FFFFFF">
							<table border="0" cellspacing="0" cellpadding="0" width="100%">
								<tr align="center">
									<td width="100%" class="contentEdit" align="center" valign="top">
										<input type="button" onmousedown="javascript:ChangeOption('maincontent')" value="{{ lang.uprofile.maincontent }}" class="button"/>
										<input type="button" onmousedown="javascript:ChangeOption('additional')" value="{{ lang.uprofile.additional }}" class="button"/>
									</td>
								</tr>
							</table>
							<br/>
							<table id="maincontent" class="content" width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
								<tr>
									<td style="padding: 5px; background-color: #f9fafb;" class="entry">
										{{ lang.uprofile.status }}
									</td>
									<td style="padding: 5px; background-color: #f9fafb;" class="entry">{{ user.status }}</td>
								</tr>
								<tr>
									<td style="padding: 5px; background-color: #f9fafb;" class="entry">
										{{ lang.uprofile.regdate }}
									</td>
									<td style="padding: 5px; background-color: #f9fafb;" class="entry">{{ user.reg }}</td>
								</tr>
								<tr>
									<td style="padding: 5px;" class="entry">{{ lang.uprofile.last }}</td>
									<td style="padding: 5px;" class="entry">{{ user.last }}</td>
								</tr>
								<tr>
									<td style="padding: 5px; background-color: #f9fafb;" class="entry">
										{{ lang.uprofile.all_news }}
									</td>
									<td style="padding: 5px; background-color: #f9fafb;" class="entry">{{ user.news }}</td>
								</tr>
								<tr>
									<td style="padding: 5px;" class="entry">{{ lang.uprofile.all_comments }}</td>
									<td style="padding: 5px;" class="entry">{{ user.com }}</td>
								</tr>
								<tr>
									<td style="padding: 5px;" class="entry">{{ lang.uprofile.email }}</td>
									<td style="padding: 5px;" class="entry">
										<input type="text" class="email" name="editmail" value="{{ user.email }}" size="40"/>
									</td>
								</tr>
								<tr>
									<td style="padding: 5px; background-color: #f9fafb;" class="entry">
										{{ lang.uprofile.site }}
									</td>
									<td style="padding: 5px; background-color: #f9fafb;" class="entry">
										<input type="text" name="editsite" value="{{ user.site }}" size="40"/></td>
								</tr>
								<tr>
									<td style="padding: 5px;" class="entry">{{ lang.uprofile.icq }}</td>
									<td style="padding: 5px;" class="entry">
										<input type="text" name="editicq" value="{{ user.icq }}" size="40" maxlength="10"/></td>
								</tr>
								<tr>
									<td style="padding: 5px; background-color: #f9fafb;" class="entry">
										{{ lang.uprofile.from }}
									</td>
									<td style="padding: 5px; background-color: #f9fafb;" class="entry">
										<input type="text" name="editfrom" value="{{ user.from }}" size="40" maxlength="60"/>
									</td>
								</tr>
								<tr>
									<td style="padding: 5px;" class="entry">{{ lang.uprofile.about }} {{ about_sizelimit_text }}
									</td>
									<td style="padding: 5px;" class="entry">
										<textarea name="editabout" rows="7" cols="55">{{ user.info }}</textarea></td>
								</tr>
								<tr>
									<td style="padding: 5px; background-color: #f9fafb;" class="entry">
										{{ lang.uprofile.new_pass }}
									</td>
									<td style="padding: 5px; background-color: #f9fafb;" class="entry">
										<input class="password" name="editpassword" size="40" maxlength="16" autocomplete="off"/><br/>
										<small>{{ lang.uprofile.pass_left }}</small>
									</td>
								</tr>
								<tr>
									<td style="padding: 5px;" class="entry">{{ lang.uprofile.oldpass }}<br/>
										<small>{{ lang.uprofile['oldpass#desc'] }}</small>
									</td>
									<td style="padding: 5px;" class="entry">
										<input type="password" name="oldpass" value="" size="40" maxlength="10" autocomplete="off"/>
									</td>
								</tr>
								{plugin_xfields_1}
							</table>

							<table id="additional" style="display: none;" class="content" border="0" width="100%" cellspacing="0" cellpadding="0">
								<tr>
									<td style="padding: 5px; background-color: #f9fafb;" class="entry">
										{{ lang.uprofile.avatar }}
									</td>
									<td style="padding: 5px; background-color: #f9fafb;" class="entry">
										{% if (flags.avatarAllowed) %}
										<input type="file" name="newavatar" size="40" /><br />
										{% if (user.flags.hasAvatar) %}
											<img src="{{ user.avatar }}" style="margin: 5px; border: 0px; alt=""/><br/>
											<input type="checkbox" name="delavatar" id="delavatar" class="check" />&nbsp;
											<label for="delavatar">{{ lang.uprofile['delete'] }}</label>
										{% endif %}
										{% else %}{{ lang.uprofile['avatars_denied'] }}
										{% endif %}
									</td>
								</tr>
								<tr>
									<td style="padding: 5px;" class="entry">{{ lang.uprofile.photo }}</td>
									<td style="padding: 5px;" class="entry">
										{% if (flags.photoAllowed) %}
										<input type="file" name="newphoto" size="40" /><br />
										{% if (user.flags.hasPhoto) %}
											<a href="{{ user.photo }}" target="_blank"><img src="{{ user.photo_thumb }}" style="margin: 5px; border: 0px; alt=""/></a><br/>
											<input type="checkbox" name="delphoto" id="delphoto" class="check" />&nbsp;
											<label for="delphoto">{{ lang.uprofile['delete'] }}</label>
										{% endif %}
										{% else %}{{ lang.uprofile['photos_denied'] }}
										{% endif %}
									</td>
								</tr>
								{plugin_xfields_0}
							</table>
							<br/>
							<table border="0" width="100%" cellspacing="0" cellpadding="0">
								<tr align="center">
									<td width="100%" class="contentEdit" align="center" valign="top">
										<input type="submit" value="{{ lang.uprofile.save }}" class="button" onclick="return validate_form();"/>
										<input type="hidden" name="plugin_cmd" value="apply"/>
									</td>
								</tr>
							</table>
						</td>
						<td width="7">&nbsp;</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<table border="0" width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td>
							</td>
						<td width="100%"></td>
						<td>
							</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</form>