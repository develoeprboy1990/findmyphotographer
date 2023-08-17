<?php
add_shortcode('ZTS_ADD_BUSINESS',  'zts_add_business_callback');

function zts_add_business_callback($atts)
{
  ob_start();
  wp_enqueue_style('zts_business_css');
  wp_enqueue_style('zts_gallery_bootstrap_css');
  wp_enqueue_style('zts_gallery_font_awsome');
  wp_enqueue_style('zts_gallery_fileinput');
  wp_enqueue_style('zts_gallery_css');
  wp_enqueue_script('zts_bs_pooper');
  wp_enqueue_script('zts_bs_min');
  wp_enqueue_script('zts_bs_file_input');
  wp_enqueue_script('zts_file_input_theme');
  wp_enqueue_script('zts_gallery_main_js');
  // for categories and lcoation.
  wp_enqueue_style('select2_css');
  wp_enqueue_script('select2_js');
  $url = get_site_url();
  // FOR LOCATIONS.
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
  //FOR CATEGORIES
  $categories = get_terms(array(
    'taxonomy' => 'product_cat',
    'hide_empty' => false,
    'exclude' => array(get_option('default_product_cat')),
  ));
  // FOR PROVINCE.
  $taxonomy = 'location'; // Replace 'location' with your custom taxonomy slug

  $zts_province = get_terms(array(
    'taxonomy' => $taxonomy,
    'hide_empty' => false,
    'hierarchical' => true, // Enable hierarchical display
    'parent' => 0, // Set parent parameter to 0 to retrieve only top-level terms
  ));?>
  <form id="myForm">
    <div id="step1" class="form-step">
      <?php require_once plugin_dir_path(__FILE__) . 'steps/plans.php'; ?>
      <div class="text-right abc mt-5">
        <!-- <button type="button" class="junaid next-btn btn zts_first_click  mr-3">Next</button> -->
      </div>
    </div>
    <div id="step2" class="form-step">
      <?php
      require_once plugin_dir_path(__FILE__) . 'steps/business.php'; ?>
      <div class="text-md-right text-center abc mt-5">
        <button type="button" class="prev-btn ">Previous</button>
        <button type="button" class="next-btn zts_second_step_next">Next</button>
      </div>
    </div>
    <div id="step3" class="form-step">
      <?php
      require_once plugin_dir_path(__FILE__) . 'steps/review.php'; ?>
      <div class="text-md-right text-center abc mt-5">
        <button type="button" class="prev-btn mr-4 ">Previous</button>
        <button type="button" class="btn check-out zts_form_submit">
          Proceed To Checkout
        </button>
      </div>
    </div>
  </form>
  <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
  <script>
    $(document).ready(function() {
      var currentStep = 1;
      var totalSteps = $('.form-step').length;
      
      $('.next-btn').click(function(e) {
        e.preventDefault();
        var zts_exist = $('.zts_exist').val();
        if (zts_exist == 1) {
          alert('you already have Active Plan. Kindly Contact admin  to Cancel that Plan first');
          return false;
        }
        // Check if the current step is the second step
        if (currentStep === 2) {
          var product_scope = $('.zts_hidden_product_scope').val();
          if (product_scope == 'free') {
            var account_email = $('#account_email').val();
            if (account_email == '') {
              $('.zts_email_address_error').text('Email Address is Required').show();
              document.querySelector("#account_email").scrollIntoView({ behavior: "smooth" });
              return;
            } else {
              $('.zts_email_address_error').hide();
            }
            var account_password = $('#account_password').val();
            if (account_password == '') {
              $('.zts_password_error').text('Password is Required').show();

              document.querySelector("#account_password").scrollIntoView({ behavior: "smooth" });
              return;
            } else {
              $('.zts_password_error').hide();
            }
            var zts_comp_name = $('.zts_comp_name').val();
            if (zts_comp_name == '') {
              $('.zts_comp_name_error').text('Enter a valid company name').show();
              document.querySelector('.zts_comp_name').scrollIntoView({ behavior: "smooth" });
              return;
            } else {
              $('.zts_comp_name_error').hide();

            }
            var zts_comp_url = $('.zts_comp_url').val();
            if (zts_comp_url == '') {
              $('.zts_comp_url_error').text('Enter a valied company url').show();

              document.querySelector(".zts_comp_url").scrollIntoView({ behavior: "smooth" });
              return;
            } else {
              $('.zts_comp_url_error').hide();
            }
            var categories = $('.zts_categories_select2').val();
            if (categories == '' || categories == 'Select Category') {
              $('.zts_categories_select2_error').text('Select Category').show();

              document.querySelector(".zts_categories_select2").scrollIntoView({ behavior: "smooth" });
              return;
            } else {
              $('.zts_categories_select2_error').hide();
            }
            var location = $('.get_form_location_id').val();
            if (location == '' || location == 'Select Location') {
              $('.get_form_location_error').text('Select Location').show();

              document.querySelector(".get_form_location_id").scrollIntoView({ behavior: "smooth" });
              return;
            } else {
              $('.get_form_location_error').hide();
            }
          } else {
            var zts_comp_name = $('.zts_comp_name').val();
            if (zts_comp_name == '') {
              $('.zts_comp_name_error').text('Enter a valid company name').show();

              document.querySelector(".zts_comp_name").scrollIntoView({ behavior: "smooth" });
              return;
            } else {
              $('.zts_comp_name_error').hide();
            }
            var zts_comp_url = $('.zts_comp_url').val();
            if (zts_comp_url == '') {

              $('.zts_comp_url_error').text('Enter a valied company url').show();

              document.querySelector(".zts_comp_url").scrollIntoView({ behavior: "smooth" });
              return;
            } else {
              $('.zts_comp_url_error').hide();
            }

            var phone = $('.zts_business_phone').val();
            if (phone == '') {
              $('.zts_business_error').text('Enter a valied Phone NO').show();

              document.querySelector(".zts_business_phone").scrollIntoView({ behavior: "smooth" });
              return;
            } else {
              $('.zts_business_error').hide();
            }
            var fileInput = $('#zts_file_upload');
            if (fileInput[0].files.length === 0) {
              $('.zts_file_upload_error').text('Profile Image is not Selected').show();

              document.querySelector(".zts_comp_profile_cont").scrollIntoView({ behavior: "smooth" });
              return;
            } else {
              $('.zts_file_upload_error').hide();
            }
            var gallery = $('#multiplefileupload');
            if (gallery[0].files.length === 0) {
              $('.get_gallery_error').text('Gallery Image is not Selected').show();

              document.querySelector("#multiplefileupload").scrollIntoView({ behavior: "smooth" });
              return;
            } else {
              $('.get_gallery_error').hide();
            }
            var categories = $('.zts_categories_select2').val();
            if (categories == '' || categories == 'Select Category') {
              $('.zts_categories_select2_error').text('Select Category').show();

              document.querySelector(".zts_categories_select2").scrollIntoView({ behavior: "smooth" });
              return;
            } else {
              $('.zts_categories_select2_error').hide();
            }
            var location = $('.get_form_location_id').val();
            if (location == '' || location == 'Select Location') {
              $('.get_form_location_error').text('Select Location').show();

              document.querySelector(".get_form_location_id").scrollIntoView({ behavior: "smooth" });
              return;
            } else {
              $('.get_form_location_error').hide();
            }

          }

        }
        if (currentStep < totalSteps) {
          $('#step' + currentStep).hide();
          currentStep++;
          $('#step' + currentStep).show();
          $("#myForm").scrollTop(0);
          document.querySelector("#myForm").scrollIntoView({ behavior: "smooth" }); 
        }

      });

      $('.prev-btn').click(function() {
        if (currentStep > 1) {
          $('#step' + currentStep).hide();
          currentStep--;
          $('#step' + currentStep).show();
        }
      });


    });
  </script>
<?php
} ?>