{{ error.print }}
<div class="blockform">
	<h2><span>Сообщить модератору</span></h2>
	<div class="box">
		<form id="report" method="post" action="">
			<div class="inform">
				<fieldset>
					<legend>Пожалуйста, сделайте краткое описание причины, по которой вы обращаете внимание модератора на это сообщение.</legend>

					<div class="infldset txtarea">
						<input type="hidden" name="form_sent" value="1" />
						<label><strong>Причина сообщения</strong><br /><textarea name="message" rows="5" cols="60"></textarea><br /></label>
					</div>
				</fieldset>
			</div>
			<p><input type="submit" name="submit" value="Отправить" accesskey="s" /><a href="javascript:history.go(-1)">Вернуться назад</a></p>

		</form>
	</div>
</div>