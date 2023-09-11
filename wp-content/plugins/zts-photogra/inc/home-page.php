<?php
/**
 * shortcode form for search bar.
 *

 * @since    1.0
 */

function my_custom_shortcode_callback($atts){
    // Get the custom taxonomy terms
    $taxonomy = 'location';
    $parent_terms = get_terms(array(
      'taxonomy'     => $taxonomy,
      'hide_empty'   => false,
      'hierarchical' => true,
      'parent'       => 0, // Only retrieve parent terms
      'orderby' => 'name',
      'order' => 'ASC',
    ));

    $taxonomy_array = array();
    
    foreach ($parent_terms as $key => $value) {
      $child_term_id = $value->term_id;
      //$child_terms = get_term_children($child_term_id, $taxonomy);

      $child_terms = get_terms(array(
      'taxonomy'     => $taxonomy,
      'hide_empty'   => false,
      'hierarchical' => true,   
      'parent'       => $child_term_id, 
      'orderby' => 'name',
      'order' => 'ASC',
        ));
      //print_r($child_terms);

      // Create an array to store the child terms of the current parent term
      $child_term_array = array();
      /*foreach ($child_terms as $child_term) {
          $child_term_obj = get_term($child_term, $taxonomy);
          $child_term_array[] = array(
              'id'   => $child_term_obj->term_id,
              'name' => $child_term_obj->name,
              // Add any other desired properties of the child term
          );
      }*/
     // echo '<pre>';
      foreach ($child_terms as $key => $child_term) {
        //echo $child_term->term_id.'<bR>';
          $child_term_array[] = array(
              'id'   => $child_term->term_id,
              'name' => $child_term->name,
              // Add any other desired properties of the child term
          );
      }
      //print_r($child_term_array).'<Br><Br>';
     
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
     //print_r($taxonomy_array).'<Br><Br>';

    // get categories.
    $categories = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
        'orderby' => 'name',
        'order' => 'ASC',
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
                                    <select name="category" id="ct"  class="form-control-lg form-control">
                                        <?php
                                        if (!empty($categories) && !is_wp_error($categories)) {             
                                            echo '<option value="">Select category</option>';
                                            foreach ($categories as $category) {
                                                // Access category properties
                                                echo '<option value="' . $category->slug . '">' . $category->name . '</option>';
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
                                        echo '<select name="location" id="loc"  class="form-control-lg form-control">';
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
				box-shadow: 0px 0px 10px 0px rgba(164.99999999999997, 164.99999999999997, 164.99999999999997, 0.5);
	padding: 15px;
	border-radius: 10px;
	margin-bottom: 35px;
        }
		
		.latest-categories .icon-box .icon {
	height: 250px;
	text-align: center;
}

.latest-categories .icon-box .icon img {
	height: 100%;
	border-radius: 3px;
}

        .latest-categories .icon-box h4 {
            font-family: "Poppins", Sans-serif;
            font-size: 18px;
            font-weight: 600;
           
            margin:8px 0;
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
		
		
@media screen and (min-width: 320px) and (max-width: 768px) { 
  
.latest-categories .col-5 {
	width: 50% !important;
}
	.latest-categories .icon-box {
	margin-bottom: 20px;
	padding: 15px 8px;

}
	.latest-categories .icon-box h4 {

	font-size: 16px;
	
}
	.latest-categories .icon-box .icon {
	height: 200px;

}
}
    </style>

    <div class="row latest-categories">
        <?php
        foreach ($categories as $category) {
            // Get the category image URL
            $image_id = get_term_meta($category->term_id, 'thumbnail_id', true);
            $image_url = wp_get_attachment_image_url($image_id, 'full');
            ?>
              <div class="col-5">
                <div class="icon-box">
                  <div class="icon">
                      <img src="<?php echo $image_url; ?>" class="">
                      
                  </div>
                  <h4><a href=""><?php echo $category->name; ?></a></h4>
                 
                        <div class="listing-btn">
                            <a href="<?php echo get_site_url().'/search-page/?category='.$category->name; ?>"><i aria-hidden="true" class="fas fa-chevron-right"></i></a>
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
    $feature_products = $wpdb->get_results("SELECT * FROM $table_name WHERE priority = 1 AND expiry <> 1 AND status = 'active' LIMIT 4");


    ?>
 <style type="text/css">
        .latest-listing .col-4 {
    flex: 0 0 auto;
    width: 25%;
}

.latest-listing > * {
    flex-shrink: 0;
    width: 100%;
    max-width: 100%;
    padding-right: calc(var(--bs-gutter-x) * .5);
    padding-left: calc(var(--bs-gutter-x) * .5);
    margin-top: var(--bs-gutter-y);
}

.latest-listing .icon-box {
	box-shadow: 0px 0px 10px 0px rgba(164.99999999999997, 164.99999999999997, 164.99999999999997, 0.5);
	margin: 0;
	padding: 20px 15px 10px 15px;
	border-radius: 30px 30px 30px 30px;
	text-align: center;
}
	 
.latest-listing .icon-box .icon img {
	border-radius: 20px;
}

.latest-listing .icon-box h4 {
	font-family: "Poppins", Sans-serif;
	font-size: 18px;
	font-weight: 600;
	margin-bottom: 7px;
}

.latest-listing .icon-box h4 a {
    color: #0b1632;
    transition: all .4s ease-in-out;
-moz-transition: all .4s ease-in-out;
-webkit-transition: all .4s ease-in-out;
-o-transition: all .4s ease-in-out;
}

.latest-listing .icon-box h4 a:hover,.latest-listing .listing-location a:hover{color:#18655a}

.latest-listing .listing-location .list-icon {
vertical-align: middle
}


.latest-listing .listing-location .list-icon i {
    color: #18655a;
    font-size: 17px;
}

.latest-listing .listing-location a {
    font-family: "Poppins", Sans-serif;
    font-size: 14px;
    font-weight: 400;
    color: #000;
    vertical-align: middle;
    line-height: 1.2;
}

.latest-listing .listing-btn {
    margin: 12px 0 17px;
}

.latest-listing  .listing-btn a {
    padding: 7px 20px;
    background: #18655a;
    color: #fff;
    font-family: poppins;
    font-size: 14px;
    border-radius: 100px;
    line-height: normal;
        transition: all .4s ease-in-out;
-moz-transition: all .4s ease-in-out;
-webkit-transition: all .4s ease-in-out;
-o-transition: all .4s ease-in-out;
}

.latest-listing  .listing-btn a:hover {

    background: #14234b;

}
		
@media screen and (min-width: 320px) and (max-width: 768px) { 
	.latest-listing .col-4 {

	width: 100%;
	padding-left: 25px;
	padding-right: 25px;
}
	
	.latest-listing .icon-box {
	
	border-radius: 15px;
	margin-bottom: 20px;
}
	
	.latest-listing .listing-location {
	
	padding: 5px 0;
}
			
		}

    </style>
    <div class="row latest-listing">

        <?php
        foreach ($feature_products as $key => $value) {
            $get_profile = $value->profile_image;
            $profile_img =  get_the_guid($get_profile);
            $get_locations = unserialize($value->locations)
            
            ?>
        <div class="col-4">
            <div class="icon-box">
                <div class="icon"> 
                  <img src="<?php echo $profile_img; ?>" class=""> 
                </div>
                <h4><a href="<?php echo get_site_url() . '/profile-page/?user=' .$value->company_name; ?>"><?php echo $value->company_name; ?></a></h4>
                <div class="listing-location">
                  <span class="list-icon"><i aria-hidden="true" class="fas fa-map-marker-alt"></i></span>     
                      <?php 
                      $terms = [];
                      foreach ($get_locations as $term_id) {
                          $term = get_term_by('term_id', $term_id, 'location');
                           if ($term) {
                                $terms[] = '<a href="' . get_site_url() . '/search-page/?location=' . $term->name . '">' . $term->name . '</a>';
                            }
                      }
                      echo implode(', ', $terms);
                       ?>                  
                </div>
                    <div class="listing-btn">
                        <a href="<?php echo get_site_url() . '/profile-page/?user=' .$value->company_name; ?>">View Listing</a>
            </div>
              </div>
        </div>
            <?php
            
        }
        ?>
        </div>
    <?php


    
    $content = ob_get_clean();
    return $content;
}

add_shortcode('HOME_FIND_PHOGOGRA_BTN', 'zts_home_find_photogra');
function zts_home_find_photogra(){
    ob_start();
    if ( is_user_logged_in() ) {
        $user_id = get_current_user_id();
        global $wpdb;
        $table_name = $wpdb->prefix . 'zts_user_data';
        $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %s", $user_id);
        $row = $wpdb->get_row($sql, ARRAY_A);
        ?>
        <div class="zts_home_photogra_btn">
        <a href="<?php echo get_site_url() . '/profile-page/?user=' .$row['company_name']; ?>">MY Listing</a>
        </div>
        <?php
    } else {
        ?>
        <div class="zts_home_photogra_btn">
        <a href="<?php echo get_site_url().'/add-business'; ?>">ADD BUSINESS</a>
        </div>
        <?php
    }
    $content = ob_get_clean();
    return $content;
}