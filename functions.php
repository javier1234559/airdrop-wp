<?php
add_filter('use_block_editor_for_post', '__return_false');

function my_custom_fields_enable()
{
    add_post_type_support('post', 'custom-fields');
    add_post_type_support('page', 'custom-fields');
}
add_action('init', 'my_custom_fields_enable');

// Function to display a custom airdrop post card
function airdrop_post_card_custom_shortcode($atts)
{
    global $current_social_links;

    // Extract shortcode attributes
    $atts = shortcode_atts(
        array(
            'title' => '',
            'link' => '',
            'thumbnail' => '',
            'action' => '',
            'price' => '',
            'confirmed' => ''
        ),
        $atts,
        'airdrop_post_card_custom'
    );

    // Output the post card HTML
    ob_start();
    ?>

    <div class="airdrop-card-post-custom">
        <img src="" alt="" class="top-right-icon">
        <a href="<?php echo esc_url($atts['link']); ?>">
            <div class="card-header">
                <div class="card-img">
                    <img src="<?php echo esc_url($atts['thumbnail']); ?>" alt="<?php echo esc_attr($atts['title']); ?>" />
                </div>
                <div class="card-info">
                    <h3><?php echo esc_html($atts['title']); ?></h3>
                    <p><img src="" alt="" />Action: <?php echo esc_html($atts['action']); ?></p>
                    <p><img src="" alt="" /><?php echo esc_html($atts['price']); ?>$</p>
                </div>
            </div>
        </a>
        <div class="card-footer">
            <span
                class="<?php echo $atts['confirmed'] === "NotConfirm" ? "unactive" : ""; ?>"><?php echo esc_html($atts['confirmed']); ?></span>
            <ul class="card-social-links">
                <?php
                if (!empty($current_social_links)):
                    foreach ($current_social_links as $social):
                        ?>
                        <li><a href="<?php echo esc_url($social['link']); ?>" target="_blank"><img
                                    src="/wp-content/uploads/2024/07/<?php echo esc_attr($social['icon']); ?>.png"
                                    alt="<?php echo esc_attr($social['icon']); ?> Icon"></a></li>
                        <?php
                    endforeach;
                endif;
                ?>
            </ul>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('airdrop_post_card_custom', 'airdrop_post_card_custom_shortcode');

// Function to display the latest airdrops sorted by date
function latest_airdrop_shortcode($atts)
{
    // Extract and set default attributes
    $atts = shortcode_atts(
        array(
            'posts_per_page' => 5,
        ),
        $atts
    );

    $args = array(
        'post_type' => 'post',
        'posts_per_page' => intval($atts['posts_per_page']),
        'orderby' => 'date',
        'order' => 'DESC'
    );

    $query = new WP_Query($args);

    // Check if we have posts
    if ($query->have_posts()) {
        ob_start();
        echo '<section class="latest-airdrop-section"><div class="flex-container">';

        while ($query->have_posts()) {
            $query->the_post();

            $title = get_the_title();
            $link = get_permalink();
            $thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'medium');

            // Lấy giá trị của các sub fields
            $airdrop_details = get_field('airdrop_detail');
            if ($airdrop_details) {
                $confirmed = $airdrop_details['airdrop_confirmed_or_not'];
                $price = $airdrop_details['price'];
                $action = $airdrop_details['action'];
            }

            // Set social_links to global variable
            global $current_social_links;
            $current_social_links = array(
                array('icon' => 'twitter', 'link' => 'https://twitter.com/link1'),
                array('icon' => 'facebook', 'link' => 'https://facebook.com/link1'),
                array('icon' => 'instagram', 'link' => 'https://instagram.com/link1')
            );

            // Call the airdrop_post_card_custom shortcode
            echo do_shortcode("[airdrop_post_card_custom title=\"$title\" link=\"$link\" thumbnail=\"$thumbnail\" action=\"$action\" price=\"$price\" confirmed=\"$confirmed\"]");
        }

        echo '</div></section>';
        wp_reset_postdata();
        return ob_get_clean();
    } else {
        return 'No airdrops found.';
    }
}
add_shortcode('latest_airdrop', 'latest_airdrop_shortcode');


function hottest_airdrop_shortcode()
{
    // Lấy danh sách ID của các bài viết phổ biến dựa trên số lượng "like"
    $post__in = wp_ulike_get_popular_items_ids(
        array(
            'type' => 'post',
            'rel_type' => 'post',
            'status' => 'like',
            'period' => 'all',
        )
    );

    // Tạo truy vấn mới để lấy thông tin chi tiết của các bài viết
    $args = array(
        'posts_per_page' => 10, // Số lượng bài viết muốn hiển thị
        'post__in' => $post__in,
        'post_type' => 'post', // Loại bài viết
        'orderby' => 'post__in',
        'order' => 'DESC' // Sắp xếp theo thứ tự giảm dần
    );

    $query = new WP_Query($args);

    // Kiểm tra xem có bài viết nào được trả về không
    if ($query->have_posts()) {
        ob_start(); // Bắt đầu buffering output
        echo '<ul>'; // Bắt đầu danh sách

        // Vòng lặp qua mỗi bài viết
        while ($query->have_posts()) {
            $query->the_post();
            // Hiển thị tiêu đề bài viết
            echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
        }

        echo '</ul>'; // Kết thúc danh sách
        wp_reset_postdata(); // Khởi động lại dữ liệu bài viết gốc
        return ob_get_clean(); // Trả về nội dung đã buffer
    } else {
        return 'Không có bài viết nào được tìm thấy.';
    }
}
add_shortcode('hottest_airdrop', 'hottest_airdrop_shortcode');


// Function to display posts from the 'Potential Airdrops' category
function potential_airdrops_shortcode($atts)
{
    // Extract and set default attributes
    $atts = shortcode_atts(
        array(
            'posts_per_page' => 5,
        ),
        $atts
    );

    $args = array(
        'post_type' => 'post',
        'posts_per_page' => intval($atts['posts_per_page']),
        'category_name' => 'Potential Airdrops',
        'orderby' => 'date',
        'order' => 'DESC'
    );

    $query = new WP_Query($args);

    // Check if we have posts
    if ($query->have_posts()) {
        ob_start();
        echo '<section class="potential-airdrops-section"><div class="flex-container">';

        while ($query->have_posts()) {
            $query->the_post();

            $title = get_the_title();
            $link = get_permalink();
            $thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'medium');

            // Lấy giá trị của các sub fields
            $airdrop_details = get_field('airdrop_detail');
            if ($airdrop_details) {
                $confirmed = $airdrop_details['airdrop_confirmed_or_not'];
                $price = $airdrop_details['price'];
                $action = $airdrop_details['action'];
            }

            // Set social_links to global variable
            global $current_social_links;
            $current_social_links = array(
                array('icon' => 'twitter', 'link' => 'https://twitter.com/link1'),
                array('icon' => 'facebook', 'link' => 'https://facebook.com/link1'),
                array('icon' => 'instagram', 'link' => 'https://instagram.com/link1')
            );

            // Call the airdrop_post_card_custom shortcode
            echo do_shortcode("[airdrop_post_card_custom title=\"$title\" link=\"$link\" thumbnail=\"$thumbnail\" action=\"$action\" price=\"$price\" confirmed=\"$confirmed\"]");
        }

        echo '</div></section>';
        wp_reset_postdata();
        return ob_get_clean();
    } else {
        return 'No potential airdrops found.';
    }
}
add_shortcode('potential_airdrops', 'potential_airdrops_shortcode');



// Function to get top 3 latest posts sorted by date in a specific category
function top3_category_latest_airdrop_shortcode($atts)
{
    // Extract and set default attributes
    $atts = shortcode_atts(
        array(
            'category' => 'Potential Airdrops', // Default category
        ),
        $atts
    );

    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 3,
        'category_name' => $atts['category'],
        'orderby' => 'date',
        'order' => 'DESC'
    );

    $query = new WP_Query($args);

    // Check if we have posts
    if ($query->have_posts()) {
        ob_start();
        echo '<section class="top3-category-latest-airdrops"><div class="flex-container">';

        while ($query->have_posts()) {
            $query->the_post();

            $title = get_the_title();
            $link = get_permalink();
            $thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'medium');

            echo '<div class="post-item">';
            echo '<a href="' . esc_url($link) . '">';
            echo '<img src="' . esc_url($thumbnail) . '" alt="' . esc_attr($title) . '">';
            echo '<h2>' . esc_html($title) . '</h2>';
            echo '</a>';
            echo '</div>';
        }

        echo '</div></section>';
        wp_reset_postdata();
        return ob_get_clean();
    } else {
        return 'No posts found in the specified category.';
    }
}
add_shortcode('top3_category_latest_airdrop', 'top3_category_latest_airdrop_shortcode');


// Function to list all categories
function list_all_categories_shortcode()
{
    $args = array(
        'orderby' => 'name',
        'order' => 'ASC'
    );

    $categories = get_categories($args);

    if (!empty($categories)) {
        ob_start();
        echo '<ul class="all-categories-list">';
        foreach ($categories as $category) {
            $category_link = get_category_link($category->term_id);
            echo '<li><a href="' . esc_url($category_link) . '">' . esc_html($category->name) . '</a></li>';
        }
        echo '</ul>';
        return ob_get_clean();
    } else {
        return 'No categories found.';
    }
}
add_shortcode('list_all_categories', 'list_all_categories_shortcode');


// Function to get the top 1 post with is_exclusive_airdrop field value "Is Exclusive Airdrops"
function top1_exclusive_airdrop_shortcode()
{
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 1,
        'meta_key' => 'is_exclusive_airdrop',
        'meta_value' => 'Is Exclusive Airdrops',
        'orderby' => 'date',
        'order' => 'DESC'
    );

    $query = new WP_Query($args);

    // Check if we have posts
    if ($query->have_posts()) {
        ob_start();
        echo '<section class="exclusive-airdrop-section"><div class="flex-container">';

        while ($query->have_posts()) {
            $query->the_post();

            $title = get_the_title();
            $link = get_permalink();
            $thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'medium');

            echo '<div class="post-item">';
            echo '<a href="' . esc_url($link) . '">';
            echo '<img src="' . esc_url($thumbnail) . '" alt="' . esc_attr($title) . '">';
            echo '<h2>' . esc_html($title) . '</h2>';
            echo '</a>';
            echo '</div>';
        }

        echo '</div></section>';
        wp_reset_postdata();
        return ob_get_clean();
    } else {
        return 'No exclusive airdrops found.';
    }
}
add_shortcode('top1_exclusive_airdrop', 'top1_exclusive_airdrop_shortcode');




function potential_airdrop_slider_shortcode($atts)
{
    // Extract and set default attributes
    $atts = shortcode_atts(
        array(
            'posts_per_page' => 6,
        ),
        $atts
    );

    $args = array(
        'post_type' => 'post',
        'posts_per_page' => intval($atts['posts_per_page']),
        'category_name' => 'Potential Airdrops',
        'orderby' => 'date',
        'order' => 'DESC'
    );

    $query = new WP_Query($args);

    ob_start();

    if ($query->have_posts()) {
        ?>
        <div id="potential-airdrops-swiper" class="swiper-container">
            <div class="swiper-wrapper">
                <?php
                while ($query->have_posts()) {
                    $query->the_post();

                    $title = get_the_title();
                    $link = get_permalink();
                    $thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'medium');

                    // Lấy giá trị của các sub fields
                    $airdrop_details = get_field('airdrop_detail');
                    if ($airdrop_details) {
                        $confirmed = $airdrop_details['airdrop_confirmed_or_not'];
                        $price = $airdrop_details['price'];
                        $action = $airdrop_details['action'];
                    }

                    // Set social_links to global variable
                    global $current_social_links;
                    $current_social_links = array(
                        array('icon' => 'twitter', 'link' => 'https://twitter.com/link1'),
                        array('icon' => 'facebook', 'link' => 'https://facebook.com/link1'),
                        array('icon' => 'instagram', 'link' => 'https://instagram.com/link1')
                    );

                    // Call the airdrop_post_card_custom shortcode within a swiper-slide
                    echo '<div class="swiper-slide">';
                    echo do_shortcode("[airdrop_post_card_custom title=\"$title\" link=\"$link\" thumbnail=\"$thumbnail\" action=\"$action\" price=\"$price\" confirmed=\"$confirmed\"]");
                    echo '</div>';
                }
                wp_reset_postdata();
                ?>
            </div>
            <!-- Add Pagination -->
            <div class="swiper-pagination"></div>
            <!-- Add Navigation -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
        <?php
    } else {
        echo 'No potential airdrops found.';
    }

    return ob_get_clean();
}
add_shortcode('potential_airdrop_slider', 'potential_airdrop_slider_shortcode');



function latest_airdrop_slider_shortcode($atts)
{
    // Extract and set default attributes
    $atts = shortcode_atts(
        array(
            'posts_per_page' => 6,
        ),
        $atts
    );

    $args = array(
        'post_type' => 'post',
        'posts_per_page' => intval($atts['posts_per_page']),
        'orderby' => 'date',
        'order' => 'DESC'
    );

    $query = new WP_Query($args);

    ob_start();

    if ($query->have_posts()) {
        ?>
        <div id="latest-airdrops-swiper" class="swiper-container">
            <div class="swiper-wrapper">
                <?php
                while ($query->have_posts()) {
                    $query->the_post();

                    $title = get_the_title();
                    $link = get_permalink();
                    $thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'medium');

                    // Lấy giá trị của các sub fields
                    $airdrop_details = get_field('airdrop_detail');
                    if ($airdrop_details) {
                        $confirmed = $airdrop_details['airdrop_confirmed_or_not'];
                        $price = $airdrop_details['price'];
                        $action = $airdrop_details['action'];
                    }

                    // Set social_links to global variable
                    global $current_social_links;
                    $current_social_links = array(
                        array('icon' => 'twitter', 'link' => 'https://twitter.com/link1'),
                        array('icon' => 'facebook', 'link' => 'https://facebook.com/link1'),
                        array('icon' => 'instagram', 'link' => 'https://instagram.com/link1')
                    );

                    // Call the airdrop_post_card_custom shortcode within a swiper-slide
                    echo '<div class="swiper-slide">';
                    echo do_shortcode("[airdrop_post_card_custom title=\"$title\" link=\"$link\" thumbnail=\"$thumbnail\" action=\"$action\" price=\"$price\" confirmed=\"$confirmed\"]");
                    echo '</div>';
                }
                wp_reset_postdata();
                ?>
            </div>
            <!-- Add Pagination -->
            <div class="swiper-pagination"></div>
            <!-- Add Navigation -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
        <?php
    } else {
        echo 'No latest airdrops found.';
    }

    return ob_get_clean();
}
add_shortcode('latest_airdrop_slider', 'latest_airdrop_slider_shortcode');

function hottest_airdrop_slider_shortcode($atts)
{
    $atts = shortcode_atts(
        array(
            'posts_per_page' => 10,
        ),
        $atts
    );

    // Lấy danh sách ID của các bài viết phổ biến dựa trên số lượng "like"
    $post__in = wp_ulike_get_popular_items_ids(
        array(
            'type' => 'post',
            'rel_type' => 'post',
            'status' => 'like',
            'period' => 'all',
        )
    );

    // Tạo truy vấn mới để lấy thông tin chi tiết của các bài viết
    $args = array(
        'posts_per_page' => intval($atts['posts_per_page']),
        'post__in' => $post__in,
        'post_type' => 'post',
        'orderby' => 'post__in',
        'order' => 'DESC'
    );

    $query = new WP_Query($args);

    ob_start();

    if ($query->have_posts()) {
        ?>
        <div id="hottest-airdrops-swiper" class="swiper-container">
            <div class="swiper-wrapper">
                <?php
                while ($query->have_posts()) {
                    $query->the_post();

                    $title = get_the_title();
                    $link = get_permalink();
                    $thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'medium');

                    // Lấy giá trị của các sub fields
                    $airdrop_details = get_field('airdrop_detail');
                    if ($airdrop_details) {
                        $confirmed = $airdrop_details['airdrop_confirmed_or_not'];
                        $price = $airdrop_details['price'];
                        $action = $airdrop_details['action'];
                    }

                    // Set social_links to global variable
                    global $current_social_links;
                    $current_social_links = array(
                        array('icon' => 'twitter', 'link' => 'https://twitter.com/link1'),
                        array('icon' => 'facebook', 'link' => 'https://facebook.com/link1'),
                        array('icon' => 'instagram', 'link' => 'https://instagram.com/link1')
                    );

                    // Call the airdrop_post_card_custom shortcode within a swiper-slide
                    echo '<div class="swiper-slide">';
                    echo do_shortcode("[airdrop_post_card_custom title=\"$title\" link=\"$link\" thumbnail=\"$thumbnail\" action=\"$action\" price=\"$price\" confirmed=\"$confirmed\"]");
                    echo '</div>';
                }
                wp_reset_postdata();
                ?>
            </div>
            <!-- Add Pagination -->
            <div class="swiper-pagination"></div>
            <!-- Add Navigation -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
        <?php
    } else {
        echo 'No hottest airdrops found.';
    }

    return ob_get_clean();
}
add_shortcode('hottest_airdrop_slider', 'hottest_airdrop_slider_shortcode');
?>