<?php /* ATOM/digitalnature */

   // Header template, used by all pages.
   // Handles the document head, and the page header section (usually menus, logo, featured posts)

?>
<!DOCTYPE html>
<html <?php language_attributes('html'); ?>>

<head>



<meta charset="<?php bloginfo('charset'); ?>" />

<title><?php $app->DocumentTitle(); ?></title>

<?php $app->MetaDescription(); ?>
<?php $app->Favicon(); ?>

<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<!--[if lte IE 7]>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/ie.css" type="text/css" media="screen" />
<![endif]-->

<?php wp_head(); ?>

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-25325430-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>


<script type="text/javascript" src="http://www.costofwar.com/costofwar-embed.js"></script>




</head>
<body <?php body_class('no-js no-fx'); // these classes are removed with js in the "before_page" hook below ?>>

 <?php $app->action('before_page'); ?>

 <?php if(has_nav_menu($location = 'top')): ?>
 <div class="nav nav-top">
   <div class="page-content">
     <?php $app->Menu($location = 'top', $classes = 'slide-down'); ?>
   </div>
 </div>
 <?php endif; ?>

 <!-- page -->
 <div id="page">

    <?php $app->action('top'); ?>

    <div id="page-ext">

    <!-- header -->
    <div id="header">
      <div class="page-content">
        <div id="site-title" class="clear-block">
          <?php $app->Logo(); ?>
           <?php if(get_bloginfo('description')): ?><div class="headline"><?php bloginfo('description'); ?></div><?php endif; ?>
        </div>
      </div>

       <div class="shadow-left page-content">
        <div class="shadow-right nav nav-main" role="navigation">
          <?php $app->Menu($location = 'primary', $classes = 'slide-down', $fallback = 'pageMenu'); ?>
        </div>
        <!--<?php $app->SocialMediaLinks($classes = 'fadeThis', $nudge_direction = 'top', $nudge_amount = 10); ?>-->
      </div>

    </div>
    <!-- /header -->

<center>
<?php easyrotator_display_rotator('erc_79_1384664746'); ?>
</center>

 <!-- gallery 
<div text-align:center>
<?php
if (function_exists("nggSlideshowWidget"))
{ nggSlideshowWidget(3,960,320); }
?>
<?php echo do_shortcode('[nggallery id=3 w=960 h=320]'); ?>
 
</div>
   /gallery -->
    <?php $app->action('before_main'); ?>

    <!-- main -->
    <div id="main" class="page-content">
      <div id="main-ext" class="clear-block">