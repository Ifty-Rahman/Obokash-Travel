<?php

namespace App\Http\Controllers\Traveling;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\City\City;
use App\Models\Country\Country;
use App\Models\Reservation\Reservation;
use Auth;
use Session;
use Redirect;
class TravelingController extends Controller
{
    public function about($id)
    {
        $cities= City::select()->orderBy('id','desc')->take(5)->where('country_id',$id)->get();
        $country= Country:: find($id);
        $citiesCount= City::select()->where('country_id',$id)->count();
        return view('traveling.about',compact('cities','country','citiesCount'));
    }

    public function makeReservations($id)
    {
        $city= City::find($id);
        
        
        return view('traveling.reservation',compact('city'));
    }

    public function storeReservations(Request $request, $id)
    {
        $city= City::find($id);

        if($request->check_in_date > date("Y-m-d")) {

            $totalPrice= (int)$city->price *(int) $request->num_guests;

            $storeReservations= Reservation:: create([
                "name"=>$request->name,
                "phone_number"=>$request->phone_number,
                "num_guests"=>$request->num_guests,
                "check_in_date"=>$request->check_in_date,
                "destination"=>$request->destination,
                "price"=>$totalPrice,
                "user_id"=> $request->user_id,

        ]);
        
        if($storeReservations){

            $price= Session::put('price',$city->price * $request->num_guests);
            $newPrice= Session::get($price);

            return Redirect::route('traveling.pay');
        }

        
        } else{

            echo "Invalid date. You need to choose a date in the future";
        }
        

       // return view('traveling.reservation',compact('city'));
    }

    
    public function payWithPaypal()
    {

        
        
        return view('traveling.pay');
    }

    public function success()
    {

        Session::forget('price');
        return view('traveling.success');
    }


    public function deals(){


        $cities=City::select()->orderBy('id','desc')->take(4)->get();
        $countries = Country::all();
        return view('traveling.deals',compact('cities','countries'));
    }

    
    public function searchDeals(Request $request){


        $country_id= $request->get('country_id');
        $price=$request->get('price');

        $searches= City::where('country_id',$country_id)->where('price','<=',($price))->orderBy('id','desc')->take(4)->get();

        $countries= Country::all();
        return view('traveling.searchdeals',compact('searches','countries'));
    }
}
