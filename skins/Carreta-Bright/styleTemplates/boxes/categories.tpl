<!-- BEGIN: categories -->
<div class="LeftBox">
<div class="subHeading"><h3>{LANG_CATEGORY_TITLE}</h3></div>
<ul id="mainmenu-nav"> <!-- top nav list -->
    <li class="li-nav"><a href="index.php" title="{LANG_HOME}">{LANG_HOME}</a></li>
    <!-- BEGIN: a -->
	<!-- BEGIN: ul_start --><ul class="ul-nav"> <!-- start sub nav list --> <!-- END: ul_start -->
	<!-- BEGIN: li_start -->
	<li class="li-nav"><!-- END: li_start --><a href="index.php?_a=viewCat&amp;catId={DATA.cat_id}" title="{DATA.cat_name}">{DATA.cat_name} ({DATA.noProducts})</a><!-- BEGIN: li_end --></li>
	<!-- END: li_end -->
	<!-- BEGIN: ul_end --></ul></li>
	<!-- END: ul_end -->
    <!-- END: a -->
    <!-- BEGIN: gift_certificates --><li class="li-nav"><a href="index.php?_a=giftCert" title="{LANG_GIFT_CERTS}">{LANG_GIFT_CERTS}</a></li><!-- END: gift_certificates -->
    <!-- BEGIN: sale --><li class="li-nav"><a href="index.php?_a=viewCat&amp;catId=saleItems" title="{LANG_SALE_ITEMS}">{LANG_SALE_ITEMS}</a></li><!-- END: sale -->
</ul>
</div>
<div style="clear: both;"></div>
<!-- END: categories -->
