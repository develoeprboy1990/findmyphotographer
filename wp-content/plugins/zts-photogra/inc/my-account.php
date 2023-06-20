<?php







// Add a new tab to My Account page



function zts_add_custom_tab_to_my_account($tabs)
{



    $tabs['zts-tab'] = 'Find Photographer';



    return $tabs;
}



add_filter('woocommerce_account_menu_items', 'zts_add_custom_tab_to_my_account');







// Create content for the new tab
function custom_tab_content()
{
if (is_user_logged_in()) {

    $user_id = get_current_user_id(); 


    // Get locations.
    $taxonomy = 'location';
    $taxonomy_array = get_terms(array(
      'taxonomy'     => $taxonomy,
      'hide_empty'   => false,
      'hierarchical' => true,
    ));



    // get categories.
    $categories = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
        'exclude' => array(get_option('default_product_cat')),
    ));


    global $wpdb;
    $table_name = $wpdb->prefix . 'zts_user_data';
  
    if (isset($_POST['zts_edit_gallery'])) {

        if($_POST['zts_priority'] == 1){
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
            }else{
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
                    if ( move_uploaded_file( $file['tmp_name'], $file_path ) ) {
                        $uploaded_files[] = $file_name;
                        $attachment_id = wp_insert_attachment( array(
                            'post_title'     => $file_name,
                            'post_mime_type' => $file['type'],
                            'post_status'    => 'inherit',
                            'guid'           => $upload_dir['url'] . '/' . $file_name
                        ), $file_path );
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
                if ( move_uploaded_file( $e_profile['tmp_name'], $profile_image_path ) ) {
                    $send_profile = wp_insert_attachment( array(
                        'post_title'     => $profile_image_name,
                        'post_mime_type' => $e_profile['type'],
                        'post_status'    => 'inherit',
                        'guid'           => $upload_dir['url'] . '/' . $profile_image_name
                    ), $profile_image_path );

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

        }else{
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

        }
   


    }
    ?>

    <h3>Update Profile</h3>
    <a target="blank" href="<?php echo get_site_url() . '/profile-page/?user_id=' . $user_id; ?>">Profile</a>
    <?php
        $user_id = 1;
        if (!empty($user_id)) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'zts_user_data';
            $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %s", $user_id);
            $row = $wpdb->get_row($sql, ARRAY_A);
            if ($row) {
                $image_urls = unserialize($row['gallery_images']);
                $priority = $row['priority'];
                $profile_attachment_id = $row['profile_image'];
                $e_categories = unserialize($row['categories']);
                $e_locations = unserialize($row['locations']);
                $profile_img =  get_the_guid($profile_attachment_id); 
            }
            ?>
            <!-- bootstrap -->
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

      
            <!-- gallery css-->
            <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
            <link type="text/css" rel="stylesheet" href="http://localhost/gallery/image-uploader.min.css">
            <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
            <script type="text/javascript" src="http://localhost/gallery/image-uploader.min.js"></script>
            <style type="text/css">
                #form-example-2{
                    margin-bottom: 200px;
                }
            </style>
            <form method="POST" name="form-example-2" id="form-example-2" enctype="multipart/form-data">
                <div class="row mt-5 ">
                    <div class="col-md-12">
                        <label >Company Name <span>*</span></label>
                        <input type="text" name="company_name" class="form-control" 
                        value="<?php echo !empty($row['company_name']) ? $row['company_name'] : ''; ?>">          
                    </div>

                    <div class="col-md-6 mt-3">
                      <label >Company URL <span>*</span></label>
                      <input type="text" name="company_url" class="form-control"
                      value="<?php echo !empty($row['company_url']) ? $row['company_url'] : ''; ?>">
                    </div>
                    <?php
                    if($priority == 1){
                    ?>

                    <div class="col-md-6 mt-3">
                        <label >Phone No <span>*</span></label>
                        <input type="text" name="phone_number" class="form-control"
                        value="<?php echo !empty($row['phone_number']) ? $row['phone_number'] : ''; ?>">
                    </div>

                    <div class="col-md-6 mt-3">
                        <label >Upload Profile <span>*</span></label>
                        <input  type="file" name="e_comp_profile">
                        <input type="hidden" name="e_comp_profile_h" value="<?php echo !empty($row['profile_image']) ? $row['profile_image'] : ''; ?>">
                    </div>

                    <div class="col-md-6 mt-3">
                        <img src="<?php echo $profile_img; ?>" width="100">
                    </div>


                    <div class="col-md-12 mt-3">
                        <div>
                            <label class="active">Gallery Images<span>*</span></label>
                            <div class="input-images-2" style="padding-top: .5rem;"></div>
                        </div>
                    </div>
                    <?php } ?>

                    <div class="col-md-6 mt-3"> 
                        <label class="active">Select Categories<span>*</span></label>
                       <select name="l_category[]" class="zts_e_cat_select2 form-control"  multiple="multiple">
                            <?php
                            if (!empty($categories) && !is_wp_error($categories)) {
                                echo '<option value="">Select category</option>';
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

                    <div class="col-md-6 mt-3"> 
                        <label class="active">Select Locations<span>*</span></label>
                       <select name="l_location[]" class="zts_e_loc_select2 form-control"  multiple="multiple">
                            <?php
                            if (!empty($taxonomy_array) && !is_wp_error($taxonomy_array)) {
                                echo '<option value="">Select category</option>';
                                foreach ($taxonomy_array as $l_texonomy) {
                                    $selected = '';
                                    if (in_array($l_texonomy->term_id, $e_locations)) {
                                        $selected = 'selected';
                                    }
                                    echo '<option value="' . $l_texonomy->term_id . '" ' . $selected . '>' . $l_texonomy->name . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <input type="hidden" name="zts_priority" value="<?php echo $priority?> " class="zts_priority">

                <div class="col-md-12 mt-3">
                    <button name="zts_edit_gallery">Update</button>
                </div>
         
            </form>
                  <!-- select 2 -->
            <link href="<?php echo  get_site_url().'/wp-content/plugins/zts-photogra/assets/css/select2.min.css'; ?>" rel="stylesheet"  crossorigin="anonymous">
            <script src="<?php echo  get_site_url().'/wp-content/plugins/zts-photogra/assets/js/select2.min.js'; ?>" crossorigin="anonymous"></script>
            <script type="text/javascript">
            var priority = $('.zts_priority').val();
            if(priority == 1){
                $('.zts_e_cat_select2').select2({
                   theme: "classic"
                });
                $('.zts_e_loc_select2').select2({
                    maximumSelectionLength: 3,
                    theme: "classic"
                });
            }else{
                $('.zts_e_cat_select2').select2({
                   maximumSelectionLength: 3,
                   theme: "classic"
                });
                $('.zts_e_loc_select2').select2({
                    maximumSelectionLength: 1,
                    theme: "classic"
                });
            }
    
   
                let preloaded = [
                    <?php foreach ($image_urls as $url) { ?>
                        { id: '<?php echo $url; ?>', src: '<?php echo $url; ?>' },
                    <?php } ?>
                ];

                $('.input-images-2').imageUploader({
                    preloaded: preloaded,
                    maxFiles: 10,
                    imagesInputName: 'photos',
                    preloadedInputName: 'old'
                });
            </script>
            <?php
        }
        exit;
    } else {



        echo 'You are not allowed to access this page.';
    }
}



add_action('woocommerce_account_zts-tab_endpoint', 'custom_tab_content');







// Register a new rewrite endpoint for the tab



function add_custom_tab_endpoint()
{



    add_rewrite_endpoint('zts-tab', EP_PAGES);
}



add_action('init', 'add_custom_tab_endpoint');







// Flush rewrite rules when activating the plugin or theme



function flush_rewrite_rules_on_activation()
{



    add_custom_tab_endpoint();



    flush_rewrite_rules();
}



register_activation_hook(__FILE__, 'flush_rewrite_rules_on_activation');







// Fix 404 error on custom tab



function custom_tab_query_vars($vars)
{



    $vars[] = 'zts-tab';



    return $vars;
}



add_filter('woocommerce_get_query_vars', 'custom_tab_query_vars', 10, 1);
