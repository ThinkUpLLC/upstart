<p style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left">We just confirmed your ThinkUp membership payment. Here are the details:</p>

<ul>
	<li>You're officially a <strong>ThinkUp {if $member_level eq "Pro"}Pro {/if}Member</strong>! Your username is <strong><a href="{$installation_url}" style="color: #46bcff;">{$thinkup_username}</a></strong>.</li>
	<li>Your Amazon Payments account has been charged <strong>${$amount}</strong> for your membership.</li>
	<li>Your membership lasts until <strong>{$renewal_date}</strong>, when it will automatically renew.</li>
</ul>

<p style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left">We truly appreciate you joining the ThinkUp community, and we hope you'll love ThinkUp as much as we enjoy building it for you.</p>

<div style="text-align:center;"><!--[if mso]>
  <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="https://www.thinkup.com/join/user/membership.php" style="height:40px;v-text-anchor:middle;width:200px;" arcsize="10%" stroke="f" fillcolor="#125C9C">
    <w:anchorlock/>
    <center>
  <![endif]-->
      <a href="{$installation_url}"
style="background-color:#125C9C;border-radius:4px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:13px;font-weight:bold;line-height:40px;text-align:center;text-decoration:none;width:200px;margin-bottom:20px;-webkit-text-size-adjust:none;">See your ThinkUp insights</a>
  <!--[if mso]>
    </center>
  </v:roundrect>
<![endif]--></div>

<p style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left">p.s. If you ever have questions or comments, just contact us at <a href="mailto:help@thinkup.com">help@thinkup.com</a> &mdash; we'd love to hear from you.</p>