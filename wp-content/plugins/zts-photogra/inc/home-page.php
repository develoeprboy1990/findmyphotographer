<?php







/**

 * shortcode form for search bar.

 *

 * @since    1.0

 */







function my_custom_shortcode_callback($atts)

{





    // Get the custom taxonomy terms

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

              // Add any other desired properties of the child term

          );

      }



      // Store the parent term and its child terms in the taxonomy array

      $taxonomy_array[] = array(

          'parent' => array(

              'id'   => $value->term_id,

              'name' => $value->name,

              // Add any other desired properties of the parent term

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







    $zts_site_url = get_site_url();















?>







    <div class="search-button">







        <div class="row">







            <div class="col-md-12 col-lg-12 col-xs-12">







                <div class="search-bar">







                    <div class="search-inner">







                        <form class="search-form" action="<?php echo get_site_url() . '/search-page'; ?>" method="GET" >















                            <div class="form-group inputwithicon">







                                <img src="<?php echo $zts_site_url; ?>/wp-content/uploads/2023/06/boxes.png" />







                                <div class="select">







                                    <select name="l_category" id="ct"  class="form-control-lg form-control">









                                        <?php







                                        if (!empty($categories) && !is_wp_error($categories)) {



              

                                            echo '<option value="">Select category</option>';

                                            foreach ($categories as $category) {







                                                // Access category properties







                                                echo '<option value="' . $category->name . '">' . $category->name . '</option>';

                                            }

                                        }







                                        ?>







                                    </select>







                                </div>







                            </div>







                            <div class="form-group inputwithicon">







                                <img src="<?php echo $zts_site_url; ?>/wp-content/uploads/2023/06/gps_location.png" />







                                <div class="select">















                                    <?php







                                    if (!empty($taxonomy_array) && !is_wp_error($taxonomy_array)) {
                                        echo '<select name="l_location" id="loc"  class="form-control-lg form-control">';
                                        echo '<option value="">Select location</option>';
                                        foreach ($taxonomy_array as $group) {
                                            echo "<optgroup label='" . $group['parent']['name'] . "'>";
                                            foreach ($group['children'] as $child) {
                                                echo "<option value='" . $child['name'] . "'>" . $child['name'] . "</option>";

                                            }
                                            echo "</optgroup>";
                                        }









                                        echo '</select>';

                                    }







                                    ?>







                                </div>







                            </div>







                            <button class="button btn btn-common"><i class="fa fa-search"></i> Search</button>







                        </form>







                    </div>







                </div>







            </div>







        </div>







    </div>









<?php















}







add_shortcode('ZTS_SEARCH', 'my_custom_shortcode_callback');


add_shortcode('ZTS_CAT_BLOCK', 'my_cat_block_callback');
function my_cat_block_callback(){
    ob_start();
    $categories = get_terms(array(
    'taxonomy' => 'product_cat',
    'hide_empty' => false,
    'exclude' => array(get_option('default_product_cat')),
    ));
    ?>

   
    <style type="text/css">

        .latest-categories .col-5 {
            flex: 0 0 auto;
            width: 20%;
        }

        .latest-categories > * {
            flex-shrink: 0;
            width: 100%;
            max-width: 100%;
            padding-right: calc(var(--bs-gutter-x) * .5);
            padding-left: calc(var(--bs-gutter-x) * .5);
            margin-top: var(--bs-gutter-y);
        }

        .latest-categories
        .icon-box {
         
            margin: 0;


        }

        .latest-categories .icon-box h4 {
            font-family: "Poppins", Sans-serif;
            font-size: 20px;
            font-weight: 500;
            margin-bottom: 7px;
            margin-top: 0;
            text-align: center;
            text-transform: capitalize
        }

        .latest-categories .icon-box h4 a span {
            font-weight: 700;
        }

        .latest-categories .icon-box h4 a {
            color: #0b1632;
            transition: all .4s ease-in-out;
        -moz-transition: all .4s ease-in-out;
        -webkit-transition: all .4s ease-in-out;
        -o-transition: all .4s ease-in-out;
        }

        .latest-categories .icon-box h4 a:hover{color:#18655a}




        .latest-categories .listing-btn a i {
            padding: 0;
            border: 2px solid #2b8c7f;
            color: #2b8c7f;
            font-size: 18px;
            border-radius: 100px;
            line-height: normal;
            transition: all .4s ease-in-out;
            -moz-transition: all .4s ease-in-out;
            -webkit-transition: all .4s ease-in-out;
            -o-transition: all .4s ease-in-out;
            height: 32px;
            width: 32px;
            line-height: 28px;
            text-align: center;
            margin: 0;
        }

        .latest-categories  .listing-btn a i:hover {
            color: #fff;
            background: #2b8c7f;

        }

        .latest-categories .listing-btn {
            text-align: center;
            margin-top: 10px;
        }
    </style>
    <div class="row latest-categories">
        <?php
        foreach ($categories as $category) {
            // Get the category image URL
            $image_id = get_woocommerce_term_meta($category->term_id, 'thumbnail_id', true);
            $image_url = wp_get_attachment_image_url($image_id, 'full');
            ?>
              <div class="col-5">
                <div class="icon-box">
                  <div class="icon">
                      <img src="<?php echo $image_url; ?>" class="">
                      
                  </div>
                  <h4><a href=""><?php echo $category->name; ?></a></h4>
                 
                        <div class="listing-btn">
                            <a target="blank" href="<?php echo get_site_url().'/search-page/?l_category='.$category->name; ?>"><i aria-hidden="true" class="fas fa-chevron-right"></i></a>
                </div>
                  </div>
              </div>
            
            <?php
        }
    echo '</div>';

    $content = ob_get_clean();
    return $content;
}


add_shortcode('ZTS_FEATURE_BLOCK', 'my_feature_block_callback');
function my_feature_block_callback(){
    ob_start();

    global $wpdb;
    $table_name = $wpdb->prefix . 'zts_user_data';
    // Retrieve all data from the table
    $data = $wpdb->get_results("SELECT * FROM $table_name WHERE priority = 1 LIMIT 4");

    echo "<pre>";
    print_r($data);
    echo "</pre>";
    exit;
    
    $content = ob_get_clean();
    return $content;
}


