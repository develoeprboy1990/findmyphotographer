jQuery(document).ready(function($){

    







    var login = zts_ajax_url.login;

    var cat_limit = zts_ajax_url.category_limit;







    // cookies set function.

    const zts_setCookie = (key, value, expiry = 15) => {



        var expires = new Date();



        expires.setTime(expires.getTime() + (expiry * 60 * 1000)); // Convert expiry to milliseconds



        document.cookie = key + '=' + value + ';expires=' + expires.toUTCString() + '; path=/';



    }







    // cookies get function.



    const zts_getCookie = (key) => {



        var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');



        return keyValue ? keyValue[2] : null;



    }







    // Remove Cookies. 



    const zts_eraseCookie = (key) => {



        var keyValue = zts_getCookie(key);



        zts_setCookie(key, keyValue, '-1');



    }



    // for click on next button.

    $('.zts_plan_btn_click').on('click', function(e) { 

        // $('.zts_plan').prop('checked', false);

        // $(this).closest('.card').find('.zts_plan').prop('checked', true);

        var product_id            = $(this).attr('data-id');

        var product_title         = $(this).attr('data-title');

        var product_description   = $(this).attr('data-description');

        var product_price          = $(this).attr('data-price');

        var product_scope          = $(this).attr('data-scope');



        var g_business        = $(this).attr('data-business');

        var g_address         = $(this).attr('data-address');

        var g_category        = $(this).attr('data-category');

        var g_location        = $(this).attr('data-location');

        var g_page            = $(this).attr('data-page');

        var g_listing         = $(this).attr('data-listing');

        var g_gallery         = $(this).attr('data-gallery');

        $('.zts_hidden_product_scope').val(product_scope);

        $('.zts_hidden_product_id').val(product_id);



        

        // var product_duration      = $(this).attr('data-duration');

        // var product_categories    = $(this).attr('data-categories');

        // var product_sample_images = $(this).attr('data-sample_images');

        // var product_appear        = $(this).attr('data-appear');

        // var product_cities        = $(this).attr('data-cities');

        // var product_save          = $(this).attr('data-save');

 

        $('.b_selected_package').html(product_title);

    

        

        if (product_scope == 'free') {



            $('.zts_comp_province_cont').hide();



            $('.zts_comp_city_cont').hide();



            $('.zts_comp_profile_cont').hide();



            $('.zts_comp_gallery_cont').hide();

            

            $('.zts_business_phone_cont').hide();

            // $(".zts_comp_categories_cont").hide();



            // $(".zts_comp_location_cont").hide();



            $('.zts_categories_select2').select2({

                   maximumSelectionLength: 3,



                theme: "classic"



            });







            // for selet2 locations



             $('.get_form_location_id').select2({



                maximumSelectionLength: 1,



                theme: "classic"



            });







        }else{



            $('.zts_comp_province_cont').show();



            $('.zts_comp_city_cont').show();



            $('.zts_comp_profile_cont').show();



            $('.zts_comp_gallery_cont').show();



            $(".zts_comp_categories_cont").show();



            $(".zts_comp_location_cont").show();



            $('.zts_business_phone_cont').show();

                // for selet2 categories



            $('.zts_categories_select2').select2({



                theme: "classic"



            });







            // for selet2 locations



             $('.get_form_location_id').select2({



                maximumSelectionLength: cat_limit,



                theme: "classic"



            });



        }







        $('.zts_review_plan_title h4').html( product_title );

        $('.zts_review_plan_title p').html( product_description );

        $('.product-title').text(product_title + ' ' + product_description );

        $('.product-detail h2').html('Price: $' + product_price + ' <span>' + product_description +  '</span>');

        $('.zts_review_price').html( '<span class="price-table__currency">$</span>' + '	<span class="price-table__integer-part"> '+product_price+ '</span>' + '  <span class="price-table__period">'+product_description+ '</span');



 



        if (product_scope == 'free') {

            $(".rv_page i").removeClass("far fa-check-circle").addClass("fas fa-times");

            $(".rv_listing i").removeClass("far fa-check-circle").addClass("fas fa-times");

            $(".rv_gallery i").removeClass("far fa-check-circle").addClass("fas fa-times");

        }





        $('.rv_business span').html( g_business );

        $('.rv_website_address span').html( g_address );

        $('.rv_category span').html( g_category);

        $('.rv_locations span').html( g_location );

        $('.rv_page span').html( g_page );

        $('.rv_listing span').html( g_listing );

        $('.rv_gallery span').html( g_gallery );



    });







 



    // update value in review section.



    $('.zts_comp_name').on('blur', function() {



        var companyName = $(this).val(); 



        $('.last-price span').text(companyName);



    });







    $('.zts_comp_url').on('blur', function() {



        var companyName = $(this).val();



        $('.new-price span').text(companyName);



    });









    









    /// form submit



    $('.zts_form_submit').on('click', function(e) { 



        e.preventDefault();



            var product_id    = $('.zts_hidden_product_id').val();

            var product_scope = $('.zts_hidden_product_scope').val();

            var getLoaderGif = zts_ajax_url.loader_gif_url;

            if (product_scope == 'free') {



                var company_name = $('.zts_comp_name').val();



                var company_url  = $('.zts_comp_url').val();



                var categories   = $('#zts_only_categories').val();



                var location_id  = $('.get_form_location_id').val();



                var form_data = {



                    'product_id': product_id,



                    'product_scope' : product_scope,



                    'company_name': company_name,



                    'company_url': company_url,



                    'categories': categories,



                    'location_id': location_id



                };



                zts_eraseCookie('zts_form_data');



                zts_setCookie('zts_form_data', JSON.stringify(form_data));   



                var getSiteAdminURL = zts_ajax_url.ajax_url;



                var formData = new FormData();



                formData.append('product_id', product_id);



                formData.append('action', 'zts_process_form_data_free');



                $.ajax({



                    type: "post",



                    url: getSiteAdminURL,



                    data: formData,



                    cache: false,



                    processData: false,



                    contentType: false,



                    beforeSend: function (data) {



                        $('body').css('background-image', 'url(' + getLoaderGif + ')');



                        $("body").css('background-repeat', 'no-repeat');



                        $('body').css('background-position', 'center');



                        $('body').css('background-attachment', 'fixed');



                        $('body *').css('opacity', '0.8');



                    },



                    success: function (response) {



                        $("body").css('background-image', 'none');



                        $('body *').css('opacity', '1');

                             window.location.href = zts_ajax_url.zts_site_url + '/checkout';

                    }



                });



            }else{



                    var company_name = $('.zts_comp_name').val();



                    var company_url  = $('.zts_comp_url').val();



                    var company_phone  = $('.zts_business_phone').val();



                    var categories   = $('#zts_only_categories').val();



                    var location_id  = $('.get_form_location_id').val();



                    var profileImage = $(".zts_profile_file-input").get(0).files[0];



                    var files        = $("#multiplefileupload").get(0).files;



                    var getSiteAdminURL = zts_ajax_url.ajax_url;



                    var formData = new FormData();



                    for (var i = 0; i < files.length; i++) {



                        formData.append('multiplefileupload[]', files[i]);



                    }



                    formData.append('product_id', product_id);



                    formData.append('profileImage', profileImage);



                    formData.append('action', 'zts_process_form_data');



                    $.ajax({



                        type: "post",



                        url: getSiteAdminURL,



                        data: formData,



                        cache: false,



                        processData: false,



                        contentType: false,



                        beforeSend: function (data) {



                            $('body').css('background-image', 'url(' + getLoaderGif + ')');



                            $("body").css('background-repeat', 'no-repeat');



                            $('body').css('background-position', 'center');



                            $('body').css('background-attachment', 'fixed');



                            $('body *').css('opacity', '0.8');



                        },



                        success: function (response) {



                        if (response.success) {



                            $("body").css('background-image', 'none');



                            $('body *').css('opacity', '1');



                            var data = response.data;



                            var gallery = data.multiplefileupload;



                            var profile_img = data.profileImage;



                            var form_data = {



                                'product_id': product_id,



                                'company_name': company_name,



                                'product_scope' : product_scope,



                                'company_phone' : company_phone,



                                'company_url': company_url,



                                'categories': categories,



                                'location_id': location_id,



                                'profile_img': profile_img,



                                'gallery': gallery



                            };



                            zts_eraseCookie('zts_form_data');



                            zts_setCookie('zts_form_data', JSON.stringify(form_data));



                            window.location.href = zts_ajax_url.zts_site_url + '/checkout';

                        } 



                        }



                    });



                }

            

         

    });



    







    // For profile image in form.



    // $('.zts_profile_file-input').change(function(event){



    //     event.preventDefault();



    //     var curElement = $('.image');



    //     var reader = new FileReader();



    //     reader.onload = function (e) {



    //         // get loaded data and render thumbnail.



    //         curElement.attr('src', e.target.result);



    //     };



    //     // read the image file as a data URL.



    //     reader.readAsDataURL(this.files[0]);



    // });



    

    function zts_set_profile_img(input) {

        if (input.files && input.files[0]) {

            var reader = new FileReader();

            reader.onload = function(e) {

                $('#imagePreview').css('background-image', 'url('+e.target.result +')');

                $('#imagePreview').hide();

                $('#imagePreview').fadeIn(650);

            }

            reader.readAsDataURL(input.files[0]);

        }

    }

    $("#zts_file_upload").change(function() {

        zts_set_profile_img(this);

    });

 







});