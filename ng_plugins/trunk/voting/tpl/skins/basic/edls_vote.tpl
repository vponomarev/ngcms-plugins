
<div id="zz_voting_{voteid}">
<fieldset>
<legend>�����: <b>{votename}</b></legend>
<form action="/plugin/voting/" method="get" id="voteForm_{voteid}">
<input type=hidden name=action value=vote />
<input type=hidden name=voteid value="{voteid}" />
<input type=hidden name=referer value="{REFERER}" />
{votelines}
<input type=submit value="����������" onclick="return make_voteL(0,{voteid});" /> <input type=button value="����������" onclick="document.location='/plugin/voting/';" />
</form>
</fieldset>
</div>