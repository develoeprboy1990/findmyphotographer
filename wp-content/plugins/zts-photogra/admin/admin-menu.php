<?php

 add_action( 'admin_menu',  'zts_admin_menu' );

 function zts_admin_menu( ) {

    add_menu_page(

        'Business Settings',

        'Business Settings',

        'manage_options',

        'zts-admin-menu',

        'zts_admin_menu_callback',

        'dashicons-rest-api',

        20

    );



     // Add Submenu page

    add_submenu_page(

        'zts-admin-menu', // Parent slug (should match the parent menu page's slug)

        'Subscription Users', // Page title

        'Subscription Users', // Menu title

        'manage_options', // Capability required

        'zts-subscription-users', // Menu slug

        'zts_subscription_user_callback' // Callback function to display the submenu page content

    );















}



















function zts_admin_menu_callback( ) {























    if (isset($_POST['save'])) {















        $get_settings = array(















            'zts_gallery_size'      => $_POST['zts_gallery_size'],















            'zts_location_size'  => $_POST['zts_location_size'],







        );















        update_option( 'mma_settings', $get_settings );















    }































    $get_setting =  get_option( 'mma_settings' );







    $zts_gallery_size      = !empty($get_setting['zts_gallery_size']) ? $get_setting['zts_gallery_size'] : '';







    $zts_location_size  = !empty($get_setting['zts_location_size']) ? $get_setting['zts_location_size'] : '';































    ?>







    <style>







        .mma_input{







    width: 40%;







}







.mma_save_btn{







    padding: 4px 18px;







    background-color: #2271b1;







    color: white;







    border: none;







    border-radius: 3px;







    margin-top: 10px;







}







.mma_selected_option{







    background-color: #2271b1;







    color: white;







}







    </style>















    <div class="dvsl_custimization_settings wrap">















        <h1>















            <?php _e('Business Settings Page') ?>















        </h1>















        <hr>















        <form method="POST"  >















            <table class="form-table">















                <tbody>















                    <tr>















                        <th>















                            <label >Gallery Upload Limit</label>















                        </th>















                        <td>















                            <input class="mma_input"  name="zts_gallery_size"  type="text" value="<?php echo $zts_gallery_size; ?>" > 















                        </td>















                    </tr>















                    <tr >















                        <th >















                            <label >Locations Service Limit</label>















                        </th>















                        <td >















                            <input class="mma_input"  name="zts_location_size"  type="text" value="<?php echo $zts_location_size; ?>"  >















                        </td>















                    </tr>















                    </tbody>















                </table>















                <input type="submit" name="save" value="save" class="mma_save_btn">















        </form>















    </div>















    <?php















}







function zts_subscription_user_callback(){
$categories = get_terms(array(
    'taxonomy' => 'product_cat',
    'hide_empty' => false,
    'exclude' => array(get_option('default_product_cat')),
));

echo "<pre>";
foreach ($categories as $category) {
    // Get the category image URL
    $image_id = get_woocommerce_term_meta($category->term_id, 'thumbnail_id', true);
    $image_url = wp_get_attachment_image_url($image_id, 'full');
    
    // Print category details
    echo "Category Name: " . $category->name . "<br>";
    echo "Category Slug: " . $category->slug . "<br>";
    echo "Category Image URL: " . $image_url . "<br><br>";
}
echo "</pre>";


exit;















    exit;



    $user_id = 1;
    global $wpdb;
    $table_name = $wpdb->prefix . 'zts_user_data';
    $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %s", $user_id);
    $row = $wpdb->get_row($sql, ARRAY_A);
    $g_images = unserialize($row['gallery_images']);

    // Store the image URLs in an array
    $image_urls = array();
    foreach ($g_images as $key => $value) {
        $image_urls[] = get_the_guid($value);
    }

    if (isset($_POST['zts_edit_gallery'])) {
        // Get the uploaded images
        $uploadedImages = $_FILES['photos'];
        $old = $_POST['old'];


        foreach ($old as $index => $url) {
            $isValid = isImageValid($url);
            
            if ($isValid) {
                echo "Image at index $index is valid." . PHP_EOL;
            } else {
                echo "Image at index $index is NOT valid." . PHP_EOL;
            }
        }
        echo "<pre>";
        print_r($uploadedImages);
        echo 'These are old';
        print_r($old);
        echo "</pre>";
    }

    function isImageValid($url) {
        // Check if the URL is empty or not a string
        if (empty($url) || !is_string($url)) {
            return false;
        }
        
        // Check if the URL starts with "http" or "https"
        if (!preg_match('/^https?:\/\//', $url)) {
            return false;
        }
        
        // Use getimagesize() to verify the image
        $imageInfo = @getimagesize($url);
        
        // Check if getimagesize() returned false or an empty array
        if (!$imageInfo || empty($imageInfo)) {
            return false;
        }
        
        // Valid image URL
        return true;
    }


?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gallery</title>
    <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link type="text/css" rel="stylesheet" href="http://localhost/gallery/image-uploader.min.css">
</head>
<body>
    <form method="POST" name="form-example-2" id="form-example-2" enctype="multipart/form-data">
        <div class="input-field">
            <label class="active">Photos</label>
            <div class="input-images-2" style="padding-top: .5rem;"></div>
        </div>
        <button name="zts_edit_gallery">Submit and display data</button>
    </form>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="http://localhost/gallery/image-uploader.min.js"></script>
    <script type="text/javascript">
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
</body>
</html>
<?php
exit;














    global $wpdb;

    $table_name = $wpdb->prefix . 'zts_user_data';



    // Retrieve all data from the table

    $data = $wpdb->get_results("SELECT * FROM $table_name");

    ?>

    <style type="text/css">



        table {

            border-collapse: collapse;

            width: 100%;

        }



        th, td {

            border: 1px solid #ddd;

            padding: 8px;

        }



        th {

            background-color: #f2f2f2;

            font-weight: bold;

        }



        tr:nth-child(even) {

            background-color: #f9f9f9;

        }



        tr:hover {

            background-color: #e9e9e9;

        }





    </style>

    <?php



    // Check if any rows were returned

    if ($wpdb->num_rows > 0) {

        // Start creating the table

        echo '<table>';

        echo '<thead>';

        echo '<tr>';

        echo '<th>User ID</th>';

        echo '<th>Customer Plan</th>';

        echo '<th>Company Name</th>';

        echo '<th>Company URL</th>';

        echo '<th>Profile Image</th>';

        echo '<th>Status</th>';

        echo '</tr>';

        echo '</thead>';

        echo '<tbody>';



        // Loop through the data and display each row

        foreach ($data as $row) {

            echo '<tr>';

            echo '<td>' . $row->user_id . '</td>';

            echo '<td>' . $row->customer_plan . '</td>';

            echo '<td>' . $row->company_name . '</td>';

            echo '<td>' . $row->company_url . '</td>';

            echo '<td>' . $row->profile_image . '</td>';

            echo '<td>' . $row->status . '</td>';

            echo '</tr>';

        }



        echo '</tbody>';

        echo '</table>';

    } else {

        echo 'No data found.';

    }



}