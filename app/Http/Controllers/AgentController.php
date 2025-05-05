<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;
use App\Models\Property;
use App\Models\PropertyMessage;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AgentController extends Controller
{
    public function AgentDashboard(){

        $id = Auth::user()->id;

        // Fetch counts specific to the logged-in agent
        $propertyCount = Property::where('agent_id', $id)->count();

        // Count messages where the property belongs to the agent
        $messageCount = PropertyMessage::whereHas('property', function ($query) use ($id) {
            $query->where('agent_id', $id);
        })->count();

        // Get property statistics by status
        $rentProperties = Property::where('agent_id', $id)->where('property_status', 'rent')->count();
        $buyProperties = Property::where('agent_id', $id)->where('property_status', 'buy')->count();

        // Get property statistics by status (approved, pending, rejected)
        $approvedProperties = Property::where('agent_id', $id)->where('status', 'approved')->count();
        $pendingProperties = Property::where('agent_id', $id)->where('status', 'pending')->count();
        $rejectedProperties = Property::where('agent_id', $id)->where('status', 'rejected')->count();

        // Get recent messages
        $recentMessages = PropertyMessage::whereHas('property', function ($query) use ($id) {
            $query->where('agent_id', $id);
        })->with(['user', 'property'])
          ->latest()
          ->take(5)
          ->get();

        // Get monthly property counts for the chart (last 6 months)
        $monthlyPropertyData = [];
        $monthlyMessageData = [];

        for ($i = 0; $i < 6; $i++) {
            $month = now()->subMonths($i);
            $monthStart = $month->startOfMonth()->format('Y-m-d');
            $monthEnd = $month->endOfMonth()->format('Y-m-d');

            $monthlyPropertyData[$i] = Property::where('agent_id', $id)
                                      ->whereBetween('created_at', [$monthStart, $monthEnd])
                                      ->count();

            $monthlyMessageData[$i] = PropertyMessage::whereHas('property', function ($query) use ($id) {
                                        $query->where('agent_id', $id);
                                      })
                                      ->whereBetween('created_at', [$monthStart, $monthEnd])
                                      ->count();
        }

        // Reverse arrays to show oldest to newest
        $monthlyPropertyData = array_reverse($monthlyPropertyData);
        $monthlyMessageData = array_reverse($monthlyMessageData);

        // Get recent properties
        $recentProperties = Property::where('agent_id', $id)
                            ->with(['type'])
                            ->latest()
                            ->take(7)
                            ->get();

        // Create property status data for chart
        $propertyStatusData = [
            'rent' => $rentProperties,
            'buy' => $buyProperties
        ];

        return view('agent.index', compact(
            'propertyCount',
            'messageCount',
            'rentProperties',
            'buyProperties',
            'approvedProperties',
            'pendingProperties',
            'rejectedProperties',
            'recentMessages',
            'monthlyPropertyData',
            'monthlyMessageData',
            'recentProperties',
            'propertyStatusData'
        ));

    } // End Method


    public function AgentLogin(){

        return view('agent.agent_login');

    } // End Method


    public function AgentRegister(Request $request){


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'agent',
            'status' => 'inactive',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::AGENT);

    }// End Method


    public function AgentLogout(Request $request){
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

         $notification = array(
            'message' => 'Agent Logout Successfully',
            'alert-type' => 'success'
        );

        return redirect('/agent/login')->with($notification);
    }// End Method




    public function AgentProfile(){

        $id = Auth::user()->id;
        $profileData = User::find($id);
        return view('agent.agent_profile_view',compact('profileData'));

     }// End Method


public function AgentProfileStore(Request $request){

        $id = Auth::user()->id;
        $data = User::find($id);
        $data->username = $request->username;
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;

        if ($request->file('photo')) {
            $file = $request->file('photo');
            @unlink(public_path('upload/agent_images/'.$data->photo));
            $filename = date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('upload/agent_images'),$filename);
            $data['photo'] = $filename;
        }

        $data->save();

        $notification = array(
            'message' => 'Agent Profile Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);

     }// End Method


 public function AgentChangePassword(){

        $id = Auth::user()->id;
        $profileData = User::find($id);
        return view('agent.agent_change_password',compact('profileData'));

     }// End Method


       public function AgentUpdatePassword(Request $request){

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


    public function DownloadDashboardReport()
    {
        $id = Auth::user()->id;
        $properties = Property::where('agent_id', $id)->latest()->get();

        $filename = "agent_properties_report_" . date('Y-m-d') . ".csv";

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'. $filename . '"',
        ];

        $callback = function() use ($properties) {
            $handle = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($handle, ['Property Name', 'Code', 'Status', 'City', 'Price']);

            // Add data rows
            foreach ($properties as $prop) {
                 fputcsv($handle, [
                    $prop->property_name ?? '',
                    $prop->property_code ?? '',
                    $prop->property_status ?? '',
                    $prop->city ?? '',
                    $prop->lowest_price ?? '', // Assuming lowest_price exists
                 ]);
            }

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);

    } // End Method

}
