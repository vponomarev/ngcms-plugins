{% extends localPath(0) ~ "site.body.tpl" %}
{% block content %}
{% if (flags.link_news) %}
<b>Запрос по новости : <a href="{{ news.url }}">{{ news.title }}</a></b>
<br/><br/>
{%endif %}
{{ description }}
{{ plugin_basket }}
<br/><br/>
{% if (flags.jcheck) %}
<script language="JavaScript">
var FBF_INIT = {{ FBF_DATA }};
function FBF_CHECK() {
 var frm = document.getElementById('feedback_form');
 if (frm == null) return true;

 var i;
 for (i in FBF_INIT) {
 	if (FBF_INIT[i][1]) {
 	 if (FBF_INIT[i][0] == 'date') {
		if ((frm[i+':day'].value == '1') && (frm[i+':month'].value == '1') && (frm[i+':year'].value == '1970')) {
 			alert('{{ lang['feedback:form.err.notfilled'] }} ('+FBF_INIT[i][2]+')!');
 			frm[i+':day'].focus();
 			return false;
		}
 	 } else if (frm[i].value == '') {
 		alert('{{ lang['feedback:form.err.notfilled'] }} ('+FBF_INIT[i][2]+')!');
 		frm[i].focus();
 		return false;
 	}
   }
 }
 return true;
}
</script>
{% endif %}
<form method="post" action="{{ form_url }}" id="feedback_form" name="feedback_form">
{{ hidden_fields }}
<input type="hidden" name="id" value="{{ id }}"/>
<table>
{% if (flags.error) %}<tr><td colspan="2" style="background: red; color: white;">{{ errorText }}</td></tr>{% endif %}
{% for entry in entries %}
{% if entry.type == 'text' %}
<tr>
 <td>{{ entry.title }}:</td>
 <td><input style="width: 300px;" type="text" name="{{ entry.name }}" value="{{ entry.value }}"/></td>
</tr>
{% endif %}
{% if entry.type == 'textarea' %}
<tr>
 <td>{{ entry.title }}:</td>
 <td><textarea name="{{ entry.name }}" cols="50" rows="5">{{ entry.value }}</textarea></td>
</tr>
{% endif %}
{% if entry.type == 'select' %}
<tr>
 <td>{{ entry.title }}:</td>
 <td><select name="{{ entry.name }}">{{ entry.options.select }}</select></td>
</tr>
{% endif %}
{% if entry.type == 'date' %}
<tr>
 <td>{{ entry.title }}:</td>
 <td><select name="{{ entry.name }}:day">{{ entry.options.day }}</select>.<select name="{{ entry.name }}:month">{{ entry.options.month }}</select>.<select name="{{ entry.name }}:year">{{ entry.options.year }}</select></td>
</tr>
{% endif %}
{% endfor %}
{% if (flags.captcha) %}
<!-- Captcha check -->
<tr>
 <td>{{ lang['feedback:sform.captcha'] }}:</td>
 <td><input type="text" name="vcode" /> <img id="img_captcha" onclick="this.src='{{ captcha_url }}&rand='+Math.random();" src="{{ captcha_url }}&rand={{ captcha_rand }}" alt="captcha" /></td>
</tr>
{% endif %}
{% if (flags.recipients) %}
<tr>
 <td>{{ lang['feedback:sform.elist'] }}:</td>
 <td><select name="recipient">{{ recipients_list }}</select></td>
 </tr>
{% endif %}
</table>
<input type="submit" {% if (flags.jcheck) %}onclick="return FBF_CHECK();" {% endif %}value="{{ lang['feedback:form.request'] }}"/>
</form>
{% endblock %}