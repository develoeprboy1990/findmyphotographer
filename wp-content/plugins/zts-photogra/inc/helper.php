<?php
/**
 * This file is used to process all ajax calls.
 *
 * @package bootstrap 4
 * @since 1.1.0
 */
add_action( 'wp_ajax_zts_get_child_terms', 'zts_get_child_terms' );
add_action( 'wp_ajax_nopriv_zts_get_child_terms', 'zts_get_child_terms' );

/**
 * Add ajax to store bargaining product data using user_id and create new customers
 *
 * @since 1.1.0
 */
function zts_get_child_terms() {
	$parent_term_id = $_POST['term_id'];
	$taxonomy = 'location'; // Replace 'location' with the slug of your custom taxonomy
	$child_terms = get_term_children($parent_term_id, $taxonomy);

	// Loop through the child terms
	$child_term_list = array();
	foreach ($child_terms as $child_term_id) {
		$child_term = get_term_by('id', $child_term_id, $taxonomy);
		$child_term_list[] = array(
			'term_id' => $child_term->term_id,
			'term_name' => $child_term->name,
		);
	}
	
	echo json_encode($child_term_list);
	die();
}

add_action( 'wp_ajax_zts_process_form_data_free', 'zts_process_form_data_free' );
add_action( 'wp_ajax_nopriv_zts_process_form_data_free', 'zts_process_form_data_free' );
/**
 * Process Form DATA.
 *
 * @since 1.1.0
 */
function zts_process_form_data_free() {
	$product_id = $_POST['product_id'];
	add_product_to_cart($product_id);	
	die();
}



add_action( 'wp_ajax_zts_process_form_data', 'zts_process_form_data' );
add_action( 'wp_ajax_nopriv_zts_process_form_data', 'zts_process_form_data' );
/**
 * Process Form DATA.
 *
 * @since 1.1.0
 */
function zts_process_form_data() {
	$product_id       = $_POST['product_id'];
	$profile_image    = $_FILES['profileImage'];
	$file_array       = $_FILES['multiplefileupload'];
	// Handle file uploads
	$upload_dir = wp_upload_dir(); // Get the WordPress upload directory
	$upload_path = $upload_dir['path'] . '/';
	$uploaded_files = array();
	$gallery_attachment_ids = array(); // Initialize the associative array
	$profile_img_attachment_id = '';
    
    // gallery images
	foreach ($file_array['name'] as $key => $name) {
		$file = array(
			'name'     => $file_array['name'][$key],
			'type'     => $file_array['type'][$key],
			'tmp_name' => $file_array['tmp_name'][$key],
			'error'    => $file_array['error'][$key],
			'size'     => $file_array['size'][$key]
		);

		$file_name = $file['name'];
		$file_path = $upload_path . $file_name;

		if ( move_uploaded_file( $file['tmp_name'], $file_path ) ) {
			$uploaded_files[] = $file_name;
			
			$attachment_id = wp_insert_attachment( array(
				'post_title'     => $file_name,
				'post_mime_type' => $file['type'],
				'post_status'    => 'inherit',
				'guid'           => $upload_dir['url'] . '/' . $file_name
			), $file_path );
			
			$gallery_attachment_ids[] = $attachment_id; // Store attachment ID in the associative array
		}
	}

    // for profile image.
	$profile_image_name = $profile_image['name'];
	$profile_image_path = $upload_path . $profile_image_name;
	if ( move_uploaded_file( $profile_image['tmp_name'], $profile_image_path ) ) {
		$profile_img_attachment_id = wp_insert_attachment( array(
			'post_title'     => $profile_image_name,
			'post_mime_type' => $profile_image['type'],
			'post_status'    => 'inherit',
			'guid'           => $upload_dir['url'] . '/' . $profile_image_name
		), $profile_image_path );
	}

	if ( !empty($uploaded_files) ) {
		$response = array(
			'success' => true,
			'multiplefileupload' => $gallery_attachment_ids,
			'profileImage' => $profile_img_attachment_id
		);

	

		// Send a response back to the AJAX request
		add_product_to_cart($product_id);
		wp_send_json_success($response);
	} else {
		// Send an error response back to the AJAX request
		wp_send_json_error( 'Error uploading files.' );
	}

	die();
}

function add_product_to_cart($product_id) {
    // Ensure WooCommerce is active
    if (!class_exists('WooCommerce')) {
        return;
    }
    
    // Check if the product exists
    if (!$product_id || !wc_get_product($product_id)) {
        return;
    }
    
    WC()->cart->empty_cart();
    // Add the product to the cart
    $cart_item_data = array();
    $quantity = 1;
    $variation_id = 0;
    
    WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $cart_item_data);
}
