<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width"/>
</head>
<body>
  <table class="body">
    <tr>
      <td class="center" align="center" valign="top">
        <center>


          <table class="row header">
            <tr>
              <td>
                <table class="container">
                  <tr>
                    <td class="wrapper last">

                      <table class="twelve columns">
                        <tr>

                          <td class="six sub-columns">
                            <a href="https://thinkup.com" style="text-decoration:none;"><img src="https://thinkup.com/join/assets/img/thinkup-logo.png" alt="ThinkUp" width="100" height="28" style="width: 100px !important; height: 28px !important;"></a>
                          </td>
                          <td class="six sub-columns last" style="text-align: right; vertical-align: bottom;">
                            <a href="{$thinkup_url}">Your Insights</a>
                          </td>
                          <td class="expander"></td>
                        </tr>
                      </table>

                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
          <br>


          <table class="row" style="background: #41DAB3">
            <tr>
              <td class="wrapper last">

                <table class="twelve columns">
                  <tr>
                    <td>
                      <a href="{$site_url}user/membership.php"><img width="580" height="360" src="https://thinkup.com/join/assets/img/email/reminder-4-promo-{if $membership_level eq 'Member'}v2{else}pro{/if}.png"
                      alt="Don't miss out! Your ThinkUp trial is ending. Join now! Just ${if $membership_level eq 'Member'}5{else}10{/if}/month"></a>
                    </td>
                    <td class="expander"></td>
                  </tr>
                </table>

              </td>
            </tr>
          </table>

          <table class="row" style="margin-top: 20px;">
            <tr>
              <td class="wrapper last">

                <table class="twelve columns">
                  <tr>
                    <td class="text-pad">
                      <p>Don’t lose your personal insights at <a href="{$thinkup_url}">{$thinkup_url}</a> &mdash; this is your last chance to <a href="{$site_url}user/membership.php">join</a>! It only takes a moment to complete your membership.</p>

                    </td>
                    <td class="expander"></td>
                  </tr>
                </table>

              </td>
            </tr>
          </table>


          <table class="container">
            <tr>
              <td>
                <table class="row footer">
                  <tr>
                    <td class="wrapper last">

                      <table class="twelve columns">
                        <tr>
                          <td align="center">
                            <center>
                              <p style="text-align:center;"><a href="{$site_url}user/membership.php">Manage your account</a> or <a href="{$site_url}about/contact.php">contact us</a></p>
                              <p style="text-align:center;">220 E 23rd #601, NY, NY 10010</p>
                            </center>
                          </td>
                          <td class="expander"></td>
                        </tr>
                      </table>

                    </td>
                  </tr>
                </table>

              </td>
            </tr>
          </table>

        </center>
      </td>
    </tr>
  </table>
</body>
</html>