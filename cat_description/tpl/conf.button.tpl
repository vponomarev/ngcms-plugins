<script type="text/javascript">
	function ChangeOption(selectedOption) {
		document.getElementById('list').style.display = "none";
		document.getElementById('addnew').style.display = "none";

		if (selectedOption == 'list') {
			document.getElementById('list').style.display = "";
		}
		if (selectedOption == 'addnew') {
			document.getElementById('addnew').style.display = "";
		}
	}
</script>

<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr align="center">
		<td width="100%" class="contentNav" align="center">
			<input type="button" onmousedown="javascript:ChangeOption('list')" value="{l_cat_description:button_list}" class="navbutton"/>
			<input type="button" onmousedown="javascript:ChangeOption('addnew')" value="{l_cat_description:button_add}" class="navbutton"/>
		</td>
	</tr>
</table><br/>