<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation\Reservation;

use Auth;


class UsersController extends Controller
{
    

    public function bookings()
    {
        $bookings= Reservation::where('user_id', Auth::user()->id)
        ->orderBy('id','desc')->get();

        return view('users.bookings',compact('bookings'));
    }
}
