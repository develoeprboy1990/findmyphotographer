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
                    <tr>
                        <th>
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
    global $wpdb;
    $table_name = $wpdb->prefix . 'zts_user_data';
    // Retrieve all data from the table
    $data = $wpdb->get_results("SELECT * FROM $table_name");
    ?>

   <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
   <script src="https://cdn.datatables.net/1.10.4/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.4/css/jquery.dataTables.min.css">
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
    
    <script type="text/javascript">
    $(document).ready(function () {
        $('#table_id').dataTable();
    });
</script>

    <?php

    // Check if any rows were returned
    if ($wpdb->num_rows > 0) {

        // Start creating the table
echo '<table id="table_id" >';
echo '<thead>';
echo '<tr>';
echo '<th>User ID</th>';
echo '<th>Company Name</th>';
echo '<th>Status</th>';
echo '<th>Update</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

// Loop through the data and display each row
foreach ($data as $row) {
    echo '<tr>';
    echo '<td>' . $row->user_id . '</td>';
    echo '<td>' . $row->company_name . '</td>';
    echo '<td>';
    echo '<select name="status">';
     echo '<option value="">Select Status</option>';
    echo '<option value="active"' . ($row->status === 'active' ? ' selected' : '') . '>Active</option>';
    echo '<option value="pending"' . ($row->status === 'pending' ? ' selected' : '') . '>Pending</option>';
    echo '<option value="disabled"' . ($row->status === 'disabled' ? ' selected' : '') . '>Disabled</option>';
    echo '</select>';
    echo '</td>';
    echo '<td> <button>Update</button></td>';
    echo '</tr>';
}

echo '</tbody>';
echo '</table>';

    } else {

        echo 'No data found.';

    }

}