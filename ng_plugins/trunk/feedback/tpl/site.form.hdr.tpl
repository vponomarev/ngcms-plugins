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
 			alert('{l_feedback:form.err.notfilled} ('+FBF_INIT[i][2]+')!');
 			frm[i+':day'].focus();
 			return false;
		}
 	 } else if (frm[i].value == '') {
 		alert('{l_feedback:form.err.notfilled} ('+FBF_INIT[i][2]+')!');
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
<input type="hidden" name="id" value="{id}"/>
<table>
[error]<tr><td colspan="2" style="background: red; color: white;">{errorText}</td></tr>[/error]
{entries}
{captcha}
{elist}
</table>
<input type="submit" [jcheck]onclick="return FBF_CHECK();" [/jcheck]value="{l_feedback:form.request}"/>
</form>
