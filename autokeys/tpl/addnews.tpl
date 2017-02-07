<tr>
	<td width="100%" class="contentHead" colspan="3"><img src="/engine/skins/default/images/nav.gif" hspace="8" alt=""/>Автоматическая
		генерация ключевых слов
	</td>
</tr>
<tr>
	<td colspan="3">
		<table width="100%">
			<tr>
				<td>
					<input type="checkbox" id="autokeys_generate" name="autokeys_generate" value="1" {% if (flags.checked) %} checked="checked" {% endif %}class="check"/>
				</td>
				<td><label for="autokeys_generate">Генерировать keywords?</label></td>
			</tr>
			<tr>
				<td colspan="2">
					<div id="autokeysArea" style="border: #EEEEEE 1px solid; height: 30px; text-align: center;" onclick="autokeysAjaxUpdate();">
						.. сгенерировать сейчас..
					</div>
					<input type="button" id="autokeysButton" value="Перенести.." onclick="autokeysSetKeywords();"/>
			</tr>
		</table>
	</td>
</tr>
<script language="javascript">
	var autokeysAjaxUpdate = function () {
		ngShowLoading();
		$.post('/engine/rpc.php', {
			json: 1,
			methodName: 'plugin.autokeys.generate',
			rndval: new Date().getTime(),
			params: json_encode({
				'title': $('#newsTitle').val(),
				'content': $('#ng_news_content_short').val() + ' ' + $('#ng_news_content_full').val()
			})
		}, function (data) {
			ngHideLoading();
			// Try to decode incoming data
			try {
				resTX = eval('(' + data + ')');
			} catch (err) {
				alert('Error parsing JSON output. Result: ' + linkTX.response);
			}
			if (!resTX['status']) {
				ngNotifyWindow('Error [' + resTX['errorCode'] + ']: ' + resTX['errorText'], 'ERROR');
			}

			$("#autokeysArea").html(resTX['data']);
			$("#autokeysButton").show();
		}, "text").error(function () {
			ngHideLoading();
			ngNotifyWindow('HTTP error during request', 'ERROR');
		});
	};
	$("#autokeysButton").hide();

	var autokeysSetKeywords = function () {
		$("#newsKeywords").val($("#autokeysArea").html());
	}
</script>