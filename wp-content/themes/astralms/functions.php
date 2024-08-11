<?php

// Ourse CPT
function create_course_post_type()
{
    register_post_type(
        'course', array(
        'labels' => array(
          'name' => 'Courses',
          'singular_name' => 'Course',
          'add_new' => 'Add New Course',
          'add_new_item' => 'Add New Course',
          'edit_item' => 'Edit Course',
          'new_item' => 'New Course',
          'view_item' => 'View Course',
          'search_items' => 'Search Courses',
          'not_found' => 'No courses found',
          'not_found_in_trash' => 'No courses found in Trash',
          'parent_item_colon' => '',
          'menu_name' => 'Courses'
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
        )
    );
}
add_action('init', 'create_course_post_type');

// Module CPT
function create_module_post_type()
{
    register_post_type(
        'module', array(
        'labels' => array(
          'name' => 'Modules',
          'singular_name' => 'Module',
          'add_new' => 'Add New Module',
          'add_new_item' => 'Add New Module',
          'edit_item' => 'Edit Module',
          'new_item' => 'New Module',
          'view_item' => 'View Module',
          'search_items' => 'Search Modules',
          'not_found' => 'No modules found',
          'not_found_in_trash' => 'No modules found in Trash',
          'parent_item_colon' => '',
          'menu_name' => 'Modules'
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'custom-fields'),
        'hierarchical' => true, // Make modules hierarchical
        )
    );
}
add_action('init', 'create_module_post_type');

// Roles
function create_custom_roles()
{
    add_role(
        'student', 'Student', array(
        'read' => true,
        'view_course' => true, // Ability to view course details
        'access_enrolled_modules' => true // Ability to access enrolled modules
        )
    );

    add_role(
        'instructor', 'Instructor', array(
        'edit_posts',
        'delete_posts',
        'publish_posts',
        // Add other capabilities as needed
        )
    );
}
add_action('init', 'create_custom_roles');

// Enroll PT
function create_course_enrollment_post_type()
{
    register_post_type(
        'course_enrollment', array(
        'labels' => array(
        'name' => 'Course Enrollments',
        'singular_name' => 'Course Enrollment',
        ),
        'public' => false,
        'show_ui' => false,
        'capability_type' => 'course_enrollment',
        'map_meta_cap' => true,
        'supports' => array('title') // Just for internal reference
        )
    );
}
add_action('init', 'create_course_enrollment_post_type');

// Enroll user
function enroll_user_in_course( $order_id, $product_id )
{
    // Get user ID from order
    $order = wc_get_order($order_id);
    $user_id = $order->get_user_id();

    // Get course ID from product (assuming it's a custom field)
    $product = wc_get_product($product_id);
    $course_id = get_post_meta($product_id, '_course_id', true);

    // Create a course enrollment post
    $enrollment_post = array(
    'post_type' => 'course_enrollment',
    'post_title' => 'Enrollment for User ' . $user_id . ' and Course ' . $course_id,
    'post_status' => 'publish',
    'meta_input' => array(
      '_user_id' => $user_id,
      '_course_id' => $course_id,
    ),
    );
    wp_insert_post($enrollment_post);
}
add_action('woocommerce_order_status_completed', 'enroll_user_in_course', 10, 2);

// Check if user Erolled
function is_user_enrolled_in_course($user_id, $course_id)
{
    $args = array(
    'post_type' => 'course_enrollment',
    'meta_query' => array(
      array(
        'key' => '_user_id',
        'value' => $user_id,
      ),
      array(
        'key' => '_course_id',
        'value' => $course_id,
      ),
    ),
    );
    $query = new WP_Query($args);

    return $query->have_posts();
}

// Restrict Content
function restrict_course_content( $content )
{
    global $post;

    // Check if the user is logged in and has the 'access_enrolled_modules' capability
    if (is_user_logged_in() && current_user_can('access_enrolled_modules')) {
        // User is logged in and has the capability

        // Check if the user is enrolled in this specific course
        $user_id = get_current_user_id();
        $course_id = $post->ID; // Assuming the current post is a course or module

        if (is_user_enrolled_in_course($user_id, $course_id)) {
            return $content; // User is enrolled, display content
        } else {
            // User is not enrolled, display a message
            return '<p>You must be enrolled in this course to view this content.</p>';
        }
    } else {
        // User is not logged in or doesn't have the capability
        return '<p>You must be logged in to view this content.</p>';
    }
}
add_filter('the_content', 'restrict_course_content');
