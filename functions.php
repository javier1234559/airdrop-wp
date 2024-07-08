<?php
add_filter('use_block_editor_for_post', '__return_false');


function my_custom_fields_enable() {
    add_post_type_support('post', 'custom-fields');
    add_post_type_support('page', 'custom-fields');
}
add_action('init', 'my_custom_fields_enable');