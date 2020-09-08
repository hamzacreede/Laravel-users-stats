<?php

namespace HamzaDjebiri\Laraveluserstats\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon ;
use HamzaDjebiri\Laraveluserstats\Models\StatisticsForPages ;
use Auth;
use DB;

class PageStatisticsController extends Controller
{
     // Add data to stats
     public function PageStats($page_name)
     {  
         $pages_stats= StatisticsForPages::where('page_name',$page_name)
                                   ->where('day_connexion'  ,now()->day )
                                   ->where('month_connexion',now()->month)
                                   ->where('year_connexion' ,now()->year )
                                   ->first() ;
 
         $pages_stats  
              ? $this->UpdatePageStats($pages_stats) 
              : $this->NewPageStats($page_name) ;       
     }
 
     //new Connexion Stats Row
     private function NewPageStats($page_name)
     {
         $new_data = new StatisticsForPages();
         $new_data->wording_day     = now()->format('D');
         $new_data->wording_month   = now()->format('F');
         $new_data->day_connexion   = now()->day;
         $new_data->month_connexion = now()->month;
         $new_data->year_connexion  = now()->year;
         $new_data->number_of_visits_by_date = 1;
         $new_data->page_name = $page_name;
         $new_data->save();
     }
 
     private function UpdatePageStats($pages_stats)
     {
         return $pages_stats->increment('number_of_visits_by_date') ;
     }
 
   
     // get Stats
 
     public function UsersNumbersForeachPage()
     {
         return  DB::table('statistics_for_pages')
                     ->select('page_name','number_of_visits_by_date','day_connexion','month_connexion','year_connexion')
                     ->groupBy('day_connexion')
                     ->groupBy('month_connexion')
                     ->groupBy('year_connexion')
                     ->groupBy('page_name')
                     ->groupBy('number_of_visits_by_date')
                     ->get();
     }
 
     public function WebsiteViewsPerDate($option = "day")
     { 
          if($option == "day") return $this->WebsiteViewsPerDay() ;
          else if($option == "month") return $this->WebsiteViewsPerMonth() ;
          else if($option == "year") return $this->WebsiteViewsPerYear() ;
     }
     
     // max of number_of_visits_by_date of pages per date is the Website Views Per Date
     public function WebsiteViewsPerDay()
     {
         return  DB::table('statistics_for_pages')
                     ->select('wording_day','day_connexion','month_connexion','year_connexion',DB::raw('MAX(number_of_visits_by_date) as website_views_per_date'))
                     ->groupBy('day_connexion')
                     ->groupBy('month_connexion')
                     ->groupBy('year_connexion')
                     ->groupBy('wording_day')
                     ->get();
     }
 
     public function WebsiteViewsPerMonth()
     {
         return  DB::table('statistics_for_pages')
                     ->select('wording_month','month_connexion','year_connexion',DB::raw('MAX(number_of_visits_by_date) as website_views_per_date'))
                     ->groupBy('month_connexion')
                     ->groupBy('year_connexion')
                     ->groupBy('wording_month')
                     ->get();
     }
 
     public function WebsiteViewsPerYear()
     {
         return  DB::table('statistics_for_pages')
                     ->select('year_connexion',DB::raw('MAX(number_of_visits_by_date) as website_views_per_date'))
                     ->groupBy('year_connexion')
                     ->get();
     }
 
     public function TotalWebsiteViews()
     {
         $total_website_views =  DB::select('
                          select sum(website_views_per_date) as total_website_views from 
                          (    
                              select max(number_of_visits_by_date) as website_views_per_date from statistics_for_pages group by (day_connexion) 
                          )as total_website_views
                      ');
 
         return $total_website_views[0]->total_website_views ;              
                          
     }
}