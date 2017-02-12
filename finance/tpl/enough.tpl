<div class="not_logged">
	<span style="float:right;"><a href="/plugin/finance/"><u>Ваш баланс</u></a>: $<font color="blue"><b>{ubalance}</b></font>&nbsp;</b></span>
	Доступ к данному контенту платный.<br/>Стоимость: $<b> {price}</b>

	<font color=blue>(ваших средств достаточно)</font><br/><br/>
	<form method="get" action="/plugin/finance/">
		<input type=hidden name=mode value=pay>
		<input type=hidden name=access_element_id value="{newsid}">
		<input type=hidden name="back" value="{backurl}">
		<input type=submit value="Оплатить контент"/>
	</form>
	<br/>
</div>
