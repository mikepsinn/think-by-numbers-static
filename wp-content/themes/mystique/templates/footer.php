<?php

/*
 * @template  Mystique
 * @revised   December 20, 2011
 * @author    digitalnature, http://digitalnature.eu
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */

// Document footer.
// This is a template part which is displayed on every page of the website.

?>

         </div>
       </div>
       <!-- /main -->

       <?php atom()->action('after_main'); ?>

       <?php if(atom()->MenuExists('footer')): ?>
       <div class="nav nav-footer page-content">
          <?php atom()->Menu($location = 'footer', $class = 'slide-up'); ?>
       </div>
       <?php endif; ?>

       <!-- footer -->
       <div class="shadow-left page-content">
         <div class="shadow-right">

           <div id="footer">

             <?php if(($count = atom()->isAreaActive('footer1')) > 0): // make sure there are visible widgets ?>
             <ul class="blocks count-<?php echo $count; ?> clear-block">
               <?php atom()->Widgets('footer1'); ?>
             </ul>
             <?php endif; ?>

             <div id="copyright">
<div style="position:relative; top:60px"><a rel="license" href="http://creativecommons.org/licenses/by/3.0/deed.en_US"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by/3.0/88x31.png" /></a></div>


<br /><span xmlns:dct="http://purl.org/dc/terms/" property="dct:title">Think By Numbers</span> by <a xmlns:cc="http://creativecommons.org/ns#" href="http://thinkbynumbers.org/" property="cc:attributionName" rel="cc:attributionURL">Bob Butterfield</a> is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by/3.0/deed.en_US">Creative Commons Attribution 3.0 Unported License</a>.

               <?php echo do_shortcode(atom()->options('footer_content'));  ?>
               <?php wp_footer(); ?>
             
           </div>
	  </div>
         </div>
       </div>
       <!-- /footer -->

       <a class="go-top" href="#page"><?php atom()->te('Go to Top'); ?></a>

     </div>
    <!-- /page-ext -->


    <!-- <?php echo do_shortcode('[load]'); ?> -->

  </div>
  <!-- page -->

  <?php atom()->end(); ?>

<!-- Quantcast Tag -->
<script type="text/javascript">
var _qevents = _qevents || [];

(function() {
var elem = document.createElement('script');
elem.src = (document.location.protocol == "https:" ? "https://secure" : "http://edge") + ".quantserve.com/quant.js";
elem.async = true;
elem.type = "text/javascript";
var scpt = document.getElementsByTagName('script')[0];
scpt.parentNode.insertBefore(elem, scpt);
})();

_qevents.push({
qacct:"p-3dPN3jSyfC0fn"
});
</script>

<noscript>
<div style="display:none;">
<img src="//pixel.quantserve.com/pixel/p-3dPN3jSyfC0fn.gif" border="0" height="1" width="1" alt="Quantcast"/>
</div>
</noscript>
<!-- End Quantcast tag -->

<script type="text/javascript">
  var _sf_async_config = { uid: 41063, domain: 'thinkbynumbers.org' };
  (function() {
    function loadChartbeat() {
      window._sf_endpt = (new Date()).getTime();
      var e = document.createElement('script');
      e.setAttribute('language', 'javascript');
      e.setAttribute('type', 'text/javascript');
      e.setAttribute('src',
        (("https:" == document.location.protocol) ? "https://a248.e.akamai.net/chartbeat.download.akamai.com/102508/" : "http://static.chartbeat.com/") +
        "js/chartbeat.js");
      document.body.appendChild(e);
    };
    var oldonload = window.onload;
    window.onload = (typeof window.onload != 'function') ?
      loadChartbeat : function() { oldonload(); loadChartbeat(); };
  })();
</script>

</body>
</html>