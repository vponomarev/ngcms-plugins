<form action="" method="post" name="Reput">
{{ error.print }}
<div class="blockform">
	<h2><span>Пожалуйста, заполните форму</span></h2>
	<div class="box">

		<div class="inbox">

		<table cellspacing="0">
				<tr>
					<td  class="tc4" width="30%">Ваше имя:</td>
					<td  class="tc4" width="70%">{{ addusers }}</td>
				</tr>

				<tr>
					<td class="tc4" width="30%">Кому изменяем рейтинг:</td>
					<td class="tc4" width="70%">{{ users }}</td>
				</tr>
				<tr>
					<td class="tc4" width="30%">Причина изменения рейтинга:</td>
					<td class="tc4" width="70%"><textarea cols='60' rows='10' name="message" class='textinput'></textarea></td>

				</tr>				
				<tr>
					<td class="tc4" width="30%">Метод:</td>
					<td class="tc4" width="70%">{% if (info.method == 1) %}Увеличение рейтинга{% elseif (info.method == 2) %}Уменьшение рейтинга{% endif %}</td>
				</tr>
			</table>
			<table cellspacing="0">
				<tr>

					<td  class="tc4" style="text-align:center;"><input type="submit" name="submit" value="Отправить"> : <a href="javascript:history.go(-1)">Вернуться назад</a></td>
				</tr>
			</table>
		</div>
	</div>
</div>
</form>
