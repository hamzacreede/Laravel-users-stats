<?php

namespace HamzaDjebiri\Laraveluserstats\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User ;
use Carbon\Carbon ;
use HamzaDjebiri\Laraveluserstats\Models\UserConnexion;
use Auth;
use DB;

class UserStatisticsController extends Controller
{
    // Add data to stats
     
    /**
    *  if we have already saved the current user login for today then we need to change the login status of this user on UserConnexion table, 
    *  otherwise if $user_state == 'logout' then we have to find this user by day , month and year because user can log out after days
    *  using 'Remember_me' , if user dont exist on UserConnexion table then we need to create a new row
    */
    public function ConnexionStats($user_state)
    {
       $user_state == 'logout'
       
           ? $user_connexion = UserConnexion::where('user_id',Auth::id())->first()

           : $user_connexion = UserConnexion::where('user_id',Auth::id())
                                            ->where('day_connexion'  ,now()->day  )
                                            ->where('month_connexion',now()->month)
                                            ->where('year_connexion' ,now()->year )
                                            ->first();

       
       $user_connexion
           ? $this->UpdateConnexionStats($user_connexion , $user_state)
           : $this->NewConnexionStats();
    }

    //new Connexion Stats Row
    private function NewConnexionStats()
    {
        $new_data = new UserConnexion();
        $new_data->wording_day     = now()->format('D');
        $new_data->wording_month   = now()->format('F');
        $new_data->day_connexion   = now()->day;
        $new_data->month_connexion = now()->month;
        $new_data->year_connexion  = now()->year;
        $new_data->time_login = now()->toTimeString();
        $new_data->user_id = Auth::id();
        $new_data->save();
    }

    /*  --  push new data to  Connexion Stats Row -- */

    private function UpdateConnexionStats($user_connexion, $user_state)
    {        
        if($user_state == "login")
        {
          /**
           * we don't need to update days ... because if the user logging out the he login again in the same day it's mean we already have 
           * the full date , but if he connect in different day then in this case we need to create a new row
           */
           $user_connexion->time_login = now()->toTimeString();
           $user_connexion->time_logout = null;
           $user_connexion->save();
           
        }elseif($user_state == "logout")
        {
           // if $user_state == "logout" and we have $user_connexion->time_login == null it means we have an exception
           // in this case we should not call FixConnexionTimeValue() with null value for time_login so we put connexion_time with 0 value
           $user_connexion->time_login == null 
             ? $connexion_time = 0 
             : $connexion_time = $this->FixConnexionTimeValue($user_connexion->connexion_time ,  $user_connexion->time_login);

           $user_connexion->time_login = null; // we use time_login to count connexion_time
           $user_connexion->time_logout = now()->toTimeString(); // if null == state >> online , else  >> offline
           $user_connexion->connexion_time = $connexion_time;
           $user_connexion->save();
        }
    }

    private function FixConnexionTimeValue($connexion_time , $time_login)
    {   
        $time_now   = Carbon::createFromFormat('H:i:s',now()->toTimeString());
         
        $time_login = Carbon::createFromFormat('H:i:s', $time_login);    
        
        $diff_in_minutes = $time_now->diffInMinutes($time_login);

        if($connexion_time < $diff_in_minutes ) $connexion_time = $diff_in_minutes;
           
        return $connexion_time ;
    }


    // get Stats
    public function ConnectingUsers()
    {
        //we should not use the date as a condition because we can have users logged in for several days
        return  UserConnexion::where('time_logout','=',null)->count();
    }

    public function RegistredUsers()
    {        
        return User::count() ;   
    }

    public function ConnexionByDate($option = "day")
    { 
         if($option == "day") return $this->ConnexionByDay() ;
         else if($option == "month") return $this->ConnexionByMonth();
         else if($option == "year") return $this->ConnexionByYear();
    }

    public function ConnexionByDay()
    { 
          return  DB::table('user_connexions')->select('wording_day','day_connexion','month_connexion','year_connexion', DB::raw('count(*) as total'))
                  ->groupBy('day_connexion')
                  ->groupBy('month_connexion')
                  ->groupBy('year_connexion')
                  ->groupBy('wording_day')
                  ->get();
    }

    public function ConnexionByMonth()
    { 
          return  DB::table('user_connexions')->select('wording_month','month_connexion','year_connexion', DB::raw('count(*) as total'))
                  ->groupBy('month_connexion')
                  ->groupBy('year_connexion')
                  ->groupBy('wording_month')
                  ->get();
    }

    public function ConnexionByYear()
    { 
          return  DB::table('user_connexions')->select('year_connexion', DB::raw('count(*) as total'))
                  ->groupBy('year_connexion')
                  ->get();
    }

}