            {if $success_msg}
             <span class="label label-info" style="">
                    <i class="icon icon-lightbulb"></i>
                   {if $success_msg_no_xss_filter}
                       {$success_msg}
                   {else}
                       {$success_msg|filter_xss}
                   {/if}

             </span>
            {/if}
            {if $error_msg}
             <span class="label label-error" style="">
 
                    <i class="icon icon-warning-sign"></i>
                   {if $error_msg_no_xss_filter}
                       {$error_msg}
                   {else}
                       {$error_msg|filter_xss}
                   {/if}

            </span>
            {/if}
            {if $info_msg}
                {if $success_msg OR $error_msg}<br />{/if}
            <span class="label label-success"> 

                     {if $info_msg_no_xss_filter}
                        {$info_msg}
                     {else}
                        {$info_msg|filter_xss}
                     {/if}
                </p>
            </span>
            {/if}
        {/if}
