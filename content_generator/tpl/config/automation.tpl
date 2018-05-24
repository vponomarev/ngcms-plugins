<script>

	$(document).ready(function() {

		var i;
		var progressbar;
		var progressLabel;
		var button;

		$('form').submit(function (event) {

			var actionName = $(this).attr('name');
			var count = parseInt($(this).find('input[name=count]').val());

			ajaxLongProcess($(this), actionName, count);

			return false;
		});

		var ajaxLongProcess = function(form, actionName, count){
			var chunk_size = 1000;
			var chunk_count = (count > chunk_size) ? Math.ceil(count/chunk_size) : 1;
			i = 1;
			progressbar = form.find(".progressbar");
			progressLabel = form.find(".progress-label");
			button = form.find('input[type=submit]');

			button.hide();
			progressbar.show();

			progressbar.progressbar({
				value: false,
				change: function() {
					progressLabel.text( progressbar.progressbar( "value" ) + "%" );
				},
				complete: function() {
					progressLabel.text( "Готово!" );
				}
			});

			doAjax(count, chunk_size, chunk_count, actionName);
		}

		function doneCallback() {
			button.show();
			progressbar.hide();
		}

		function doAjax(count, chunk_size, chunk_count, actionName) {

			$.ajax({
				method: "POST",
				cache: false,
				url: '/plugin/content_generator/',
				data: { i: i, count: count, chunk_size: chunk_size, chunk_count: chunk_count, real_count: (count > chunk_size*i) ? chunk_size : count - chunk_size*(i-1), actionName: actionName },
				success: function(response) {
					console.log(i + " ::  " + chunk_count);
					progressbar.progressbar("value", (100/chunk_count)*i);
					i++;
				},
				complete: function() {
					if(i <= chunk_count) {
						doAjax(count, chunk_size, chunk_count, actionName);
					} else {
						doneCallback();
					}
				}
			});

		}

	});

</script>

<style>
	.ui-progressbar {
		position: relative;
	}
	.progress-label {
		position: absolute;
		left: 50%;
		top: 0px;
		font-weight: bold;
		text-shadow: 1px 1px 0 #fff;
	}
</style>

<table border="0" cellspacing="0" cellpadding="0" class="content">
	<tbody>

	<tr>
		<td width="50%" valign="top" class="contentEntry1">
			<form action="" method="post" name="generate_news">
				<input type="hidden" name="generate_news" value="1">
				<table border="0" cellspacing="0" cellpadding="0" class="content" align="center">
					<tbody>
					<tr>
						<td class="contentHead"><img src="{{ admin_url }}/skins/default/images/nav.gif" hspace="8" alt="">Новости</td>
					</tr>
					<tr>
						<td>

							<div class="list">
								Количество: <input type="text" value="10000" name="count">
							</div>

							<div class="list">
								<div class="progressbar"><div class="progress-label"></div></div>
							</div>

						</td>
					</tr>
					<tr align="center">
						<td width="100%" class="contentEdit" align="center" valign="top">
							<input type="submit" value="Начать!" class="button">
						</td>
					</tr>
					</tbody>
				</table>
			</form>
		</td>
	</tr>

	<tr>
		<td width="50%" valign="top" class="contentEntry1">
			<form action="" method="post" name="generate_static">
				<input type="hidden" name="generate_static" value="1">
				<table border="0" cellspacing="0" cellpadding="0" class="content" align="center">
					<tbody>
					<tr>
						<td class="contentHead"><img src="{{ admin_url }}/skins/default/images/nav.gif" hspace="8" alt="">Статьи</td>
					</tr>
					<tr>
						<td>

							<div class="list">
								Количество: <input type="text" value="10000" name="count">
							</div>

							<div class="list">
								<div class="progressbar"><div class="progress-label"></div></div>
							</div>

						</td>
					</tr>
					<tr align="center">
						<td width="100%" class="contentEdit" align="center" valign="top">
							<input type="submit" value="Начать!" class="button">
						</td>
					</tr>
					</tbody>
				</table>
			</form>
		</td>
	</tr>

	</tbody>
</table>