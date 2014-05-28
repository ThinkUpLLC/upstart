<ul class="section-nav">
  <li><a {if $active eq 'about'}class="active" {/if}  href="{$site_root_path}about/">About</a></li>
  <li><a {if $active eq 'values'}class="active" {/if} href="{$site_root_path}about/values.php">Values</a></li>
  <li><a {if $active eq 'terms'}class="active" {/if}  href="{$site_root_path}about/terms.php">Terms</a></li>
  <li><a {if $active eq 'privacy'}class="active" {/if}href="{$site_root_path}about/privacy.php">Privacy</a></li>
  <li><a {if $active eq 'faq'}class="active" {/if}href="{$site_root_path}about/faq.php">FAQ</a></li>
  <li><a {if $active eq 'contact'}class="active" {/if}href="{$site_root_path}about/contact.php">Contact</a></li>
</ul>