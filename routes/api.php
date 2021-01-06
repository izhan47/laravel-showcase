<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* API Dingo */
$api = app('Dingo\Api\Routing\Router');

$api->version('v1', ['prefix' => 'api', 'namespace' => '\App\Http\Controllers\Api\V1', 'middleware' => ['jwt_setAuthProvider']], function ($api) {
  
    $api->group(['prefix' => '', 'namespace' => 'Auth'], function ($api) {            
        $api->post('login', ['as' => 'login', 'uses' => 'LoginController@login']);            
        $api->post('social-login', ['as' => 'social-login', 'uses' => 'SocialLoginController@login']);
        $api->post('social-signup', ['as' => 'social-signup', 'uses' => 'SocialSignUpController@signup']);
        $api->post('register', ['as' => 'register', 'uses' => 'RegisterController@create']);            
        $api->post('forgot-password', ['as' => 'forgot-password', 'uses' => 'ForgotPasswordController@forgotPassword']);            
        $api->post('reset-password', ['as' => 'password.request', 'uses' => 'ResetPasswordController@resetPassword']);    
    });

    $api->post('store-contact', ['as' => 'store-contact', 'uses' => 'ContactController@store']);
    $api->post('store-business-request', ['as' => 'store-business-request', 'uses' => 'BusinessRequestController@store']);
    $api->post('store-newsletter', ['as' => 'store-newsletter', 'uses' => 'NewsletterController@store']);
    $api->post('get-testimonial-counts', ['as' => 'get-testimonial-counts', 'uses' => 'HomeController@getTestimonialCounts']);
	$api->post('get-featured-pet-pro-list', ['as' => 'get-featured-pet-pro-list', 'uses' => 'HomeController@getFeaturedPetProList']);
	$api->post('get-testimonial-list', ['as' => 'get-testimonial-list', 'uses' => 'HomeController@getTestimonialList']);

    $api->group(['prefix' => 'pet-pro'], function ($api) { 
        $api->post('get-care-from-best', ['as' => 'get-care-from-best', 'uses' => 'PetProController@careFromBestList']);
        $api->post('get-category-list', ['as' => 'get-category-list', 'uses' => 'PetProController@getCategoryList']);
        $api->post('get-list/{page?}', ['as' => 'get-list', 'uses' => 'PetProController@getList']);
        $api->post('get-map-list', ['as' => 'get-map-list', 'uses' => 'PetProController@getMapList']);
        $api->post('get-reviews/{slug}/{lastId?}', ['as' => 'get-reviews', 'uses' => 'PetProController@getReviewList']);
        $api->post('get-details/{slug}', ['as' => 'get-details', 'uses' => 'PetProController@getDetails']);        
    });

    $api->group(['prefix' => 'watch-and-learn'], function ($api) { 
        $api->post('get-list/{page?}', ['as' => 'get-list', 'uses' => 'WatchAndLearnController@getList']);
        $api->post('get-category-list', ['as' => 'get-category-list', 'uses' => 'WatchAndLearnController@getCategoryList']);
        $api->post('get-related-videos/{slug}', ['as' => 'get-related-videos', 'uses' => 'WatchAndLearnController@getRelatedVideoList']);
        $api->post('get-details/{slug}', ['as' => 'get-details', 'uses' => 'WatchAndLearnController@getDetails']);
        $api->post('get-comments/{slug}/{lastId?}/{parentId?}', ['as' => 'review', 'uses' => 'WatchAndLearnController@CommentList']);
    });

    $api->group(['prefix' => 'product-reviews'], function ($api) { 
        $api->post('get-list/{page?}', ['as' => 'get-list', 'uses' => 'ProductReviewController@getList']);
        $api->post('get-category-list', ['as' => 'get-category-list', 'uses' => 'ProductReviewController@getCategoryList']);
    });

    /*After Login*/
    $api->group(['middleware' => ['jwtAuth']], function ($api) {  

        $api->post('get-country-list', ['as' => 'get-country-list', 'uses' => 'UsersController@getCountryList']);
       
        $api->group(['prefix' => 'profile'], function ($api) { 
            $api->post('get-details', ['as' => 'get-details', 'uses' => 'UsersController@getProfileDetails']);
            $api->post('update', ['as' => 'update', 'uses' => 'UsersController@updateProfile']);
            $api->post('update-location', ['as' => 'update', 'uses' => 'UsersController@updateLocation']);
            $api->post('add-pets', ['as' => 'add-pets', 'uses' => 'UsersController@storeMyPets']);
            $api->post('delete-my-pet/{id}', ['as' => 'delete-my-pet', 'uses' => 'UsersController@deleteMyPet']);
            $api->post('update-vet', ['as' => 'update-vet', 'uses' => 'UsersController@updateVetDetails']);
            $api->post('get-breed-list', ['as' => 'get-breed-list', 'uses' => 'UsersController@getBreedList']);
            $api->post('complete', ['as' => 'complete', 'uses' => 'UsersController@completeProfile']);
           
            $api->post('get-loved-pet-pros/{lastId?}', ['as' => 'get-loved-pet-pros', 'uses' => 'UsersController@getLovedPetPros']);
            $api->post('get-saved-videos/{page?}', ['as' => 'get-saved-videos', 'uses' => 'UsersController@getSavedVideos']);
            $api->post('get-saved-product-review/{page?}', ['as' => 'get-saved-product-review', 'uses' => 'UsersController@getSavedProductReview']);
            $api->post('get-user-pet-pro-reviews/{page?}', ['as' => 'get-user-pet-pro-reviews', 'uses' => 'UsersController@getUserPetProReview']);
            $api->post('get-users-pets/{lastId?}', ['as' => 'get-users-pets', 'uses' => 'UsersController@getUsersPet']);
        });

        $api->group(['prefix' => 'pet-pro'], function ($api) {             
            $api->post('like-dislike/{slug}', ['as' => 'like-dislike', 'uses' => 'PetProController@likeDislikePetPro']);
            $api->post('review/{slug}', ['as' => 'review', 'uses' => 'PetProController@storeReview']);
            $api->post('delete-review/{slug}/{id}', ['as' => 'delete-review', 'uses' => 'PetProController@deleteReview']);
            $api->post('deal/claim/{slug}/{pet_deal_id}', ['as' => 'deal/claim', 'uses' => 'PetProController@claimDeal']);
        });
        $api->group(['prefix' => 'watch-and-learn'], function ($api) {             
            $api->post('save-unsaved/{slug}', ['as' => 'save-unsaved', 'uses' => 'WatchAndLearnController@saveUnsaveVideos']);
            $api->post('store-comment', ['as' => 'store-comment', 'uses' => 'WatchAndLearnController@storeComment']);            
            $api->post('delete-comment/{slug}/{id}', ['as' => 'delete-comment', 'uses' => 'WatchAndLearnController@deleteComment']);            
        });

        $api->group(['prefix' => 'product-reviews'], function ($api) {             
           $api->post('deal/claim/{slug}/{watch_and_learn_deal_id}', ['as' => 'deal/claim', 'uses' => 'ProductReviewController@claimDeal']);           
        });

    }); 

});
