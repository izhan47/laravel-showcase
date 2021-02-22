<?php

$uploadsUrlPath = 'uploads/';
$uploadsDocPath = "/uploads/";

return [
    "app_url" => env('APP_URL'),
    "app_name" => env('APP_NAME', 'Wag Enabled'),
    "react_server_base_url" => env('REACT_SERVER_BASE_URL', 'http://localhost:3000'),
   
    "status_codes" => [
        "success" => "200",
        "success_with_empty" => "201",
        "auth_fail" => "401",
        "form_validation" => "422",
        "normal_error" => "403",
        "server_side" => "500",
    ],

    "path" => [
        //HTTP URL Paths [Eg. get image]
        //http://localhost:8000/storage/app/public/
        "url" => [
            "user_profile_image_path" => $uploadsUrlPath."profile/",
            "users_pet_image_path" => $uploadsUrlPath."users_pet/",
            "watch_and_learn_thumbnail_path" => $uploadsUrlPath."watch_and_learn/images/",
            "watch_and_learn_author_path" => $uploadsUrlPath."watch_and_learn/author/",
            "watch_and_learn_video_path" => $uploadsUrlPath."watch_and_learn/videos/",
            "watch_and_learn_media_path" => $uploadsUrlPath."watch_and_learn/media/",
            "pet_pro_gallery_image_path" => $uploadsUrlPath."pet_pro/galleries/",
            "testimonial_image_path" => $uploadsUrlPath."testimonial/images/",
            "product_review_image_path" => $uploadsUrlPath."product_review/images/",
            
        ],

        //Storage Document Paths [Eg. store image]
        // /var/www/html/projectname/storage/
        "doc" => [
            "user_profile_image_path" => $uploadsDocPath."profile/",         
            "users_pet_image_path" => $uploadsDocPath."users_pet/",         
            "watch_and_learn_thumbnail_path" => $uploadsDocPath."watch_and_learn/images/",         
            "watch_and_learn_author_path" => $uploadsDocPath."watch_and_learn/author/",         
            "watch_and_learn_video_path" => $uploadsDocPath."watch_and_learn/videos/",         
            "watch_and_learn_media_path" => $uploadsDocPath."watch_and_learn/media/",         
            "pet_pro_gallery_image_path" => $uploadsDocPath."pet_pro/galleries/",
            "testimonial_image_path" => $uploadsDocPath."testimonial/images/", 
            "product_review_image_path" => $uploadsDocPath."product_review/images/",
        ]
    ],

    "days" => [ "monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday" ],

    "google_map" => [
        "api_key" => env("GOOGLE_API_KEY"),
    ],

    "no_of_care_from_best_display" => 6,
    "per_page_pet_pro_results" => 6,
    "per_page_watch_and_learn_results" => 6,
    "per_page_product_review_results" => 6,
    "no_of_related_video_display" => 3,
    "no_of_review_display" => 3,
    
    "no_of_comment_display" => 3,
    "no_of_comment_children_display" => 2,
    "comment_depth" => 2,

    "no_of_saved_video_display" => 3,
    "no_of_loved_pet_pro_display" => 2,
    "no_of_user_pet_pro_review_display" => 2,
    "no_of_users_pet_display" => 3,
    "send_contact_to_email" => env("CONTACT_TO_MAIL", 'ks@hyperspaceventures.com'),
    "send_comment_notification_to_email" => env("COMMENT_NOTIFICATION_TO_MAIL", 'ks@hyperspaceventures.com'),
    "product_review_category_id" => 26,

];
