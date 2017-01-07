<?php
/*
Template Name: Memb_Dashboard
*/

if ($_GET['type'] == 'renewal'){

}
?>

<?php get_header(); ?>
<div id="content"><div class="spacer"></div>
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="post_title">
                <h1><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
            </div> <!-- post_title -->
            <div class="clear"></div>
            <div class="entry">
                <?php the_content('more...'); ?><div class="clear"></div>
                <?php udashboard(); ?>
                <div class="clear"></div>
            </div> <!-- entry -->
        </div> <!-- post -->
<?php 
        endwhile; 
    endif;
?>	
</div> <!-- content -->
<?php get_sidebar(); ?>
<!-- start footer -->
<?php get_footer();?>
<!-- end footer -->