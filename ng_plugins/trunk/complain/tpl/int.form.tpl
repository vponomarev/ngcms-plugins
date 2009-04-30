<style type="text/css">
#senderror {padding-top:10px;background:#fff7e1;border:1px dashed #e8e8e8;margin-top:10px;padding-bottom:20px;}
#senderror select.error {border:1px solid #e8e8e8;background:#fff;}
#senderror input.report{border:1px solid #e8e8e8;background:#fff;}
#senderror .texth {float:left;position:relative;left:10px;}
#senderror .formh {float:right;position:relative;top:-4px;right:10px;}
</style>
<div id="senderror">
<div class="texth">Сообщить об ошибке:</div>
<div class="formh">
	<form method="POST" target="_blank" action="">
	<input type="hidden" name="action" value="plugin"/>
	<input type="hidden" name="plugin" value="complain"/>
	<input type="hidden" name="plugin_cmd" value="post"/>
	<input type="hidden" name="ds_id" value="{ds_id}"/>
	<input type="hidden" name="entry_id" value="{entry_id}"/>
	<select name="error" class="error" id="errorSelect">
	<option value="">Выберите тип ошибки..</option>
	{errorlist}
	</select> 
	<input type="submit" class="report" value="Отправить" onclick="if (document.getElementById('errorSelect').value==''){alert('Необходимо выбрать тип ошибки!'); return false;}"/>
	</form>
</div>	
</div>
