

<?php



// Add a new tab to My Account page

function zts_add_custom_tab_to_my_account( $tabs ) {

  $tabs['zts-tab'] = 'Find Photographer';

  return $tabs;

}

add_filter( 'woocommerce_account_menu_items', 'zts_add_custom_tab_to_my_account' );



// Create content for the new tab

function custom_tab_content() {

  if (is_user_logged_in()) {

    $user_id = get_current_user_id(); // Get the current user's ID

    ?>

    <h3>Go To Profile Page</h3>

    <a target="blank" href="<?php echo get_site_url() . '/profile-page/?user_id=' . $user_id; ?>">Profile</a>

    <?php

  } else {

    echo 'You are not allowed to access this page.';

  }

}

add_action( 'woocommerce_account_zts-tab_endpoint', 'custom_tab_content' );



// Register a new rewrite endpoint for the tab

function add_custom_tab_endpoint() {

  add_rewrite_endpoint( 'zts-tab', EP_PAGES );

}

add_action( 'init', 'add_custom_tab_endpoint' );



// Flush rewrite rules when activating the plugin or theme

function flush_rewrite_rules_on_activation() {

  add_custom_tab_endpoint();

  flush_rewrite_rules();

}

register_activation_hook( __FILE__, 'flush_rewrite_rules_on_activation' );



// Fix 404 error on custom tab

function custom_tab_query_vars( $vars ) {

  $vars[] = 'zts-tab';

  return $vars;

}

add_filter( 'woocommerce_get_query_vars', 'custom_tab_query_vars', 10, 1 );



