{description}
<br/><br/>
[jcheck]
<script language="JavaScript">
var FBF_INIT = {FBF_DATA};
function FBF_CHECK() {
 var frm = document.getElementById('feedback_form');
 if (frm == null) return true;

 var i;
 for (i in FBF_INIT) {
 	if (FBF_INIT[i][1]) {
 	 if (FBF_INIT[i][0] == 'date') {
		if ((frm[i+':day'].value == '1') && (frm[i+':month'].value == '1') && (frm[i+':year'].value == '1970')) {
 			alert('�� �� ��������� �������� ������������� ��������� ('+i+')!');
 			frm[i+':day'].focus();
 			return false;
		}
 	 } else if (frm[i].value == '') {
 		alert('�� �� ��������� �������� ������������� ��������� ('+i+')!');
 		frm[i].focus();
 		return false;
 	}
   }
 }
 return true;
}
</script>
[/jcheck]
<form method="post" action="{form_url}" id="feedback_form" name="feedback_form">
<input type="hidden" name="plugin_cmd" value="post"/>
<input type="hidden" name="id" value="{id}"/>
<table>
{entries}
</table>
<input type="submit" [jcheck]onclick="return FBF_CHECK();" [/jcheck]value="��������� ������"/>
</form>
