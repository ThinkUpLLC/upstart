{include file="_appheader.tpl" include_menu=true body_id="welcome"}
{include file="_appusermessage.tpl"}
  <div class="container">
    <div class="stream">
      <div class="date-group today">
          <div class="date-marker">
              <div class="absolute">Today</div>
          </div>

          <div class="panel panel-default insight insight-default insight-purple insight-hero">
            <div class="panel-heading">
              <h2 class="panel-title insight-color-text">Thanks for joining ThinkUp!</h2>
            </div>
            <div class="panel-desktop-right">
              <div class="panel-body">
                <div class="panel-body-inner">
                  <p>A special offer: Pay for your ThinkUp subscription now and you'll get a <strong>free</strong> copy of our e-book, <em>Insights</em>.</p>

                  <p><a href="{$pay_with_amazon_url}" class="btn btn-big">Pay with Amazon</a></p>

                  <p>Have questions or need help? Email us any time at <a href="mailto:help@thinkup.com">help@thinkup.com</a>.</p>
                </div>
              </div>
            </div>
          </div>

          <div class="panel panel-default insight insight-default insight-salmon">
            <div class="panel-heading">
              <h2 class="panel-title insight-color-text">Your account is all set up</h2>
            </div>
            <div class="panel-desktop-right">
              <div class="panel-body">
                <div class="panel-body-inner">
                  <p>Head here to see your insights: <a href="{$new_subscriber->installation_url}">{$new_subscriber->installation_url}</a></p>

                  <p>And keep your eyes peeled for an email from us with your first set of ThinkUp insights.</p>
                </div>
              </div>
            </div>
          </div>

          {if $new_subscriber->membership_level eq "Pro"}
          <div class="panel panel-default insight insight-default insight-mint">
            <div class="panel-heading">
              <h2 class="panel-title insight-color-text">Got more social networking accounts?</h2>
            </div>
            <div class="panel-desktop-right">
              <div class="panel-body">
                <div class="panel-body-inner">
                  <p>As a Pro member, you can add up to ten accounts. Connect another <a href="{$new_subscriber->installation_url}account/?p=facebook">Facebook</a> or <a href="{$new_subscriber->installation_url}account/?p=twitter">Twitter</a> account with a click.</p>
                </div>
              </div>
            </div>
          </div>
          {else}
          <div class="panel panel-default insight insight-default insight-mint">
            <div class="panel-heading">
              <h2 class="panel-title insight-color-text">Got a {if $new_subscriber->network eq 'facebook'}Twitter{else}Facebook{/if} account?</h2>
            </div>
            <div class="panel-desktop-right">
              <div class="panel-body">
                <div class="panel-body-inner">
                  <p>You can <a href="{$new_subscriber->installation_url}account/?p={if $new_subscriber->network eq 'facebook'}twitter{else}facebook{/if}">connect your account</a> to ThinkUp and get insights from both Facebook and Twitter in one place.</p>
                </div>
              </div>
            </div>
          </div>
          {/if}

      </div>
    </div>

{include file="_appfooter.tpl"}