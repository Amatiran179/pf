<?php
/**
 * Comments Template
 * 
 * @package PutraFiber
 */

if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area">
    <?php if (have_comments()): ?>
        <h3 class="comments-title">
            <?php
            $comment_count = get_comments_number();
            printf(
                esc_html(_nx('%1$s Comment', '%1$s Comments', $comment_count, 'comments title', 'putrafiber')),
                number_format_i18n($comment_count)
            );
            ?>
        </h3>

        <ol class="comment-list">
            <?php
            wp_list_comments(array(
                'style'       => 'ol',
                'short_ping'  => true,
                'avatar_size' => 60,
            ));
            ?>
        </ol>

        <?php
        the_comments_navigation();
        
        if (!comments_open()):
        ?>
            <p class="no-comments"><?php esc_html_e('Comments are closed.', 'putrafiber'); ?></p>
        <?php
        endif;
    endif;

    comment_form(array(
        'title_reply_before' => '<h3 id="reply-title" class="comment-reply-title">',
        'title_reply_after'  => '</h3>',
        'class_submit'       => 'btn btn-primary',
    ));
    ?>
</div>
