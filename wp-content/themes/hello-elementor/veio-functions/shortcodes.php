<?php


// WEBSITE URL
function company_url_shortcode() {
    if( function_exists('get_field') ) {
        $company_url = get_field('company_url');
        if( $company_url ) {
            return $company_url;
        } else {
            return '#';
        }
    }
}
add_shortcode('company_url', 'company_url_shortcode');





// City
function city_shortcode() {
    if (function_exists('get_field')) {
        $city_string = get_field('city');
        $city_array = json_decode($city_string, true);
        if (is_array($city_array) && !empty($city_array)) {
            $output = '<div class="city-container">';
            foreach ($city_array as $city) {
                $output .= '<div class="city">' . $city . '</div>';
            }
            $output .= '</div>';
            return $output;
        } else {
            return 'Câmpul "city" nu a fost completat sau nu este de tipul array.';
        }
    }
}
add_shortcode('city', 'city_shortcode');





