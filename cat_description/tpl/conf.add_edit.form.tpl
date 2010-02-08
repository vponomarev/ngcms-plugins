<div id="addnew"[add] style="display: none;"[/add]>
<form method="post" action="admin.php?mod=extra-config&amp;plugin=cat_description">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="100%" colspan="2" class="contentHead"><img src="{skins_url}/images/nav.gif" hspace="8" alt="" />[add]{l_cat_description:add_title}[/add][edit]{l_cat_description:edit_title}[/edit]</td>
</tr>
<tr>
<td width="50%" class="contentEntry1">{l_cat_description:is_on}<br /><small>{l_cat_description:is_on_d}</small></td>
<td width="50%" class="contentEntry2">{is_on_list}</td>
</tr>
<tr>
<td width="50%" class="contentEntry1">{l_cat_description:category}<br /><small>{l_cat_description:category_d}</small></td>
<td width="50%" class="contentEntry2">{category_list}</td>
</tr>
<tr>
<td width="50%" class="contentEntry1">{l_cat_description:description}<br /><small>{l_cat_description:description_d}</small></td>
<td width="50%" class="contentEntry2"><TEXTAREA NAME="description" WRAP="virtual" COLS="100" ROWS="20">[edit]{description}[/edit]</TEXTAREA>
</tr>
<tr><td width="100%" colspan="2">&nbsp;</td></tr>
<tr>
<td width="100%" colspan="2" class="contentEdit" align="center">
<input type="submit" value="[add]{l_cat_description:add_submit}[/add][edit]{l_cat_description:edit_submit}[/edit]" class="button" />
<input type="hidden" name="action" value="confirm" />
<input type="hidden" name="id" value="[add]0[/add][edit]{id}[/edit]" />
</td>
</tr>
</table>
</form>
</div>