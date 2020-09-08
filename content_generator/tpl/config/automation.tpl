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
<div class="row mt-2">
	<div class="col-sm">
		<form action="" method="post" name="generate_news">
			<input type="hidden" name="generate_news" value="1">
			<div class="card">
				<div class="card-header">Новости</div>
				<div class="card-body">
					<div class="list">
						Количество: <input type="text" value="10000" name="count">
					</div>
					<div class="list">
						<div class="progressbar">
							<div class="progress-label"></div>
						</div>
					</div>
				</div>
				<div class="card-footer"><input type="submit" value="Начать!" class="btn btn-outline-primary"></div>
			</div>
		</form>
	</div>
	<div class="col-sm">
		<form action="" method="post" name="generate_static">
			<input type="hidden" name="generate_static" value="1">
			<div class="card">
				<div class="card-header">Статьи</div>
				<div class="card-body">
					<div class="list">
						Количество: <input type="text" value="10000" name="count">
					</div>
					<div class="list">
						<div class="progressbar">
							<div class="progress-label"></div>
						</div>
					</div>
				</div>
				<div class="card-footer">
					<input type="submit" value="Начать!" class="btn btn-outline-primary">
				</div>
			</div>
		</form>
	</div>
</div>
	<script>
$(document).ready(function () {
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
	var ajaxLongProcess = function (form, actionName, count) {
		var chunk_size = 1000;
		var chunk_count = (count > chunk_size) ? Math.ceil(count / chunk_size) : 1;
		i = 1;
		progressbar = form.find(".progressbar");
		progressLabel = form.find(".progress-label");
		button = form.find('input[type=submit]');
		button.hide();
		progressbar.show();
		progressbar.progressbar({
			value: false,
			change: function () {
				progressLabel.text(progressbar.progressbar("value") + "%");
			},
			complete: function () {
				progressLabel.text("Готово!");
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
			data: { i: i, count: count, chunk_size: chunk_size, chunk_count: chunk_count, real_count: (count > chunk_size * i) ? chunk_size : count - chunk_size * (i - 1), actionName: actionName },
			success: function (response) {
				console.log(i + " ::  " + chunk_count);
				progressbar.progressbar("value", (100 / chunk_count) * i);
				i++;
			},
			complete: function () {
				if (i <= chunk_count) {
					doAjax(count, chunk_size, chunk_count, actionName);
				} else {
					doneCallback();
				}
			}
		});
	}
});
</script>
