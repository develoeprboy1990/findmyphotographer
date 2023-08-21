<?php
function zts_add_custom_tab_to_my_account($tabs)
{
    $row = getPackageData();
    // Add your custom tab
    $tabs = array_reverse($tabs);
    if ($row['priority'] == 1 || $row['priority'] == 2) {
        $tabs['zts-faq'] = 'Add FAQ';
    }
    $tabs['zts-tab'] = 'My Listing';
    $tabs = array_reverse($tabs);
    // Hide all other tabs except the My Listing tab
    $hidden_tabs = array(
        'dashboard',
        'orders',
        'downloads',
        'edit-address',
        'payment-methods',
    );
    foreach ($hidden_tabs as $tab) {
        if (isset($tabs[$tab])) {
            unset($tabs[$tab]);
        }
    }
    return $tabs;
}
add_filter('woocommerce_account_menu_items', 'zts_add_custom_tab_to_my_account');
function getPackageData()
{
    global $wpdb;
    $user_id = get_current_user_id();
    $table_name = $wpdb->prefix . 'zts_user_data';
    $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %s Order by id DESC", $user_id);
    $row = $wpdb->get_row($sql, ARRAY_A);
    return $row;
}
// Create content for the new tab
function zts_listing_tab_content()
{
    if (is_user_logged_in()) {
        wp_enqueue_style('zts_gallery_font_awsome'); ?>
        <!-- bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

        <!-- gallery css-->
        <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link type="text/css" rel="stylesheet" href="<?php echo  get_site_url() . '/wp-content/plugins/zts-photogra/assets/edit-gallery/image-uploader.css' ?>">
        <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
        <script type="text/javascript" src="<?php echo  get_site_url() . '/wp-content/plugins/zts-photogra/assets/edit-gallery/image-uploader.js' ?>"></script>
        <style type="text/css">
            #form-example-2 {
                margin-bottom: 200px;
            }
        </style>
        <?php
        if (isset($_POST['zts_renew_sub'])) {
            $product_id = $_POST['plan_id'];
            WC()->cart->add_to_cart($product_id);
            echo '<script>window.location.href = "' . esc_url(wc_get_checkout_url()) . '";</script>';
            exit;
        }
        if (isset($_POST['zts_upgrade_sub'])) {
            $edit_url = get_site_url() . '/add-business/?type=upgrade';
            echo '<script>window.location.href = "' . $edit_url . '";</script>';
            exit;
        }
        global $wpdb;
        $table_name = $wpdb->prefix . 'zts_user_data';
        $row = getPackageData();
        $user_id = $row['user_id'];
        if (!empty($row)) {
            $edit_url = get_site_url() . '/my-account/zts-tab/?edit=' . $row['company_name'];

            // Get locations.
            $taxonomy = 'location';
            $parent_terms = get_terms(array(
                'taxonomy'     => $taxonomy,
                'hide_empty'   => false,
                'hierarchical' => true,
                'parent'       => 0, // Only retrieve parent terms
            ));
            $taxonomy_array = array();
            foreach ($parent_terms as $key => $value) {
                $child_term_id = $value->term_id;
                $child_terms = get_term_children($child_term_id, $taxonomy);
                // Create an array to store the child terms of the current parent term
                $child_term_array = array();
                foreach ($child_terms as $child_term) {
                    $child_term_obj = get_term($child_term, $taxonomy);
                    $child_term_array[] = array(
                        'id'   => $child_term_obj->term_id,
                        'name' => $child_term_obj->name,
                    );
                }
                $taxonomy_array[] = array(
                    'parent' => array(
                        'id'   => $value->term_id,
                        'name' => $value->name,
                    ),
                    'children' => $child_term_array,
                );
            }
            // get categories.
            $categories = get_terms(array(
                'taxonomy' => 'product_cat',
                'hide_empty' => false,
                'exclude' => array(get_option('default_product_cat')),
            ));
            if (isset($_POST['zts_edit_gallery'])) {
                if ($_POST['zts_priority'] == '1' || $_POST['zts_priority'] == '2') {
                    // for gallery images
                    $file_array = $_FILES['photos'];
                    if ($file_array["size"][0] == 0) {
                        $old = $_POST['old'];
                        $old_images = array();
                        foreach ($old as $url) {
                            if (strpos($url, "http:") === 0 || strpos($url, "https:") === 0) {
                                $old_images[] = $url;
                            }
                        }
                        $e_gallery_images = $old_images;
                    } else {
                        $old = $_POST['old'];
                        $old_images = array();
                        foreach ($old as $url) {
                            if (strpos($url, "http:") === 0 || strpos($url, "https:") === 0) {
                                $old_images[] = $url;
                            }
                        }
                        $upload_dir = wp_upload_dir(); // Get the WordPress upload directory
                        $upload_path = $upload_dir['path'] . '/';
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
                            if (move_uploaded_file($file['tmp_name'], $file_path)) {
                                $uploaded_files[] = $file_name;
                                $attachment_id = wp_insert_attachment(array(
                                    'post_title'     => $file_name,
                                    'post_mime_type' => $file['type'],
                                    'post_status'    => 'inherit',
                                    'guid'           => $upload_dir['url'] . '/' . $file_name
                                ), $file_path);
                                $attachment_id = get_the_guid($attachment_id);
                                $gallery_new_images[] = $attachment_id; // Store attachment ID in the associative array
                            }
                        }

                        // Merge the new_images and old_images arrays
                        $e_gallery_images = array_merge($gallery_new_images, $old_images);
                    }
                    // for profile image.
                    $e_profile = $_FILES["e_comp_profile"];
                    // Check if the file is empty
                    if ($e_profile["size"] == 0) {
                        $send_profile = $_POST['e_comp_profile_h'];
                    } else {
                        $upload_dir = wp_upload_dir(); // Get the WordPress upload directory
                        $upload_path = $upload_dir['path'] . '/';
                        $profile_image_name = $e_profile['name'];
                        $profile_image_path = $upload_path . $profile_image_name;
                        if (move_uploaded_file($e_profile['tmp_name'], $profile_image_path)) {
                            $send_profile = wp_insert_attachment(array(
                                'post_title'     => $profile_image_name,
                                'post_mime_type' => $e_profile['type'],
                                'post_status'    => 'inherit',
                                'guid'           => $upload_dir['url'] . '/' . $profile_image_name

                            ), $profile_image_path);
                        }
                    }

                   // Define the data you want to update

                    $data = array(

                        'company_name' => $_POST['company_name'],

                        'company_url' => $_POST['company_url'],

                        'profile_image' => $send_profile,

                        'gallery_images' => serialize($e_gallery_images),

                        'phone_number' => $_POST['phone_number'],

                        'categories' => serialize($_POST['l_category']),

                        'locations' => serialize($_POST['l_location'])

                    );
                    // Define the WHERE condition to identify the row(s) to update
                    $where = array(
                        'user_id' => $user_id // Example condition
                    );
                    // Update the data in the table
                    $wpdb->update($table_name, $data, $where);
                    // Output JavaScript code to perform the redirect
                    echo '<script>window.location.href = "' . esc_url(get_site_url()) . '/my-account/zts-tab";</script>';
                    exit;
                } else {
                    $data = array(
                        'company_name' => $_POST['company_name'],
                        'company_url' => $_POST['company_url'],
                        'categories' => serialize($_POST['l_category']),
                        'locations' => serialize($_POST['l_location'])
                    );
                    // Define the WHERE condition to identify the row(s) to update
                    $where = array(
                        'user_id' => $user_id // Example condition
                    );
                    // Update the data in the table
                    $wpdb->update($table_name, $data, $where);
                    // Output JavaScript code to perform the redirect
                    echo '<script>window.location.href = "' . esc_url(get_site_url()) . '/my-account/zts-tab";</script>';
                    exit;
                }
            }

           $edit_id = isset($_GET['edit']) ? $_GET['edit'] : '';
            $profile_url = get_site_url() . '/profile-page/?user=' . $row['company_name'];
            $d_prifile_img = get_the_guid($row['profile_image']);
            // Get the product object
            $product = wc_get_product($row['customer_plan']);
            $product_name = $product->get_name();
            // Get the user object
            $user = get_user_by('ID', $row['user_id']);
            // Get the user login name
            $user_login = $user->user_login;
            // plan days.
            $plan_type = strtolower($product->get_description());
            $created_at = strtotime($row['created_at']); // Assuming 'created_at' is a valid date/time format
            if ($plan_type == '1 year') {
                $days = 365;
            } elseif ($plan_type == '2 years') {
                $days = 730;
            }
            $remaining_days = '';
            if ($row['priority'] == 1 || $row['priority'] == 2) {
                $now = time(); // Current timestamp
                $expiration_date = $created_at + ($days * 24 * 60 * 60);
                $remaining_days = max(0, floor(($expiration_date - $now) / (24 * 60 * 60)));
            }
            // set date format.
            $date = DateTime::createFromFormat('Y-m-d H:i:s', $row['created_at']);
            $formattedDate = $date->format('Y-m-d');
            if (empty($edit_id)) {
                if ($row['expiry'] == 1 || $remaining_days <= 5) {?>
                    <div>
                        <form method="POST" class="zts_renew_fm" style="float:right;">
                            <input type="hidden" name="plan_id" value="<?php echo $row['customer_plan'] ?>">

                            <?php if ($row['priority'] == 1 || $row['priority'] == 2) { ?><button type="submit" name="zts_renew_sub">Renew Subscription</button>
                            <?php } ?>
                        </form>
                    </div>
                    <br />
                <?php
                }
                if($row['priority'] == 3)
                { ?>
                <div>
                    <form method="POST" class="zts_renew_fm" style="float:right;">
                        <input type="hidden" name="plan_id" value="<?php echo $row['customer_plan'] ?>">
                        <button type="submit" name="zts_upgrade_sub">Upgrade To Featured</button>
                    </form>
                </div>
                <br />
                <?php }
                echo '<h3>My Listing</h3>';
                echo '<div class="table-responsive">';
                echo '<table id="table_id" >';
                echo '<thead>';
                echo '<tr>';
                echo '<th>User Name</th>';
                echo '<th>Company Name</th>';
                echo '<th>User Plan</th>';
                echo '<th>Plan Start</th>';
                if ($row['priority'] == 1 || $row['priority'] == 2) {
                    echo '<th>Remaining Days</th>';
                }
                echo '<th>Action</th>';

                if ($row['priority'] == 1 || $row['priority'] == 2) {

                    echo '<th>View</th>';
                }
                echo '</tr>';
                echo '</thead>';
                echo '<tbody style="text-align:center;">';
                echo '<tr>';
                echo '<td>' . $user_login . '</td>';
                echo '<td>' . $row['company_name'] . '</td>';
                echo '<td>' . $product_name . ' ' . $product->get_description() . '</td>';
                echo '<td>' . $formattedDate . '</td>';
                if ($row['priority'] == 1 || $row['priority'] == 2) {
                    echo '<td>'  . $remaining_days . '</td>';
                }
                echo '<td> <a target="blank" class="btn btn-primary edit-profile" href="' . $edit_url . '"><i class="fas fa-edit"></i></a></td>';
                if ($row['priority'] == 1 || $row['priority'] == 2) {
                    echo '<td> <a target="blank" class="btn btn-success view-profile" href="' . $profile_url . '">
                    <i class="fas fa-eye"></i></a></td>';
                }
                echo '</tr>';
                echo '</tbody>';
                echo '</table>';
                echo '</div>';
            } else {
                global $wpdb;
                $row = getPackageData();
                $user_id = $row['user_id'];
                if ($row) {
                    $priority = $row['priority'];
                    $image_urls = [];
                    if ($row['priority'] == 1 || $row['priority'] == 2) {
                        $image_urls = unserialize($row['gallery_images']);
                        $profile_attachment_id = $row['profile_image'];
                        $profile_img =  get_the_guid($profile_attachment_id);
                    }
                    $e_categories = unserialize($row['categories']);
                    $e_locations = unserialize($row['locations']);
                }
                ?>
                <h3 class="listing-my-account-title">Edit Listing</h3>
                <form method="POST" name="form-example-2" id="form-example-2" enctype="multipart/form-data">
                    <div class="row edit-listing-form">
                        <div class="col-md-12">
                            <label>Company Name <span>*</span></label>
                            <input type="text" name="company_name" class="form-control" value="<?php echo !empty($row['company_name']) ? $row['company_name'] : ''; ?>">
                        </div>
                        <div class="col-md-6 mt-4">
                            <label>Company URL <span>*</span></label>
                            <input type="text" name="company_url" class="form-control" value="<?php echo !empty($row['company_url']) ? $row['company_url'] : ''; ?>">

                        </div>
                        <?php
                        if ($priority == 1 || $priority == 2) {
                        ?>
                            <div class="col-md-6 mt-4">

                                <label>Phone No <span>*</span></label>

                                <input type="text" name="phone_number" class="form-control" value="<?php echo !empty($row['phone_number']) ? $row['phone_number'] : ''; ?>">

                            </div>



                            <div class="col-md-6 mt-4">

                                <label>Upload Profile <span>*</span></label>

                                <input type="file" name="e_comp_profile">

                                <input type="hidden" name="e_comp_profile_h" value="<?php echo !empty($row['profile_image']) ? $row['profile_image'] : ''; ?>">

                            </div>



                            <div class="col-md-6 mt-4">

                                <img src="<?php echo $profile_img; ?>" width="100">

                            </div>





                            <div class="col-md-12 mt-4">

                                <div>

                                    <label class="active">Gallery Images<span>*</span></label>

                                    <div class="input-images-2" style="padding-top: .5rem;"></div>

                                    <span class="edit-listing-gallery-alert">Drag and drop images or click on white space to add or remove images.</span>

                                </div>

                            </div>

                        <?php } ?>



                        <div class="col-md-6 mt-4 edit-my-listing-cat">

                            <label class="active">Select Categories<span>*</span></label>

                            <select name="l_category[]" class="zts_e_cat_select2 form-control" multiple="multiple">

                                <?php

                                if (!empty($categories) && !is_wp_error($categories)) {

                                    echo '<option disabled>Select category</option>';

                                    foreach ($categories as $category) {

                                        $selected = '';

                                        if (in_array($category->term_id, $e_categories)) {

                                            $selected = 'selected';
                                        }

                                        echo '<option value="' . $category->term_id . '" ' . $selected . '>' . $category->name . '</option>';
                                    }
                                }



                                ?>

                            </select>

                        </div>



                        <div class="col-md-6 mt-4 edit-my-listing-loc">

                            <label class="active">Select Locations<span>*</span></label>

                            <?php

                            if (!empty($taxonomy_array) && !is_wp_error($taxonomy_array)) {

                                echo '<select name="l_location[]" class="zts_e_loc_select2 form-control"  multiple="multiple">';

                                echo '<option disabled>Select location</option>';

                                foreach ($taxonomy_array as $group) {

                                    echo "<optgroup label='" . $group['parent']['name'] . "'>";

                                    foreach ($group['children'] as $child) {

                                        $selected = '';

                                        if (in_array($child['id'], $e_locations)) {

                                            $selected = 'selected';
                                        }



                                        echo "<option value='" . $child['id'] . "' " . $selected . ">" . $child['name'] . "</option>";
                                    }

                                    echo "</optgroup>";
                                }



                                echo '</select>';
                            }

                            ?>

                        </div>
                        <input type="hidden" name="zts_priority" value="<?php echo $priority ?>" class="zts_priority">

                        <div class="col-md-12 mt-5 edit-listing-update-btn text-end">

                            <button name="zts_edit_gallery" class="zts_edit_gallery">Update</button>

                        </div>

                </form>

                <!-- select 2 -->

                <link href="<?php echo  get_site_url() . '/wp-content/plugins/zts-photogra/assets/css/select2.min.css'; ?>" rel="stylesheet" crossorigin="anonymous">

                <script src="<?php echo  get_site_url() . '/wp-content/plugins/zts-photogra/assets/js/select2.min.js'; ?>" crossorigin="anonymous"></script>

                <script type="text/javascript">
                    var priority = $('.zts_priority').val();

                    if (priority == 1 || priority == 2) {

                        $('.zts_e_cat_select2').select2({

                            theme: "classic"

                        });

                        $('.zts_e_loc_select2').select2({

                            maximumSelectionLength: 3,

                            theme: "classic"

                        });



                        let preloaded = [

                            <?php foreach ($image_urls as $url) { ?>

                                {
                                    id: '<?php echo $url; ?>',
                                    src: '<?php echo $url; ?>'
                                },

                            <?php } ?>

                        ];

                        $('.input-images-2').imageUploader({

                            preloaded: preloaded,

                            maxFiles: 10,

                            imagesInputName: 'photos',

                            preloadedInputName: 'old'



                        });

                    } else {

                        $('.zts_e_cat_select2').select2({

                            maximumSelectionLength: 3,

                            theme: "classic"

                        });

                        $('.zts_e_loc_select2').select2({

                            maximumSelectionLength: 1,

                            theme: "classic"

                        });

                    }











                    let fileSelectHandler = function(e) {

                        // Prevent browser default event and stop propagation

                        prevent(e);



                        // Get the jQuery element instance

                        let $container = $(this);



                        // Change the container style

                        $container.removeClass('drag-over');



                        // Get the files

                        let files = e.target.files || e.originalEvent.dataTransfer.files;



                        // Check if the number of files exceeds the maximum limit

                        if (files.length > 10) {

                            alert('Maximum image upload limit exceeded.');

                            return;

                        }



                        // Makes the upload

                        setPreview($container, files);

                    };
                </script>

        <?php







            }
        }
    }
}
add_action('woocommerce_account_zts-tab_endpoint', 'zts_listing_tab_content');





function zts_faq_content()
{
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        if (isset($_POST['zts_edit_faq'])) {
            update_user_meta($user_id, 'being_fp', $_POST['being_fp']);
            update_user_meta($user_id, 'style_of_fp', $_POST['style_of_fp']);
            update_user_meta($user_id, 'become_fp', $_POST['become_fp']);
        }
        $being_fp = get_user_meta($user_id, 'being_fp', true);
        $style_of_fp = get_user_meta($user_id, 'style_of_fp', true);
        $become_fp = get_user_meta($user_id, 'become_fp', true);
        ?>
        <!-- bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
        </script>
        <h3 class="listing-my-account-title">Edit FAQ</h3>
        <form method="POST">
            <div class="row edit-listing-form">
                <div class="col-md-12 mt-4">
                    <div>
                        <label class="active">Why did you become a family photographer?<span>*</span></label>
                        <input type="text" name="become_fp" value="<?php echo !empty($become_fp) ? $become_fp : ''; ?>">
                    </div>
                </div>

                <div class="col-md-12 mt-4">
                    <div>
                        <label class="active">What do you like most about being a family photographer?<span>*</span></label>
                        <input type="text" name="being_fp" value="<?php echo !empty($being_fp) ? $being_fp : ''; ?>">
                    </div>
                </div>

                <div class="col-md-12 mt-4">
                    <div>
                        <label class="active">Do you specialize in a certain style of photograhy?
                            <span>*</span></label>
                        <input type="text" name="style_of_fp" value="<?php echo !empty($style_of_fp) ? $style_of_fp : ''; ?>">
                    </div>
                </div>
                <div class="col-md-12 mt-5 edit-listing-update-btn text-end">
                    <button name="zts_edit_faq" class="zts_edit_faq">Update</button>
                </div>
            </div>
        </form>
<?php
    }
}
add_action('woocommerce_account_zts-faq_endpoint', 'zts_faq_content');

// Register a new rewrite endpoint for the tab.
function add_custom_tab_endpoint()
{
    add_rewrite_endpoint('zts-tab', EP_PAGES);
    add_rewrite_endpoint('zts-faq', EP_PAGES);
}
add_action('init', 'add_custom_tab_endpoint');


// Flush rewrite rules when activating the plugin or theme.
function flush_rewrite_rules_on_activation()
{
    add_custom_tab_endpoint();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'flush_rewrite_rules_on_activation');


// Fix 404 error on custom tab.
function custom_tab_query_vars($vars)
{
    $vars[] = 'zts-tab';
    $vars[] = 'zts-faq';
    return $vars;
}

add_filter('woocommerce_get_query_vars', 'custom_tab_query_vars', 10, 1);
