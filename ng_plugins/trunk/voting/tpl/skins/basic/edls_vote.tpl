
<div id="zz_voting_{voteid}">
<fieldset>
<legend>�����: <b>{votename}</b></legend>
[votedescr]<small>��������: {votedescr}</small><br/>[/votedescr]
<form action="{post_url}" method="get" id="voteForm_{voteid}">
<input type=hidden name=action value=vote />
<input type=hidden name=voteid value="{voteid}" />
<input type=hidden name=referer value="{REFERER}" />
{votelines}
<input type=submit value="����������" onclick="return make_voteL(0,{voteid});" /> <input type=button value="����������" onclick="document.location='{post_url}?mode=show&voteid={voteid}';" />
</form>
</fieldset>
</div>