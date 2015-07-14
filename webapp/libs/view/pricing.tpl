{include file="_header.marketing.tpl" marketing_page=true hide_join=true
body_classes="marketing marketing-page hide-social" body_id="marketing-subscribe"}

  <div class="container">

    <section class="section" id="section-page-header">
      <h2 class="section-header">ThinkUp starts at five bucks a month.</h2>
      <h3 class="section-subheader">(Our annual plans are even cheaper!)</h3>
    </section>
  </div>
  <div class="container">

    <div class="section subscription-levels">

    <table class="table table-responsive" id="pricing-table">
      <tr>
        <th></th>
        <th class="">
          <h3 class="subsection-header pricing-member">1 account</h3>
          <p>per social network</p>
          <p><i class="fa fa-twitter"></i> <i class="fa fa-instagram"></i> <i class="fa fa-facebook"></i></p>
        </th>
        <th class="">
          <h3 class="subsection-header pricing-pro">More than 1 account</h3>
          <p>per social network</p>
          <p>
            <i class="fa fa-twitter"></i> <i class="fa fa-instagram"></i> <i class="fa fa-facebook"></i>
            <i class="fa fa-twitter"></i> <i class="fa fa-instagram"></i> <i class="fa fa-facebook"></i>
            <i class="fa fa-twitter"></i> <i class="fa fa-instagram"></i> <i class="fa fa-facebook"></i>
          </p>
        </th>
      </tr>

      <tr>
        <td class="table-label">
          <p>per month</p>
        </td>
        <td><h2 class="pricing-member">$5</h2></td>
        <td><h2 class="pricing-pro">$10</h2></td>
      </tr>

      <tr>
        <td class="table-label">
          <p>per year</p>
          <p>(2 months <strong>FREE</strong>)</p>
        </td>
        <td><h2 class="pricing-member">$50</h2></td>
        <td><h2 class="pricing-pro">$100</h2></td>
      </tr>
      <tr>
        <td colspan="3" >
          <div class="signup-buttons">
            <a href="{$twitter_member_link}" class="btn btn-pill-medium btn-twitter"><small>Sign up with</small><br>Twitter</a>
            <a href="{$facebook_member_link}" class="btn btn-pill-medium btn-facebook"><small>Sign up with</small><br>Facebook</a>
          </div>
        </td>
      </tr>
<!--
      <tr>
        <td colspan="3" class="media-publisher-tout">
          <p><strong>Media, Publishers and Brands</strong>:
            Need site licenses, invoicing, and other business features?<br />
            <a href="{$site_root_path}about/contact.php?type=executive">Contact us</a> and we'll take care of you.
          </p>
      </tr>
-->
    </table>


    </div>
  </div>
{include file="_footer.marketing.tpl"}

</body>

</html>
