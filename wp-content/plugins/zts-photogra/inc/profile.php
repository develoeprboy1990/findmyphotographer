<?php





add_shortcode('ZTS_ADD_PROFILE',  'zts_add_profile_callback');

function zts_add_profile_callback($atts)
{
    ob_start();
    $g_company_url = isset($_GET['user']) ? $_GET['user'] : '';
    if (!empty($g_company_url)) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'zts_user_data';
        $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE company_name = %s", $g_company_url);
        $row = $wpdb->get_row($sql, ARRAY_A);
        if ($row) {
            // Access the retrieved data
            $company_name = $row['company_name'];
            $company_url = $row['company_url'];
            if (!preg_match("~^(?:f|ht)tps?://~i", $company_url)) {
                $company_url = "http://" . $company_url;
            }

            $phone_number = $row['phone_number'];
            $categories = unserialize($row['categories']);
            $profile_locations = unserialize($row['locations']);
        } ?>
        <style>
            .main-profile-content .profile-main-lisiting {
                color: #14234B;
                font-family: "Poppins", Sans-serif;
                font-size: 32px;
                font-weight: 400;
                text-transform: uppercase;
                margin-top: 0;
            }

            .main-profile-content .profile-main-lisiting span {
                font-weight: 800;
            }

            .profile-icon-list-items,
            .profile-categories-lists {
                list-style: none;
                justify-content: flex-start;
                display: flex;
                flex-wrap: wrap;
                margin: 0;
                padding: 0;

            }

            .profile-icon-list-items .icon-list-item,
            .profile-categories-lists .cat-ist {
                justify-content: flex-start;
                text-align: left;
                display: flex;
                align-items: start;
                word-break: break-word;
                line-height: normal;
                margin-right: 40px;
            }

            .profile-icon-list-items .icon-list-item .icon-list-icon img {
                width: 42px;
            }

            .profile-icon-list-items .icon-list-item .icon-list-text {
                margin-top: 9px;
                margin-left: 6px;
                font-family: poppins;
                font-size: 14px;
                text-transform: capitalize;
            }

            .profile-icon-list-items .icon-list-item .icon-list-text a {
                color: #14234b;
                font-weight: 600;
            }

            .profile-icon-list-items .icon-list-item .icon-list-text a:hover {
                color: #2dae9c;
            }

            .profile-categories-lists .cat-ist {
                border: 2px solid #14234b;
                padding: 6px 27px;
                font-family: poppins;
                text-transform: uppercase;
                border-radius: 100px;
                font-weight: 600;
                font-size: 13px;
                margin-right: 18px !important;
            }

            .profile-categories-lists .cat-ist a {
                color: #14234b;
            }

            .profile-categories-lists {
                margin-top: 18px !important;
            }
        </style>
        <div class="main-profile-content">
            <h1 class="profile-main-lisiting">
                <?php echo $company_name; ?> <span>photographer</span></h1>
            <ul class="profile-icon-list-items">
                <li class="icon-list-item">
                    <span class="icon-list-icon">
                        <img src="https://findmyphotographer.ca/wp-content/uploads/2023/03/LOCATION.png" /></span>
                    <span class="icon-list-text">
                        <?php
                        $terms = [];
                        foreach ($profile_locations as $term_id) {
                            $term = get_term_by('term_id', $term_id, 'location');
                            if ($term) {
                                // $terms[] = $term->name;
                                $terms[] = '<a href="' . get_site_url() . '/search-page/?location=' . $term->name . '">' . $term->name . '</a>';
                            }
                        }
                        echo implode(', ', $terms); ?>
                    </span>
                </li>
                <li class="icon-list-item">
                    <span class="icon-list-icon">
                        <img src="https://findmyphotographer.ca/wp-content/uploads/2023/03/phone.png" />
                    </span>
                    <span class="icon-list-text">+<?php echo $phone_number; ?></span>
                </li>
                <li class="icon-list-item">
                    <span class="icon-list-icon"><img src="https://findmyphotographer.ca/wp-content/uploads/2023/03/website.png" /></span>
                    <span class="icon-list-text">VISIT <a href="<?php echo $company_url; ?>" target="_blank">WEBSITE</a></span>
                </li>
            </ul>
            <ul class="profile-categories-lists"> <?php
                                                    foreach ($categories as $category_id) {
                                                        $category = get_term_by('term_id', $category_id, 'product_cat');
                                                        $category_name = $category->name;
                                                    ?> <li class="cat-ist">

                        <a href="<?php echo get_site_url() . '/search-page/?category=' . $category_name; ?>">

                            <?php echo  $category_name; ?>

                        </a>

                    </li>
                <?php
                                                    } ?>
            </ul>
        </div>
    <?php
    }
    $content = ob_get_clean();
    return $content;
}
add_shortcode('ZTS_ADD_PROFILE_GALLERY',  'zts_add_profile_gallery_callback');
function zts_add_profile_gallery_callback($atts)

{
    ob_start();
    $g_company_url = isset($_GET['user']) ? $_GET['user'] : '';
    if (!empty($g_company_url)) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'zts_user_data';
        $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE company_name = %s", $g_company_url);
        $row = $wpdb->get_row($sql, ARRAY_A);
        if ($row) {
            $gallery_images = unserialize($row['gallery_images']);
        }
    ?>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
        <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/lightgallery@2.0.0-beta.3/css/lightgallery.css'>
        <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/lightgallery@2.0.0-beta.3/css/lg-zoom.css'>
        <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/justifiedGallery@3.8.1/dist/css/justifiedGallery.css'>
        <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.css'>
        <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/lightgallery@2.0.0-beta.3/css/lg-thumbnail.css'>
        <style type="text/css">
            /*    body {
  padding: 40px;
  background-image: linear-gradient(#e8f0ff 0%, white 52.08%);
  color: #0e3481;
  min-height: 100vh;

}

.header .lead {


  max-width: 620px;





}*/
            /** Below CSS is completely optional **/


            /*.gallery-item {
  width: 200px;
  padding: 5px;
}*/
        </style>
        <div class="">
            <div class="">
                <div class="">
                    <div class="gallery-container" id="animated-thumbnails-gallery">
                        <?php
                        foreach ($gallery_images as $key => $value) {
                            $guid = $value; ?>
                            <a class="gallery-item" data-src="<?php echo $guid; ?>">
                                <img class="img-responsive" src="<?php echo $guid; ?>" />
                            </a>
                        <?php
                        } ?>
                    </div>
                </div>
            </div>
        </div>
        <script src='https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.js'></script>
        <script src='https://cdn.jsdelivr.net/npm/lightgallery@2.0.0-beta.3/lightgallery.umd.js'></script>
        <script src='https://cdn.jsdelivr.net/npm/lightgallery@2.0.0-beta.3/plugins/zoom/lg-zoom.umd.js'></script>
        <script src='https://cdn.jsdelivr.net/npm/justifiedGallery@3.8.1/dist/js/jquery.justifiedGallery.js'></script>
        <script src='https://cdn.jsdelivr.net/npm/lightgallery@2.0.0-beta.3/plugins/thumbnail/lg-thumbnail.umd.js'></script>
        <script type="text/javascript">
            jQuery("#animated-thumbnails-gallery")
                .justifiedGallery({
                    captions: false, 
                   // lastRow: "hide",
                    rowHeight: 180,
                    margins: 5
                }).on("jg.complete", function() {
                    window.lightGallery(
                        document.getElementById("animated-thumbnails-gallery"), {
                            autoplayFirstVideo: false,
                            pager: false,
                            galleryId: "nature",
                            plugins: [lgZoom, lgThumbnail],
                            mobileSettings: {
                                controls: false,
                                showCloseIcon: false,
                                download: false,
                                rotate: false
                            }
                        }
                    );
                });
        </script> 
    <?php
    }
    $content = ob_get_clean();
    return $content;
}
add_shortcode('ZTS_FAQ', 'zts_faq_callback');
function zts_faq_callback()
{
    ob_start();
    $g_company_url = isset($_GET['user']) ? $_GET['user'] : '';
    if (!empty($g_company_url)) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'zts_user_data';
        $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE company_name = %s", $g_company_url);
        $row = $wpdb->get_row($sql, ARRAY_A);
        if ($row) {
            // Access the retrieved data
            $user_id = $row['user_id'];
        }
        $being_fp = get_user_meta($user_id, 'being_fp', true);
        $style_of_fp = get_user_meta($user_id, 'style_of_fp', true);
        $become_fp = get_user_meta($user_id, 'become_fp', true);

    ?>
        <div class="faqs-section">
            <div class="accordion">

                <?php
                if (!empty($become_fp)) {?>
                    <div class="accordion-item">
                        <button id="accordion-button-3" aria-expanded="false"><span class="accordion-title">Why did you become a family photographer?</span><i class="icon"></i></button>
                        <div class="accordion-content" style="padding-left:10px;padding-right:10px">
                            <p><?php echo $become_fp; ?></p>
                        </div>
                    </div>
                <?php

                }

                if (!empty($being_fp)) {

                ?>


                    <div class="accordion-item">

                        <button id="accordion-button-1" aria-expanded="false"><span class="accordion-title">What do you like most about being a family photographer?</span><i class="icon"></i></button>

                        <div class="accordion-content" style="padding-left:10px;padding-right:10px">

                            <p><?php echo $being_fp; ?></p>

                        </div>

                    </div>

                <?php

                }

                if (!empty($style_of_fp)) {

                ?>

                    <div class="accordion-item">

                        <button id="accordion-button-2" aria-expanded="false"><span class="accordion-title">Do you specialize in a certain style of photograhy?</span><i class="icon"></i></button>

                        <div class="accordion-content" style="padding-left:10px;padding-right:10px">

                            <p><?php echo $style_of_fp; ?></p>

                        </div>

                    </div>

                <?php

                }

                ?>



            </div>

        </div>



        <script>
            const items = document.querySelectorAll(".accordion button");



            function toggleAccordion() {

                const itemToggle = this.getAttribute('aria-expanded');



                for (i = 0; i < items.length; i++) {

                    items[i].setAttribute('aria-expanded', 'false');

                }



                if (itemToggle == 'false') {

                    this.setAttribute('aria-expanded', 'true');

                }

            }



            items.forEach(item => item.addEventListener('click', toggleAccordion));
        </script>

<?php

    }











    $content = ob_get_clean();

    return $content;
}











?>