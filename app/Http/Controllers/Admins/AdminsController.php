<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City\City;
use App\Models\Country\Country;
use App\Models\Admin\Admin;
use App\Models\Reservation\Reservation;
use Illuminate\Support\Facades\Hash;
use Redirect;
use File;

class AdminsController extends Controller
{
    


    public function viewLogin()
    {
        return view('admins.login');
    }

    public function checkLogin(Request $request)
    {
        $remember_me = $request->has('remember_me') ? true : false;

        if (auth()->guard('admin')->attempt(['email' => $request->input("email"), 'password' => $request->input("password")], $remember_me)) {
            
            return redirect() -> route('admins.dashboard');

        }
        return redirect()->back()->with(['error' => 'error logging in']);

    }

    public function index()
    {

        $countriesCount= Country::select()->count();
        $citiesCount= City::select()->count();
        $adminsCount= Admin::select()->count();

        return view('admins.index', compact('adminsCount', 'citiesCount', 'countriesCount'));
    }

    public function allAdmins()
    {

        $allAdmins= Admin::select()->orderBy('id', 'desc')->get();

        return view('admins.alladmins', compact('allAdmins'));
    }
    public function createAdmins()
    {

        return view('admins.createadmins');

    }
    public function storeAdmins(Request $request)
    {

        $storeAdmins = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if($storeAdmins)
        {
            return Redirect::route('admins.all.admins')->with(['create' => 'Admin created successfully']);
        }

    }
    public function allCountries()
    {

        $allCountries= Country::select()->orderBy('id', 'desc')->get();

        return view('admins.allcountries', compact('allCountries'));
    }
    public function createCountries()
    {

        return view('admins.createcountries');

    }
    public function storeCountries(Request $request)
    {

        Request()->validate([
            'name' => 'required|max:40',
            'population' => 'required|max:40',
            'territory' => 'required|max:40',
            'avg_price' => 'required|max:40',
            'description' => 'required|max:240',
            'image' => 'required|max:5000',
            'continent' => 'required|max:40',
        ]);

        $destinationPath = 'assets/images/';
        $myimage = $request->image->getClientOriginalName();
        $request->image->move(public_path($destinationPath), $myimage);



        $storeCountries = Country::create([
            'name' => $request->name,
            'population' => $request->population,
            'territory' => $request->territory,
            'avg_price' => $request->avg_price,
            'description' => $request->description,
            'image' => $myimage,
            'continent' => $request->continent,
        ]);

        if($storeCountries)
        {
            return Redirect::route('all.countries')->with(['create' => 'Country created successfully']);
        }

    }
    public function deleteCountries($id)
    {
        $deleteCountries = Country::find($id);


        if(File::exists(public_path('assets/images/' . $deleteCountries->image))){
            File::delete(public_path('assets/images/' . $deleteCountries->image));
        }else{
            // dd('File does not exist.');
        }

        $deleteCountries->delete();

        return Redirect::route('all.countries')->with(['delete' => 'Country deleted successfully']);
    }
    public function allCities()
    {

        $cities= City::select()->orderBy('id', 'desc')->get();

        return view('admins.allcities', compact('cities'));

    }
    public function createCities()
    {
        $countries = Country::all();
        return view('admins.createcities', compact('countries'));

    }
    public function storeCities(Request $request)
    {

        Request()->validate([
            'name' => 'required|max:40',
            'price' => 'required|max:40',
            'image' => 'required|max:1000',
            'num_days' => 'required|max:40',
            'country_id' => 'required|max:40',
        ]);

        $destinationPath = 'assets/images/';
        $myimage = $request->image->getClientOriginalName();
        $request->image->move(public_path($destinationPath), $myimage);



        $storeCities = City::create([
            'name' => $request->name,
            'price' => $request->price,
            'image' => $myimage,
            'num_days' => $request->num_days,
            'country_id' => $request->country_id,
        ]);

        if($storeCities)
        {
            return Redirect::route('all.cities')->with(['create' => 'City created successfully']);
        }

    }
    public function deleteCities($id)
    {
        $deleteCities = City::find($id);


        if(File::exists(public_path('assets/images/' . $deleteCities->image))){
            File::delete(public_path('assets/images/' . $deleteCities->image));
        }else{
            // dd('File does not exist.');
        }

        $deleteCities->delete();

        return Redirect::route('all.cities')->with(['delete' => 'City deleted successfully']);
    }
    public function allBookings()
    {

        $allBookings= Reservation::select()->orderBy('id', 'desc')->get();

        return view('admins.allbookings', compact('allBookings'));

    }
    public function editBookings($id)
    {

        $allBooking = Reservation::find($id);

        return view('admins.editbooking', compact('allBooking'));

    }
    public function updateBookings(Request $request, $id)
    {

        $editBooking = Reservation::find($id);

        $editBooking->update($request->all());

        if($editBooking) {
            return Redirect::route('all.bookings')->with(['update' => 'Booking status updated successfully']);}

    }
    public function deleteBookings($id)
    {
        $deleteBooking = Reservation::find($id);

        $deleteBooking->delete();

        if($deleteBooking) {
            return Redirect::route('all.bookings')->with(['delete' => 'Booking deleted successfully']);}
    }
}
