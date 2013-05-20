<div class="blockform"> 
	<h2><span>�����������</span></h2> 
	<div class="box"> 
		<form method="post" action="" enctype="multipart/form-data"> 
			<div class="inform"> 
				<div class="forminfo"> 
					<h3>������ ����������</h3> 
					<p>����������� �� �����������, �� ��� ����������� ��� ������ � �������������� ������������. ��������, �������� ����������� ������������� � ������� ���� ���������, ��������� ����������� ������� �� ���� ����������, ����������� �� ����������� � ����� ���������� �� ������. ���� � ��� ���� �����-������ ������� ������������ ����� ������, �� ������ ���������� � ��������������.</p> 
					<p>���� ��������� �����, ������� �� ������ ��������� ��� ����, ����� ������������������. ����� �����������, �� ������ �������� ���� ������� � ����������� ��������� ����������, ������� �� ������� ��������������� � ������ �������������. ����, ����������� ���� - ��� ������ ��������� ����� ���� ������������, ������� �� ������� �������� � ����� �������.</p> 
				</div> 
				<fieldset> 
					<legend>������� ��� ������������ ������ �� 2 �� 25 ��������</legend> 
					<div class="infldset"> 
						<input type="hidden" name="form_sent" value="1" /> 
						<label><strong>���</strong><br /><input type="text" name="name" maxlength="30" value="{{ name }}" /><br /></label> 
					{{ error.name.print }}
					</div> 
				</fieldset> 
			</div> 
			<div class="inform"> 
				<fieldset> 
					<legend>������� � ����������� ���� ������</legend> 
					<div class="infldset"> 
						<label class="conl"><strong>������</strong><br /><input type="text" name="password" maxlength="30" value="" /><br /></label> 
						<label class="conl"><strong>����������� ������</strong><br /><input type="text" name="confirm" maxlength="30" value="" /><br /></label> 
						<p class="clearb">������ ������ ���� �� ����� 4 � �� ����� 16 �������� � �����. ������ ������������ � �������� ��������.</p> 
					{{ error.password.print }}
					</div> 
				</fieldset> 
			</div> 
			<div class="inform"> 
				<fieldset> 
					<legend>������� ���������� e-mail �����</legend> 
					<div class="infldset"> 
					<label><strong>E-mail</strong><br /> 
						<input type="text" name="mail" maxlength="60" value="{{ mail }}" /><br /></label>
					{{ error.mail.print }}
					</div> 
				</fieldset> 
			</div>
			<div class="inform"> 
				<fieldset> 
					<legend>������� ����� � ��������</legend> 
					<div class="infldset"> 
					<label><strong>�����</strong><br /> 
						<input type="text" name="captcha" maxlength="5" size="30" /><img src="{{ url_captcha }}" /><br /></label> 
						{{ error.captcha.print }}
					</div> 
				</fieldset> 
			</div>
			<script language="javascript">
				function forum_change(){
					if (document.getElementById('forum_captcha').className == "yellow"){
						document.getElementById('forum_captcha').className = "red";
						document.getElementById('forum_captcha_sess').value = 0;
					} else {
						document.getElementById('forum_captcha').className = "yellow";
						document.getElementById('forum_captcha_sess').value = 1;
					}
				}
			</script>
			<div class="inform"> 
				<fieldset>
					<legend>� �� �����.</legend> 
					<input type="checkbox" id="forum_captcha" onclick="forum_change();" value="� - �������!">
					<input type="hidden" name="forum_captcha_sess" id="forum_captcha_sess" value="0">
					{{ error.bot.print }}
				</fieldset> 
			</div>
			<p><input type="submit" name="submit" value="�����������" /></p> 
		</form> 
	</div> 
</div> 