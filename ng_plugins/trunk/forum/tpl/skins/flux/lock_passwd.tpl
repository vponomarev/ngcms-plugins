<div class="blockform">
	<h2><span>������ �� ����� ��� �������</span></h2> 
	<div class="box"> 
		<form id="lock" method="post" action=""> 
			<div class="inform"> 
				<fieldset> 
					<legend>������� ������</legend> 
						<div class="infldset"> 
							{% if (error_text['empty_passwd']) %}�� �� ����� ������<br />{% endif %}
							{% if (error_text['error_passwd']) %}�������� ������{% endif %}
							<label class="conl"><strong>������</strong><br /><input type="password" name="lock_passwd" size="30" maxlength="30" tabindex="2" value="{{ lock_passwd }}" /><br /></label> 
						</div> 
				</fieldset> 
			</div>
			<input type="hidden" name="submit" value="1">
			<p><input type="submit" name="submit" value="����" tabindex="3" /></p> 
		</form> 
	</div> 
</div>