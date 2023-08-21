<?php

if (is_admin()) {
    new Plan_Wp_List_Table();
}


/**
 * Plan_Wp_List_Table class will create the page to load the table
 */
class Plan_Wp_List_Table
{
    /**
     * Constructor will create the menu item
     */
    public function __construct()
    {
        add_action('admin_menu', array($this, 'zts_admin_menu'));
    }

    public function zts_admin_menu()
    {
        add_menu_page(
            'Listings', // Page title
            'Listings', // Menu title
            'manage_options', // Capability required
            'zts-subscription-list', // Menu slug
            array($this, 'list_table_page'), // Callback function to display the submenu page content
            'dashicons-rest-api',
            5

        );
         
         add_submenu_page(
            'zts-subscription-list', // 1752 Parent slug (should match the parent menu page's slug)
            'Free Listing', // Page title
            'Free Listing', // Menu title
            'manage_options', // Capability required 
            'zts-subscription-free-list',
            array($this, 'list_table_free_page') // Callback function to display the submenu page content

        );

        add_submenu_page(
            'zts-subscription-list', // 1752 Parent slug (should match the parent menu page's slug)
            'Premium Listing', // Page title
            'Premium Listing', // Menu title
            'manage_options', // Capability required 
            'zts-subscription-premium-list',
            array($this, 'list_table_premium_page') // Callback function to display the submenu page content
        );
        
        add_submenu_page(
            'zts-subscription-list', // Parent slug (should match the parent menu page's slug)
            'Settings', // Page title
            'Settings', // Menu title
            'manage_options', // Capability required
            'zts-admin-men', // Menu slug
            array($this, 'zts_admin_menu_callback') // Callback function to display the submenu page content

        ); 
        add_submenu_page(
            null, // Set the parent menu slug to null
            'Subscription Actions', // Page title
            'Subscription Actions', // Menu title
            'manage_options', // Required capability to access the page
            'zts-subscription-actions', // Unique menu slug
            array($this, 'handle_custom_table_actions') // Callback function to display the page content
        );
    }

    /**
     * Display the list table page
     *
     * @return Void
     */
    public function list_table_page()
    {
        $exampleListTable = new Packages_List_Table();
        // If orderby is set, use this as the sort column
        $listingType = null; 

        $exampleListTable->prepare_items($listingType);

?>
        <div class="wrap">
            <div id="icon-users" class="icon32"></div>
            <h2> All Listing</h2>
            <?php $exampleListTable->display(); ?>
        </div>
    <?php
    }


    public function list_table_free_page()
    {

        $exampleListTable = new Packages_List_Table();
        // If orderby is set, use this as the sort column
        $listingType = '=1752';
        $exampleListTable->prepare_free_items($listingType);
    ?>
        <div class="wrap">
            <div id="icon-users" class="icon32"></div>
            <h2> Free Listing</h2>
            <?php $exampleListTable->display(); ?>
        </div>
    <?php
    }


    public function list_table_premium_page()
    {

        $exampleListTable = new Packages_List_Table();
        // If orderby is set, use this as the sort column
        $listingType = '<>1752';
        $exampleListTable->prepare_items($listingType);
    ?>
        <div class="wrap">
            <div id="icon-users" class="icon32"></div>
            <h2> Premium Listing</h2>
            <?php $exampleListTable->display(); ?>
        </div>
    <?php
    }

    public function handle_custom_table_actions()
    {
        $exampleListTable = new Packages_List_Table();
        $exampleListTable->handle_custom_table_actions();
    }


    public  function zts_admin_menu_callback()
    {
        $exampleListTable = new Packages_List_Table();
        $exampleListTable->zts_admin_menu_callback();
    }

    function zts_subscription_user_callback()
    {
        $exampleListTable = new Packages_List_Table();
        $exampleListTable->zts_subscription_user_callback();
    }
}



// WP_List_Table is not loaded automatically so we need to load it in our application
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/**
 * Create a new table class that will extend the WP_List_Table
 */
class Packages_List_Table extends WP_List_Table
{
    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items($listingType = null)
    {
        $columns     = $this->get_columns();
        $hidden      = $this->get_hidden_columns();
        $sortable    = $this->get_sortable_columns();
        $data        = $this->table_data($listingType);
        usort($data, array(&$this, 'sort_data'));
        $perPage     = 20;
        $currentPage = $this->get_pagenum();
        $totalItems  = count($data);
        $this->set_pagination_args(array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ));
        $data                  = array_slice($data, (($currentPage - 1) * $perPage), $perPage);
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items           = $data;
    }


     /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_free_items($listingType = null)
    {
        $columns     = $this->get_free_columns();
        $hidden      = $this->get_hidden_columns();
        $sortable    = $this->get_sortable_columns();
        $data        = $this->table_data($listingType);
        usort($data, array(&$this, 'sort_data'));
        $perPage     = 20;
        $currentPage = $this->get_pagenum();
        $totalItems  = count($data);
        $this->set_pagination_args(array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ));
        $data                  = array_slice($data, (($currentPage - 1) * $perPage), $perPage);
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items           = $data;
    }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns()
    {
        $columns = array(
            'id'            => 'ID',
            'post_title' => 'Customer Plan',
            'company_name'  => 'Company Name',
            'company_url'   => 'Company Url',
            'phone_number'  => 'Phone Number',
            'user_email'  => 'User',
            'actions'       => 'Featured',
            'status'        => 'Actions'
        );

        return $columns;
    }



    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_free_columns()
    {
        $columns = array(
            'id'            => 'ID',
            'post_title' => 'Customer Plan',
            'company_name'  => 'Company Name',
            'company_url'   => 'Company Url',
            'phone_number'  => 'Phone Number',
            'user_email'  => 'User',
            'status'        => 'Actions'
        );

        return $columns;
    }

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns()
    {
        return array();
    }

    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns()
    {
        return array('customer_plan' => array('customer_plan', false));
    }

    /**
     * Get the table data
     *
     * @return Array
     */
    private function table_data($listingType = null)
    {
        global $wpdb;
        $where = ' ';
        $table_name = $wpdb->prefix . 'zts_user_data';
        $table_user = $wpdb->prefix . 'users';
        $table_post = $wpdb->prefix . 'posts';
        $queryText  = "SELECT zu.*,u.display_name,u.user_email,p.post_title FROM $table_name zu LEFT JOIN $table_user u ON zu.user_id=u.ID JOIN $table_post AS p ON p.ID=zu.customer_plan";
        if (!empty($listingType)) {
            $where .= "WHERE p.ID".$listingType."";
        }
        $queryText  .=  $where; 
        $query      = $wpdb->prepare($queryText);
        $results    =  $wpdb->get_results($query, ARRAY_A);
        return $results;
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'id':
            case 'post_title':
            case 'company_name':
            case 'company_url':
            case 'phone_number':
            case 'user_email':
                return $item[$column_name];
            case 'actions':
                // Display action buttons for edit and delete
             /*   $actions = sprintf(
                    ' <a href="%s">Delete</a>',
                    //esc_url(add_query_arg(['action' => 'edit', 'id' => $item['id']], admin_url('admin.php?page=zts-subscription-actions'))),
                    esc_url(add_query_arg(['action' => 'delete', 'id' => $item['id']], admin_url('admin.php?page=zts-subscription-actions')))
                );
                  $actions = sprintf('<a href="%s">Delete</a>',
                    esc_url(add_query_arg(['action' => 'delete', 'id' => $item['id']], admin_url('admin.php?page=zts-subscription-actions')))
                ); */

                $types = ['yes', 'no'];
                $current_type = 'no';
                $dropdown       = sprintf(
                    '<select name="status[%d]"><option value="">- Select -</option>',
                    $item['id']
                );
                foreach ($types as $type) {
                    $selected = ($current_type === $type) ? 'selected="selected"' : '';
                    $dropdown .= sprintf(
                        '<option value="%s" %s>%s</option>',
                        esc_attr($type),
                        $selected,
                        ucfirst($type)
                    );
                }
                $dropdown .= '</select>';
                return $dropdown;


            case 'status';
            // Display the status change dropdown
            $statuses = ['active', 'pending', 'disabled'];
            $current_status = $item['status'];
            $dropdown = sprintf(
                '<select name="status[%d]"><option value="">- Select -</option>',
                $item['id']
            );
            foreach ($statuses as $status) {
                $selected = ($current_status === $status) ? 'selected="selected"' : '';
                $dropdown .= sprintf(
                    '<option value="%s" %s>%s</option>',
                    esc_attr($status),
                    $selected,
                    ucfirst($status)
                );
            }
            $dropdown .= '</select>';
            return $dropdown;
                
            default:
                return print_r($item, true);
        }
    }

    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @return Mixed
     */
    private function sort_data($a, $b)
    {
        // Set defaults
        $orderby = 'customer_plan';
        $order = 'asc';

        // If orderby is set, use this as the sort column
        if (!empty($_GET['orderby'])) {
            $orderby = $_GET['orderby'];
        }

        // If order is set use this as the order
        if (!empty($_GET['order'])) {
            $order = $_GET['order'];
        }


        $result = strcmp($a[$orderby], $b[$orderby]);

        if ($order === 'asc') {
            return $result;
        }

        return -$result;
    }


    public function handle_custom_table_actions()
    {
        $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

        if ($action === 'edit') {
            // Handle the edit action
            $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
            // Your code to handle the edit action
        } elseif ($action === 'delete') {
            // Handle the delete action
            $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
            echo 'Id to be deleted =' . $id;
            // Your code to handle the delete action
        }
    }
    /* ***************************************************************** */


    function zts_admin_menu_callback()
    {
        if (isset($_POST['save'])) {
            $get_settings = array(
                'zts_gallery_size'      => $_POST['zts_gallery_size'],
                'zts_location_size'  => $_POST['zts_location_size'],
            );
            update_option('mma_settings', $get_settings);
        }

        $get_setting =  get_option('mma_settings');
        $zts_gallery_size      = !empty($get_setting['zts_gallery_size']) ? $get_setting['zts_gallery_size'] : '';
        $zts_location_size  = !empty($get_setting['zts_location_size']) ? $get_setting['zts_location_size'] : '';
    ?>
        <style>
            .mma_input {
                width: 40%;
            }

            .mma_save_btn {
                padding: 4px 18px;
                background-color: #2271b1;
                color: white;
                border: none;
                border-radius: 3px;
                margin-top: 10px;
            }

            .mma_selected_option {
                background-color: #2271b1;
                color: white;
            }
        </style>

        <div class="dvsl_custimization_settings wrap">
            <h1>
                <?php _e('Business Settings Page') ?>
            </h1>
            <hr>
            <form method="POST">
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th>
                                <label>Gallery Upload Limit</label>
                            </th>
                            <td>
                                <input class="mma_input" name="zts_gallery_size" type="text" value="<?php echo $zts_gallery_size; ?>">
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label>Locations Service Limit</label>
                            </th>
                            <td>
                                <input class="mma_input" name="zts_location_size" type="text" value="<?php echo $zts_location_size; ?>">
                            </td>
                        </tr>
                    </tbody>
                </table>
                <input type="submit" name="save" value="save" class="mma_save_btn">
            </form>
        </div>
    <?php
    }







    function zts_subscription_user_callback()
    {
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

            th,
            td {
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
            $(document).ready(function() {
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


        /* Shah ge enhasments */
    }
}
