<!DOCTYPE html>
<html lang="en">
<?php

get_header();

?>


<body>

    <?php
    // require_once(dirname(__FILE__) . '/LayoutManager.plain.php');
    require_once(dirname(__FILE__) . '/public.php');
    
    
    echo "<div class='phmm-legacy-default'>";
    echo do_shortcode(codeneric\phmm\base\frontend\Main::the_content_hook(""));
    echo "</div>"; 



//    if(!isset($_GET['project'])) {
//        LayoutManager::getOpeningTags();
//        include_once(dirname(__FILE__).'/partials/client-overview.php');
//        LayoutManager::getClosingTags();
//        get_footer();
//    }
//        //include_once('client-overview.php');
////    if(isset($_GET['project']) && !$preview) {
//    if(isset($_GET['project'])) {
//        LayoutManager::getOpeningTags();
//        include_once(dirname(__FILE__).'/partials/project-overview.php');
//        LayoutManager::getClosingTags();
//        get_footer();
//    }

     ?>
<?php get_sidebar(); ?>
<?php get_footer(); ?> 
</body>



</html>
