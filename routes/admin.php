<?php

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::get('login', 'Auth\LoginController@showLoginForm');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout');

Route::middleware(['adminAuth'])->group(function () {

	Route::get('/', "DashboardController@index");
	Route::get('/get-users-graph-data', "DashboardController@getUsersGraphData");

	Route::get('/admin-users/datatable', 'AdminUsersController@getDatatable');
	Route::resource('/admin-users', 'AdminUsersController');

	Route::get('/users/datatable', 'UsersController@getDatatable');
	Route::resource('/users', 'UsersController');

	Route::get('/contacts/datatable', 'ContactsController@getDatatable');
	Route::resource('/contacts', 'ContactsController');

	Route::get('/business-requests/datatable', 'BusinessRequestsController@getDatatable');
	Route::resource('/business-requests', 'BusinessRequestsController');

	Route::get('/newsletters/datatable', 'NewslettersController@getDatatable');
	Route::resource('/newsletters', 'NewslettersController');

	Route::get('/watch-and-learn-categories/datatable', 'WatchAndLearnCategoriesController@getDatatable');
	Route::resource('/watch-and-learn-categories', 'WatchAndLearnCategoriesController');

	Route::get('/watch-and-learn-author/datatable', 'WatchAndLearnAuthorController@getDatatable');
	Route::resource('/watch-and-learn-author', 'WatchAndLearnAuthorController');

	Route::get('/watch-and-learn/get-comments/{slug}/{lastId?}/{parentId?}', 'WatchAndLearnController@CommentList');
	Route::delete('/watch-and-learn/delete-comment/{slug}/{id}','WatchAndLearnController@deleteComment');  
	Route::get('/watch-and-learn/datatable', 'WatchAndLearnController@getDatatable');
	Route::resource('/watch-and-learn', 'WatchAndLearnController');

	Route::post('watch-and-learn/store-media', 'WatchAndLearnController@storeMedia');
	
	Route::get('watch-and-learn/{id}/edit/buildwithcontentbuilder', 'WatchAndLearnController@buildWithContentBuilder');
	Route::put('watch-and-learn/{id}/save-description', 'WatchAndLearnController@setDescriptionByContentBuilder');
	Route::get('watch-and-learn/{id}/change-status/{status}', 'WatchAndLearnController@changeStatus');

	Route::post('/watch-and-learn-medias/load-more', 'WatchAndLearnMediaController@load_more')->name('load-more');
	Route::resource('/watch-and-learn-medias', 'WatchAndLearnMediaController');

	Route::get('/pet-pro-categories/datatable', 'PetProCategoriesController@getDatatable');
	Route::resource('/pet-pro-categories', 'PetProCategoriesController');

	Route::post('/pet-pros/{pet_pro_id}/deals/change-deal-status/{id}', 'PetProDealsController@changeStatus');
	Route::get('/pet-pros/{pet_pro_id}/deals/datatable/', 'PetProDealsController@getDatatable');
	Route::resource('/pet-pros/{pet_pro_id}/deals', 'PetProDealsController');

	Route::post('/pet-pros/{pet_pro_id}/events/change-events-status/{id}', 'PetProEventsController@changeStatus');
	Route::get('/pet-pros/{pet_pro_id}/events/datatable/', 'PetProEventsController@getDatatable');
	Route::resource('/pet-pros/{pet_pro_id}/events', 'PetProEventsController');
	
	Route::resource('/pet-pros/{pet_pro_id}/gallery', 'PetProGalleriesController');

	Route::get('/pet-pros/get-cities/{state_id}', 'PetProsController@getCities');
	Route::get('/pet-pros/get-states', 'PetProsController@getStates');
	Route::get('/pet-pros/get-geocode-data', 'PetProsController@getGeocodeData');
	Route::get('/pet-pros/datatable', 'PetProsController@getDatatable');
	Route::resource('/pet-pros', 'PetProsController');

	Route::get('testimonial/datatable', 'TestimonialController@getDatatable');
	Route::resource('testimonial', 'TestimonialController');

	Route::get('/product-review-categories/datatable', 'ProductReviewCategoriesController@getDatatable');
	Route::resource('/product-review-categories', 'ProductReviewCategoriesController');

	Route::get('/product-reviews/get-comments/{slug}/{lastId?}/{parentId?}', 'ProductReviewController@CommentList');
	Route::delete('/product-reviews/delete-comment/{slug}/{id}','ProductReviewController@deleteComment');  
	Route::get('/product-reviews/datatable', 'ProductReviewController@getDatatable');
	Route::resource('/product-reviews', 'ProductReviewController');

	Route::get('/product-reviews/{id}/edit/buildwithcontentbuilder', 'ProductReviewController@buildWithContentBuilder');
	Route::put('/product-reviews/{id}/save-description', 'ProductReviewController@setDescriptionByContentBuilder');
	Route::get('/product-reviews/{id}/change-status/{status}', 'ProductReviewController@changeStatus');

	Route::post('/product-reviews/{watch_and_learn_id}/deals/change-deal-status/{id}', 'ProductReviewDealsController@changeStatus');
	Route::get('/product-reviews/{watch_and_learn_id}/deals/datatable/', 'ProductReviewDealsController@getDatatable');
	Route::resource('/product-reviews/{watch_and_learn_id}/deals', 'ProductReviewDealsController');
	
});
