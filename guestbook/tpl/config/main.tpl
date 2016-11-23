<style>
.navbutton {
  text-decoration: none;
}
</style>
<div style="text-align : left;">
  <table class="content" border="0" cellspacing="0" cellpadding="0" align="center">
    <tr>
      <td width="100%" colspan="2" class="contentHead"><img src="{{skins_url}}/images/nav.gif" hspace="8" alt="" />
        <a href="admin.php?mod=extras" title="{{ lang['gbconfig']['edit_extras'] }}">{{ lang['gbconfig']['edit_extras'] }}</a> &#8594;
        <a href="{{admin_url}}/admin.php?mod=extra-config&plugin=guestbook">{{ lang['gbconfig']['guestbook'] }}</a>
      </td>
    </tr>
  </table>

  <table border="0" cellspacing="0" cellpadding="0" width="100%">
    <tr align="center">
      <td width="100%" class="contentNav" align="center" style="background-repeat: no-repeat; background-position: left;">
        <a href="{{admin_url}}/admin.php?mod=extra-config&plugin=guestbook" class="navbutton">{{ lang['gbconfig']['menu_settings'] }}</a>
        <a href="{{admin_url}}/admin.php?mod=extra-config&plugin=guestbook&action=show_messages" class="navbutton">{{ lang['gbconfig']['menu_messages'] }}</a>
        <a href="{{admin_url}}/admin.php?mod=extra-config&plugin=guestbook&action=manage_fields" class="navbutton">{{ lang['gbconfig']['menu_fields'] }}</a>
        <a href="{{admin_url}}/admin.php?mod=extra-config&plugin=guestbook&action=social" class="navbutton">{{ lang['gbconfig']['menu_social'] }}</a>
      </td>
    </tr>
  </table>

{{ entries }}

</div>
