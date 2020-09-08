<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Faker\Generator as Faker;
use  \HamzaDjebiri\Laraveluserstats\Facades\UserStatistics;

class UserStatisticsTest extends TestCase
{

    /**
     *  check if ConnexionStats method of UserStatisticsController work well when using login >> ConnectingUsers++ and 
     *  logout >> ConnectingUsers-- , The other methods uses sql only
     */
    public function testIfNewConnexionStatsSavedWhenLogin()
    {
        $user = \App\User::find(1);
        
        $this->actingAs($user);

        $CountUserConnexionBeforeSavingNewConnexion =  $this->get('/ConnectingUsers')->content();

        $this->assertEquals($CountUserConnexionBeforeSavingNewConnexion , 0 );

        UserStatistics::ConnexionStats('login');
                
        $CountUserConnexionAfterSavingNewConnexion = $this->get('/ConnectingUsers')->content();
        
        $this->assertEquals($CountUserConnexionAfterSavingNewConnexion , 1 );

        UserStatistics::ConnexionStats('logout');
    }
}
