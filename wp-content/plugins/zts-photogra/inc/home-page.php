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

