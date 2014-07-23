{*
    The field specific messages just get piped out as text.
    The ones that end up at the top get sent as JS.
    TODO: Stop relying on JS for this!
*}
{if isset($field)}
    {if isset($success_msgs.$field)}{strip}
        <div class="help-block">
        {if isset($success_msg_no_xss_filter)}
            {$success_msgs.$field}
        {else}
            {$success_msgs.$field|filter_xss}
        {/if}
        </div>{/strip}
    {/if}
    {if isset($error_msgs.$field)}{strip}
        <div class="warning-block">
        {if isset($error_msg_no_xss_filter)}
            {$error_msgs.$field}
        {else}
            {$error_msgs.$field|filter_xss}
        {/if}
        </div>{/strip}
    {/if}
    {if isset($info_msgs.$field)}{strip}
        <div class="help-block">
        {if isset($info_msg_no_xss_filter)}
            {$info_msgs.$field}
        {else}
            {$info_msgs.$field|filter_xss}
        {/if}
        </div>{/strip}
    {/if}
{else}
    {if isset($success_msg)}
        {assign var=msg_type value="success"}
        {assign var=msg_classes value="fa-override-before fa-check-circle"}
        {if isset($success_msg_no_xss_filter)}
            {assign var=msg value=$success_msg}
        {else}
            {assign var=msg value=$success_msg|filter_xss}
        {/if}
    {/if}
    {if isset($error_msg)}
        {assign var=msg_type value="warning"}
        {assign var=msg_classes value="fa-override-before fa-exclamation-triangle"}
        {if isset($error_msg_no_xss_filter)}
            {assign var=msg value=$error_msg}
        {else}
            {assign var=msg value=$error_msg|filter_xss}
        {/if}
    {/if}
    {if isset($info_msg)}
        {assign var=msg_type value="info"}
        {if isset($info_msg_no_xss_filter)}
            {assign var=msg value=$info_msg}
        {else}
            {assign var=msg value=$info_msg|filter_xss}
        {/if}
    {/if}

    {if isset($msg) and isset($msg_type)}{literal}
    <script>
    var app_message = {};
    app_message.msg = {/literal}"{$msg}"{literal};
    app_message.type = {/literal}"{$msg_type}"{literal};
    </script>
    {/literal}{/if}
{/if}