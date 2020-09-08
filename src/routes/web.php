<?php

Route::group(['namespace'=>'HamzaDjebiri\Laraveluserstats\Http\Controllers'],function(){

    //users
    Route::get('/ConnectingUsers','UserStatisticsController@ConnectingUsers');
    Route::get('/RegistredUsers','UserStatisticsController@RegistredUsers');
    Route::get('/ConnexionByDate/{option}','Statistics\UserStatisticsController@ConnexionByDate');
    // pages :
    Route::get('/WebsiteViewsPerDate/{option}','Statistics\PageStatisticsController@WebsiteViewsPerDate');
    Route::get('/UsersNumbersForeachPage','Statistics\PageStatisticsController@UsersNumbersForeachPage');
    Route::get('/TotalWebsiteViews','Statistics\PageStatisticsController@TotalWebsiteViews');

});