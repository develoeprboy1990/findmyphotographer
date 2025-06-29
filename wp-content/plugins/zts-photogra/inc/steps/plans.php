<?php
$get_subscriptions = array(
  'post_type' => 'product', // Replace 'product' with your custom post type name if applicable
  'tax_query' => array(
    array(
      'taxonomy' => 'product_tag',
      'field'    => 'slug',
      'terms'    => 'plan',
    ),
  ),
  'order'   => 'ASC',
  'orderby' => 'date',
);
$query     = new WP_Query($get_subscriptions);
$zts_exist = 0;
$customer_plan   = 0;
$user_id   = get_current_user_id();

if (!empty($user_id)) {
  global $wpdb;
  $table_name  = $wpdb->prefix . 'zts_user_data';
  $sql         = $wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %s && status !='upgraded' ", $user_id);
  $row         = $wpdb->get_row($sql, ARRAY_A);
  $currentPlan = $row['priority'];//mutahir
  if (!empty($row) && $row['priority'] != '3') {
    // User exists
    $zts_exist = 1;
    $customer_plan  = $row['customer_plan'];
  } else {
    // User does not exist
    $zts_exist = 0;
    $customer_plan  = $row['customer_plan'];
  }
} else {
  // No user ID found
  $zts_exist = 0;
}
?>

<div class="choose-plan-title">
  <h3 class="">
    <span>Choose a<b> Plan</b></span>
  </h3>
  <input type="hidden" class="zts_exist" value="<?php echo $zts_exist; ?>"> 
</div>
<div class="choose-us-main-section">
  <?php
  if ($query->have_posts()) {
    while ($query->have_posts()) {
      $query->the_post();
      $product = wc_get_product(get_the_ID());
      $product_price = $product->get_price();
      $disable = '';
      $class = 'next-btn cta-btn';
      if ((!empty($currentPlan) && $currentPlan == 3 && get_field('scope') == 'free') || $customer_plan == get_the_ID()) {
        $disable = 'disabled';
        $class = 'next-btn-disabled cta-btn-disabled';
      }
  ?>
      <div class="card">
        
        <div class="pricing-card bsic">
          <div class="heading"   <?php if ($customer_plan == get_the_ID()) { echo 'style="background:#8f8f8f"'; } ?>>
            <h4><?php echo the_title(); ?></h4>
            <p><?php echo get_the_content(); ?></p>
          </div>
          <div class="price">
            <span class="price-table__currency">$</span>
            <span class="price-table__integer-part"> <?php echo $product_price; ?></span>
            <span class="price-table__period">
              <?php
              if (!empty(get_the_content())) {
                echo  get_the_content();
              }
              ?>
            </span>
          </div>

          <ul class="features">
            <?php
            if (!empty(get_field('name_of_business'))) {
            ?>
              <li>
                <i class="fa fa-check-circle" aria-hidden="true"></i>
                <?php echo get_field('name_of_business'); ?>
              </li>
            <?php
            }
            if (!empty(get_field('_website_address'))) {
            ?>
              <li>

                <i class="fa fa-check-circle" aria-hidden="true"></i>
                <?php echo get_field('_website_address'); ?>
              </li>
            <?php
            }
            if (!empty(get_field('category_listing'))) {
            ?>
              <li>
                <i class="fa fa-check-circle" aria-hidden="true"></i>
                <?php echo get_field('category_listing'); ?>
              </li>
            <?php
            }

            if (!empty(get_field('location'))) {
            ?>
              <li><i class="fa fa-check-circle" aria-hidden="true"></i>
                <?php echo get_field('location'); ?>
              </li>
            <?php
            }

            if (!empty(get_field('personal_page'))) {
            ?>
              <li>
                <?php
                if (get_field('scope') == 'free') {
                  echo '<i class="fas fa-times"></i>';
                } else {
                  echo '<i class="fa fa-check-circle" aria-hidden="true"></i>';
                }
                ?>
                <?php echo get_field('personal_page'); ?>
              </li>
            <?php
            }

            if (!empty(get_field('featured_listing'))) {
            ?>
              <li>
                <?php
                if (get_field('scope') == 'free') {
                  echo '<i class="fas fa-times"></i>';
                } else {
                  echo '<i class="fa fa-check-circle" aria-hidden="true"></i>';
                }
                ?>
                <?php echo get_field('featured_listing'); ?>
              </li>
            <?php }
            if (!empty(get_field('image_gallery'))) { ?>
              <li>
                <?php
                if (get_field('scope') == 'free') {
                  echo '<i class="fas fa-times"></i>';
                } else {
                  echo '<i class="fa fa-check-circle" aria-hidden="true"></i>';
                }
                ?>
                <?php echo get_field('image_gallery'); ?>
              </li>
            <?php }

            ?>
          </ul>
          <button data-title="<?php echo the_title();  ?>" data-description="<?php echo get_the_content(); ?>" data-business="<?php echo get_field('name_of_business') ?>" 
          data-address="<?php echo get_field('_website_address') ?>" data-category="<?php echo get_field('category_listing') ?>" 
          data-location="<?php echo get_field('location') ?>" 
          data-page="<?php echo get_field('personal_page') ?>" 
          data-listing="<?php echo get_field('featured_listing') ?>" data-gallery="<?php echo get_field('image_gallery') ?>" data-price="<?php echo $product_price;  ?>" 
          data-scope="<?php echo get_field('scope')  ?>" data-id="<?php echo get_the_ID(); ?>" 
          class="zts_plan_btn_click <?php echo $class; ?>"
           <?php echo $disable; ?> >order Now</button>
        </div>
        <div class="price-table__ribbon">
          <?php
          if (get_field('scope') == 'basic') {
          ?>
            <div class="price-table__ribbon-inner">Popular</div>
          <?php
          }
          ?>
        </div>
      </div>
  <?php
    }
    wp_reset_postdata(); // Reset the query
  }
  ?>
</div>