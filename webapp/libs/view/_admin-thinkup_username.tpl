{if $subscriber->installation_url neq null} {* show link to installation *}
<a href="{$subscriber->installation_url}" target="_new">{$subscriber->thinkup_username}</a>
{else}
{if $subscriber->thinkup_username neq null} {* username is set, but not installed *}
{$subscriber->thinkup_username} <a href="subscriber.php?action=install&id={$subscriber->id}" class="btn btn-success btn-mini">Install</a>
{else} {* username is not set *}
<form action="subscriber.php?action=setusername&id={$subscriber->id}" method="get"><input type="text" width="10" value="{$subscriber->subdomainified_username}" placeholder="" name="username"> <input type="hidden" name="id" value="{$subscriber->id}"> <input type="hidden" name="action" value="setusername"><input type="submit" value="Set" class="btn btn-default"></form>
{/if}
{/if}