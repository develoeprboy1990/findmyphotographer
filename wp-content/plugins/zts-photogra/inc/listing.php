<?php



add_shortcode('ZTS_LISTING_PAGE',  'zts_listing_page');



function zts_listing_page(){



  ob_start();



  wp_enqueue_style('zts_pagination_css');



  wp_enqueue_script('zts_pagination_js');







global $wpdb;



$table_name = $wpdb->prefix . 'zts_user_data';



$category = isset($_GET['category']) ? $_GET['category'] : '';



$location = isset($_GET['location']) ? $_GET['location'] : '';



if ($category === 'Select category') {



  $category = '';



}







if ($location === 'Select location') {



  $location = '';



}



if(!empty($location)){



  $get_location = get_term_by('name', $location, 'location');



  $location = $get_location->term_id;



}



if(!empty($category)){



  $get_category = get_term_by('slug', $category, 'product_cat');



  $category = $get_category->term_id;



}























// Sanitize and validate the input category ID and location



$category_id = intval($category);



$location = sanitize_text_field($location);







// SQL query to retrieve data from the custom table



if (empty($category) && empty($location)) {



  $sql = "SELECT * FROM $table_name ORDER BY priority";



} else {



  $sql = "SELECT * FROM $table_name WHERE ";



  $whereClause = array();







  if (!empty($category) && empty($location)) {



    $whereClause[] = $wpdb->prepare("categories LIKE '%%\"%s\"%%'", $category_id);



  }







  if (empty($category) && !empty($location)) {



    $whereClause[] = $wpdb->prepare("locations LIKE '%%\"%s\"%%'", $location);



  }







  if (!empty($category) && !empty($location)) {



    $whereClause[] = $wpdb->prepare(



      "(categories LIKE '%%\"%s\"%%' AND locations LIKE '%%\"%s\"%%')",



      $category_id,



      $location



    );



  }







  $sql .= implode(' AND ', $whereClause) . " ORDER BY priority";



}







// Execute the SQL query



$results = $wpdb->get_results($sql, ARRAY_A);



















$site_url = get_site_url();



?>



  <?php



  $otherDisplayed = false;



  if(!empty($results)){



    echo '<div id="max_iecp_tbody">';



    foreach ($results as $key => $value) {

      if($value['expiry'] == 1 || $value['status'] != 'active'){

        continue;

      }







      echo '<div class="cart_item">';



    $guid = get_the_guid($value['profile_image']);



    $get_locations = unserialize($value['locations']);



    $get_categories = unserialize($value['categories']);







    if ($value['priority'] == '1' || $value['priority'] == '2') {



      if($key == 0){



        ?>



      <div class="text-center ">



        <div class="main-heading-section">



          <h2 class="premium-heading-title">Best Rated in 2023</h2>



        </div>



      </div>







        <?php



      }



      ?>



    <div class="row detail">



      <div class="col-12 col-sm-12 col-md-12 col-lg-auto p-0">



        <div class="ribbon">



          <img src="<?php echo $site_url; ?>/wp-content/uploads/2023/06/check-arrow.png" class="" width="57" height="57">



        </div>



        <div class="image-left-section">



          <img src="<?php echo $guid; ?>" alt="">



        </div>



      </div>



      <div class="col-12 col-sm-12 col-md-12 col-lg-8 p-0">



        <div class="deatils-right-content">



          <div class="detail-box">



            <h4><?php echo $value['company_name']; ?></h4>



            <p>



              <img src="<?php echo $site_url; ?>/wp-content/uploads/2023/06/list-location.png" class="" alt="" width="17" height="20">



              <?php 



              $terms = [];



              foreach ($get_locations as $term_id) {



                  $term = get_term_by('term_id', $term_id, 'location');



                   if ($term) {



                        // $terms[] = $term->name;
                        $terms[] = '<a href="' . get_site_url() . '/search-page/?location=' . $term->name . '">' . $term->name . '</a>';



                    }



              }



              echo implode(', ', $terms);



               ?>



            </p>



            <div class="eligibal" id="elig-box">



              <?php



              $cats_arr = [];



              foreach ($get_categories as $cat_term_id) {



                  $cat_term = get_term_by('term_id', $cat_term_id, 'product_cat');



                   if ($cat_term) {



                        // $cats_arr[] = $cat_term->name;
                        $cats_arr[] = '<a href="' . get_site_url() . '/search-page/?category=' . $cat_term->name . '">' . $cat_term->name . '</a>';



                    }



              }



              echo implode(', ', $cats_arr);







              ?>







              <!-- <p>He will help you make the most of your wedding day by capturing incredible moments for you.</p> -->



            </div>



            <ul>



              <li>



                <img src="<?php echo $site_url; ?>/wp-content/uploads/2023/06/8666632_phone_icon.png" class="" alt="" width="18" height="30">



                <strong>Phone Number : </strong> <?php echo $value['phone_number']; ?>



              </li>



              <li><a target="blank" href="<?php echo get_site_url() . '/profile-page/?user=' .$value['company_name']; ?>" class="elementor-button-link" role="button">View Listing</a></li>



            </ul>



          </div>



        </div>



      </div>



    </div>



      <?php







    }elseif($value['priority'] == '3'){



    



         if (!$otherDisplayed) {



      // Display the "Other in 2022" section



      ?>



      <div class="text-center">



        <div class="main-heading-section green-color">



          <h2 class="premium-heading-title">Other in 2023</h2>



        </div>



      </div>



      <?php



      $otherDisplayed = true; // Update the flag to indicate the section has been displayed



    }



  



      ?>



      <ul class="zts_free_listing">



      <li >



        <div class="media">







          <div class="media-left align-self-center">







            <img class="rounded-circle" src="<?php echo $site_url; ?>/wp-content/uploads/2023/06/camera-img.png">







          </div>







          <div class="media-body">







            <h4><?php echo $value['company_name']; ?></h4>







            <p>Photography</p>







          </div>







          <div class="media-right align-self-center">







            <a target="blank" href="<?php echo  $value['company_url']; ?>" class="btn btn-default">







              <i aria-hidden="true" class="fas fa-globe"></i>







              View Website</a>







          </div>







        </div>



      </li>



      </ul>



      <?php







    }



    echo '</div>';











  }







  }else{



    echo "No Results Found";



  }



  ?>



  <div>



        <div id="max_iecp_pagination" style="margin-top: -40px;"></div>







  <script type="text/javascript">



      jQuery(function($) {







        var items = $("#max_iecp_tbody .cart_item");







        var numItems = items.length;







        if (numItems >= 7) {







          var perPage = 7;







          // only show the first 2 (or "first per_page") items initially







          items.slice(perPage).hide();







          // now setup pagination







          $("#max_iecp_pagination").pagination({







            items: numItems,







            itemsOnPage: perPage,







            cssStyle: "light-theme",







            onPageClick: function(pageNumber) { // this is where the magic happens







              // someone changed page, lets hide/show trs appropriately







              var showFrom = perPage * (pageNumber - 1);







              var showTo = showFrom + perPage;







              items.hide() // first hide everything, then show for the new page







                .slice(showFrom, showTo).show();







            }







          });















        }















      });







    



  </script>















































































<!--  <div class="divider"> </div>















  <div class="pagination-btns">







    <ul class="pagination">







      <li class="page-item">







        <a class="page-link" href="#" aria-label="Previous">















          <span class="fas fa-angle-left"></span>







        </a>







      </li>







      <li class="page-item active"><a class="page-link" href="#">1</a></li>







      <li class="page-item"><a class="page-link" href="#">2</a></li>







      <li class="page-item"><a class="page-link" href="#">3</a></li>







      <li class="page-item">







        <a class="page-link" href="#" aria-label="Next">















          <span class="fas fa-angle-right"></span>







        </a>







      </li>







    </ul>







  </div> -->







<?php



  // Get the buffered content and end the output buffering



  $content = ob_get_clean();



  return $content;



}











add_shortcode('ZTS_LISTING_PAGE_SEARCH',  'zts_listing_page_search');



function zts_listing_page_search(){



  ob_start();



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







    $zts_l_categories = get_terms(array(



      'taxonomy' => 'product_cat',



      'hide_empty' => false,



      'exclude' => array(get_option('default_product_cat')),



    ));







    $site_url = get_site_url();



    ?>



      <style type="text/css">



        .search-button {



           padding:0px;



        }



        .search-bar .search-form {



            flex-direction: column;



            border: none;



        }







        .search-bar .form-group {



          width: 100%;



          background: #edf0fa;



          margin-bottom: 18px;



          border-radius: 100px !important;



          padding: 0 15px 0 40px;



        }







        .search-bar .form-group .select select {



          background: transparent;



        }







        .search-bar .btn-common {



          width: 100%;



          border-radius: 100px !important;



          background-color: #14234b;



        }



        



        .search-bar .btn-common:hover {



          background: #33b2a0;



        }



      </style>



      <div class="search-button">



        <div class="search-bar">



          <div class="search-inner">



            <form class="search-form" action="<?php echo get_site_url() . '/search-page'; ?>" method="GET" >



              <div class="form-group inputwithicon">



                <img src="<?php echo $site_url; ?>/wp-content/uploads/2023/06/camera.png" />



                <div class="select">



                  <select name="category" id="ct"  class="form-control-lg form-control">



                    <option value="" >Select category</option>



                      <?php



                      if (!empty($zts_l_categories) && !is_wp_error($zts_l_categories)) {



                        foreach ($zts_l_categories as $category) {



                          echo '<option value="' . $category->slug . '">'  .$category->name .'</option>';



                        }



                      }



                      ?>



                  </select>



                </div>



              </div>



              <div class="form-group inputwithicon">



              <img src="<?php echo $site_url;  ?>/wp-content/uploads/2023/06/list-location.png" />



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



<?php



  $content = ob_get_clean();



  return $content;



}















add_shortcode('ZTS_LISTING_TITLE',  'zts_listing_title_page');



function zts_listing_title_page(){

  ob_start();

    $location = isset($_GET['location']) ? $_GET['location'] : 'CANADA';

    $categories = isset($_GET['category']) ? $_GET['category'] : '';

     if (empty($location)) {

      $location = 'CANADA';

    }

    ?>

    <div class="profile-title-location"><h1><?php echo $categories; ?> PHOTOGRAPHY IN  <span><?php echo $location; ?></span></h1></div>

   <?php



    $content = ob_get_clean();

    return $content;

}