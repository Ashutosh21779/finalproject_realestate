<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Schedule;
use App\Models\Property;
use App\Models\Wishlist;
use App\Models\Compare;
class UserController extends Controller
{
    public function Index(){

        $agents = User::where('status','active')->where('role','agent')->orderBy('id','DESC')->limit(5)->get();
        return view('frontend.index', compact('agents'));

    } // End Method

    public function UserDashboard(){
        $id = Auth::user()->id;
        $userData = User::find($id);

        // Get schedule requests for the user
        $scheduleCount = Schedule::where('user_id', $id)->count();

        // Get property statistics by status for the user's schedule requests
        $approvedSchedules = Schedule::where('user_id', $id)
                            ->where(function($query) {
                                $query->where('status', 'approved')
                                      ->orWhere('status', '1');
                            })->count();

        $pendingSchedules = Schedule::where('user_id', $id)
                            ->where(function($query) {
                                $query->where('status', 'pending')
                                      ->orWhere('status', '0');
                            })->count();

        $rejectedSchedules = Schedule::where('user_id', $id)
                            ->where(function($query) {
                                $query->where('status', 'rejected')
                                      ->orWhere('status', '2');
                            })->count();

        // Get wishlist count
        $wishlistCount = Wishlist::where('user_id', $id)->count();

        // Get compare count
        $compareCount = Compare::where('user_id', $id)->count();

        // Get recent schedule requests
        $recentSchedules = Schedule::where('user_id', $id)
                            ->with(['property', 'property.user'])
                            ->latest()
                            ->take(5)
                            ->get();

        return view('dashboard', compact(
            'userData',
            'scheduleCount',
            'approvedSchedules',
            'pendingSchedules',
            'rejectedSchedules',
            'wishlistCount',
            'compareCount',
            'recentSchedules'
        ));
    } // End Method


    public function UserProfile(){

        $id = Auth::user()->id;
        $userData = User::find($id);
        return view('frontend.dashboard.edit_profile',compact('userData'));

    } // End Method


    public function UserProfileStore(Request $request){

        $id = Auth::user()->id;
        $data = User::find($id);
        $data->username = $request->username;
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;

        if ($request->file('photo')) {
            $file = $request->file('photo');
            @unlink(public_path('upload/user_images/'.$data->photo));
            $filename = date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('upload/user_images'),$filename);
            $data['photo'] = $filename;
        }

        $data->save();

        $notification = array(
            'message' => 'User Profile Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);

     }// End Method


    public function UserChangePassword(){

        return view('frontend.dashboard.change_password');

    }// End Method


    public function UserPasswordUpdate(Request $request){

        // Validation
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed'

        ]);

        /// Match The Old Password

        if (!Hash::check($request->old_password, auth::user()->password)) {

           $notification = array(
            'message' => 'Old Password Does not Match!',
            'alert-type' => 'error'
        );

        return back()->with($notification);
        }

        /// Update The New Password

        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->new_password)

        ]);

         $notification = array(
            'message' => 'Password Change Successfully',
            'alert-type' => 'success'
        );

        return back()->with($notification);

     }// End Method


       public function UserScheduleRequest(){

        $id = Auth::user()->id;
        $userData = User::find($id);

        $srequest = Schedule::where('user_id',$id)->get();
        return view('frontend.message.schedule_request',compact('userData','srequest'));

    } // End Method

}
