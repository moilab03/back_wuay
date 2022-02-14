<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['prefix' => 'v1'], function () {

    Route::post('auth/admin', 'Auth\AuthController@login');
    Route::get('auth/check', 'Auth\AuthController@checkAuth');
    Route::get('auth/me', 'Auth\AuthController@me');
    Route::get('countries', 'Country\CountryIndexController@index');
    Route::get('departments/{country}', 'Department\DepartmentIndexController@index');
    Route::get('cities/{department}', 'City\CityIndexController@index');
    Route::post('users/commerce', 'User\UserStoreController@storeAdminCommerce');
    Route::get('users/commerce', 'User\UserIndexController@indexUserCommerce');
    Route::put('users/commerce/{user}', 'User\UserUpdateController@updateCommerce');
    Route::get('users/status/{user}', 'User\UserUpdateController@changeState');
    Route::post('commerces', 'Commerce\CommerceStoreController@store');
    Route::put('commerces/{commerce}', 'Commerce\CommerceUpdateController@update');
    Route::post('categories/{commerce}', 'Category\CategoryStoreController@store');
    Route::get('categories/status/{category}/{commerce}', 'Category\CategoryUpdateController@changeState');
    Route::put('categories/{category}/{commerce}', 'Category\CategoryUpdateController@update');
    Route::get('categories/user/{commerce}', 'Category\CategoryIndexController@indexForUser');
    Route::get('categories/commerce/{commerce}', 'Category\CategoryIndexController@indexForCommerce');
    Route::get('products/menu/{category}', 'Product\ProductIndexController@indexUser');
    Route::get('products/commerce/{commerce}', 'Product\ProductIndexController@indexForCommerce');
    Route::get('products/status/{product}', 'Product\ProductUpdateController@changeState');
    Route::put('products/{product}', 'Product\ProductUpdateController@update');
    Route::post('products/{commerce}', 'Product\ProductStoreController@store');
    Route::get('commerces/user/{commerce}', 'Commerce\CommerceShowController@show');


    Route::post('commerces/game', 'Commerce\CommerceIndexController@getByDistance');
    Route::post('auth/user', 'Auth\AuthController@authPhone');
    Route::post('users', 'User\UserStoreController@storeUser');
    Route::post('variantProducts', 'ProductVariant\ProductVariantStoreController@store');
    Route::put('variantProducts/{product_variant}', 'ProductVariant\ProductVariantUpdateController@update');
    Route::delete('variantProducts/{product_variant}', 'ProductVariant\ProductVariantDeleteController@delete');
    Route::post('groupUsers', 'GroupUser\GroupUserStoreController@store');
    Route::get('groupUsers', 'GroupUser\GroupUserIndexController@index');
    Route::get('interests', 'Interest\InterestIndexController@index');
    Route::post('groupInterest', 'GroupInterest\GroupInterestStoreController@store');

    Route::post('groupUserRooms', 'GroupUserRoom\GroupUserRoomStoreController@store');
    Route::put('groupUserRooms/accept/{group_user_room}', 'GroupUserRoom\GroupUserRoomUpdateController@acceptGroupUser');
    Route::put('groupUserRooms/rejected/{group_user_room}', 'GroupUserRoom\GroupUserRoomUpdateController@rejectGroupUser');
    Route::get('commerces/qr/{commerce}', 'Commerce\CommerceUpdateController@updateQR');
    Route::post('roomMessages', 'MessageRoom\MessageRoomStoreController@store');
    Route::post('groupUserSilents', 'GroupUserSilent\GroupUserSilentStoreController@store');
    Route::get('groupUserRooms','GroupUserRoom\GroupUserRoomIndexController@index');
    Route::get('roomMessages/{group_user_room}','MessageRoom\MessageRoomIndexController@indexRoom');
    Route::get('roomMessages/public/{commerce}','MessageRoom\MessageRoomIndexController@indexCommerce');
    Route::get('roomMessages','MessageRoom\MessageRoomIndexController@getMessagesRooms');

    Route::get('products/bank/{commerce}','Product\ProductIndexController@indexBankProducts');
    Route::post('products/bank/{commerce}/{product}','Product\ProductStoreController@storeForBank');
    Route::get('categories/bank','Category\CategoryIndexController@indexCategories');
    Route::delete('groupUserRooms/{group_user_room}','GroupUserRoom\GroupUserRoomDeleteController@delete');
    Route::get('user/groupUsers','GroupUser\GroupUserShowController@show');
    Route::put('groupUsers','GroupUser\GroupUserUpdateController@update');


    Route::get('comments','Comment\CommentIndexController@index');
    Route::post('comments','Comment\CommentStoreController@store');



    Route::get('roomMessages/show/{message_room}','MessageRoom\MessageRoomShowController@show');
});
