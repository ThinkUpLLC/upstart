{if isset($field)}
    {if isset($success_msgs.$field)}
       <div class="alert alert-success"><i class="icon icon-ok-sign"></i>
           {if isset($success_msg_no_xss_filter)}
               {$success_msgs.$field}
           {else}
               {$success_msgs.$field|filter_xss}
           {/if}
       </div>
    {/if}
    {if isset($error_msgs.$field)}
    <div class="alert alert-danger"><i class="icon icon-warning-sign"></i>
           {if isset($error_msg_no_xss_filter)}
               {$error_msgs.$field}
           {else}
               {$error_msgs.$field|filter_xss}
           {/if}
    </div>
    {/if}
    {if isset($info_msgs.$field)}
    {if isset($success_msgs.$field) OR isset($error_msgs.$field)}<br />{/if}
        <div class="alert alert-info"><i class="icon icon-info-sign"></i>
             {if isset($info_msg_no_xss_filter)}
                {$info_msg_no_xss_filter}
             {else}
                {$info_msgs.$field|filter_xss}
             {/if}
        </div>
    {/if}
{else}
    {if isset($success_msg)}
       <div class="alert alert-success"><i class="icon icon-ok-sign"></i>
           {if isset($success_msg_no_xss_filter)}
               {$success_msg}
           {else}
               {$success_msg|filter_xss}
           {/if}
        </div>
    {/if}
    {if isset($error_msg)}
    <div class="alert alert-danger"><i class="icon icon-warning-sign"></i>
           {if isset($error_msg_no_xss_filter)}
               {$error_msg}
           {else}
               {$error_msg|filter_xss}
           {/if}
    </div>
    {/if}
    {if isset($info_msg)}
        <div class="alert alert-info"><i class="icon icon-info-sign"></i>
        {if isset($info_msg_no_xss_filter)}
                {$info_msg}
             {else}
                {$info_msg|filter_xss}
             {/if}
        </div>
    {/if}
{/if}
