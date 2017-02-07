<script language="javascript">

	var newLineIndex = 1;

	function createVLine(vid) {
		var x = document.getElementById('vlist_' + vid);
		var nr = x.insertRow(x.rows.length);
		nr.insertCell(-1).innerHTML = '<input value="" size="40" name="viname_' + vid + '_' + newLineIndex + '" />';

		var cell = nr.insertCell(-1);
		cell.style.textAlign = 'right';
		cell.innerHTML = '&nbsp; 0 &nbsp;<b>=&gt;</b>&nbsp;<input size=4 name="vicount_' + vid + '_' + newLineIndex + '" /> &nbsp;';

		cell = nr.insertCell(-1);
		cell.style.textAlign = 'center';
		cell.innerHTML = '<input type=checkbox value="1" name="viactive_' + vid + '_' + newLineIndex + '" />';

		cell = nr.insertCell(-1);
		cell.style.textAlign = 'center';
		cell.innerHTML = '<input name="videl_' + vid + '_' + newLineIndex + '" type=checkbox value="1" />';

		newLineIndex++;
	}

	function showHide(mode) {
		var divs = document.getElementsByTagName('div');
		for (i = 0; i < divs.length; i++) {
			if (divs[i].id.match("^vtr_")) {
				divs[i].style.display = mode ? 'inline' : 'none';
			}
		}
	}
</script>

