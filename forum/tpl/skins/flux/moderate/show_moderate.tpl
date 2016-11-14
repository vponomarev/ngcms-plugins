<div class="blockform">
	<h2><span>Перенести тему</span></h2>
	<div class="box">
		<form method="post" action="">
			<div class="inform">
				<fieldset>
					<legend>Выберите форум</legend>
					<div class="infldset">
						<label>Куда перенести<br />
							<select name="move_to_forum">
								{{ entries }}
							</select>
							<br />
						</label>
					</div>
				</fieldset>
			</div>
			<p><input type="submit" name="submit" value="Перенести" /><a href="javascript:history.go(-1)">Назад</a></p>
		</form>
	</div>
</div>