{{ error.print }}
<div class="blockform">
	<h2><span>��������� ���������</span></h2>
	<div class="box">
	<form method="post" id="post" action="">
		<div class="inform">
			<fieldset>
				<legend>�������� ���� ��������� � ������� ���������</legend>
				<div class="infldset txtarea">
					<label class="conl"><strong>����������</strong><br /><input type="text" name="sendto" size="25" maxlength="25" value="{{ sendto }}" tabindex="1" /><br /></label>
					<div class="clearer"></div>
					<label><strong>���������</strong><br /><input class="longinput" type='text' name='title' value='{{ title }}' size="80" maxlength="70" tabindex='2' /><br /></label>

					<label><strong>���������</strong><br />
					<textarea name="message" rows="20" cols="95" tabindex="3">{{ message }}</textarea><br /></label>
				</div>
			</fieldset>
		</div>
		<div class="inform">
				<fieldset>
					<legend>��������</legend>
					<div class="infldset">
						<div class="rbox">
							<label><input type="checkbox" name="savemessage" value="1" checked="checked" tabindex="5" />��������� ���������<br /></label>
						</div>
					</div>
				</fieldset>
			</div>
		<p><input type="submit" name="submit" value="���������" tabindex="6" accesskey="s" /><a href="javascript:history.go(-1)">��������� �����</a></p>
	</form>
	</div>
</div>