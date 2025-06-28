<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\MultiImage;
use App\Models\Facility;
use App\Models\Amenities;
use App\Models\PropertyType;
use App\Models\User;
use App\Models\PackagePlan;
use Illuminate\Support\Facades\Auth;
use App\Models\PropertyMessage;
use Carbon\Carbon;
use App\Models\State;
use App\Models\Schedule;
use App\Models\UserPropertyView;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Session;

class IndexController extends Controller
{
    public function PropertyDetails($id,$slug){

        $property = Property::findOrFail($id);

        $amenities = $property->amenities_id;
        $property_amen = explode(',',$amenities);

        $multiImage = MultiImage::where('property_id',$id)->get();
        $facility = Facility::where('property_id',$id)->get();

        $this->trackPropertyView($id);

        // Get suggested properties based on state and user history
        $suggestedProperties = $this->getSuggestedProperties($property);

        // If we don't have enough suggested properties, add similar properties by type
        if ($suggestedProperties->count() < 3) {
            $type_id = $property->ptype_id;

            // Get the state object to ensure we have the correct state ID
            $stateObj = null;
            $propertyState = $property->state;

            // If state is numeric, it's likely an ID
            if (is_numeric($propertyState)) {
                $stateObj = State::find($propertyState);
            } else {
                // name bhetni
                $stateObj = State::where('state_name', $propertyState)->first();
            }

            // Build the query with proper state matching
            $query = Property::where('ptype_id', $type_id)
                           ->where('id', '!=', $id)
                           ->where('status', '1')
                           ->whereNotIn('id', $suggestedProperties->pluck('id')->toArray());

        
            if ($stateObj) {
                $query->where(function($q) use ($stateObj) {
                    $q->where('state', $stateObj->id)
                      ->orWhere('state', $stateObj->state_name);
                });
            }

            $similarProperties = $query->orderBy('id', 'DESC')
                                     ->limit(3 - $suggestedProperties->count())
                                     ->get();

          
            $suggestedProperties = $suggestedProperties->concat($similarProperties);
        }

        // Flag to indicate if we have suggestions or not
        $hasSuggestions = $suggestedProperties->count() > 0;

        return view('frontend.property.property_details',compact(
            'property',
            'multiImage',
            'property_amen',
            'facility',
            'suggestedProperties',
            'hasSuggestions'
        ));

    }// End Method

    
    private function trackPropertyView($propertyId)
    {
        // Get current user ID if logged in
        $userId = Auth::check() ? Auth::id() : null;

        // Get or create a session ID for tracking guest users
        $sessionId = Session::get('visitor_session_id');
        if (!$sessionId) {
            $sessionId = Session::getId();
            Session::put('visitor_session_id', $sessionId);
        }

        // Get IP address
        $ipAddress = request()->ip();

        // Check if this user/session has already viewed this property recently (within 24 hours)
        $existingView = UserPropertyView::where('property_id', $propertyId)
            ->where(function($query) use ($userId, $sessionId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })
            ->where('created_at', '>=', Carbon::now()->subHours(24))
            ->first();

        // If no recent view exists, create a new record
        if (!$existingView) {
            UserPropertyView::create([
                'user_id' => $userId,
                'property_id' => $propertyId,
                'session_id' => $sessionId,
                'ip_address' => $ipAddress,
            ]);
        }
    }

    
    private function getSuggestedProperties($currentProperty)
    {
        // Start with an empty collection
        $suggestedProperties = collect();

        // Get the current property's state information
        $propertyState = $currentProperty->state;
        $propertyStateName = $currentProperty->state_name;
        $propertyType = $currentProperty->ptype_id;

        // Get the state object to ensure we have the correct state ID
        $stateObj = null;

        // If state is numeric, it's likely an ID
        if (is_numeric($propertyState)) {
            $stateObj = State::find($propertyState);
        } else {
            // Try to find by name
            $stateObj = State::where('state_name', $propertyState)->first();
        }

        // Build a query to match properties in the same state
        $stateQuery = function($query) use ($propertyState, $propertyStateName, $stateObj) {
            if ($stateObj) {
                // If we found a state object, use its ID and name
                $query->where(function($q) use ($stateObj) {
                    $q->where('state', $stateObj->id)
                      ->orWhere('state', $stateObj->state_name);
                });
            } else {
                // Fallback to using whatever we have
                $query->where(function($q) use ($propertyState, $propertyStateName) {
                    $q->where('state', $propertyState)
                      ->orWhere('state', $propertyStateName);
                });
            }
        };

        // If user is logged in, personalize suggestions
        if (Auth::check()) {
            $userId = Auth::id();

            // 1. First priority: Get properties from the same state that the user has viewed before
            $viewedPropertyIds = UserPropertyView::where('user_id', $userId)
                                               ->orderBy('created_at', 'desc')
                                               ->pluck('property_id')
                                               ->toArray();

            if (!empty($viewedPropertyIds)) {
                // Get properties from the same state that the user has viewed
                $stateViewedProperties = Property::whereIn('id', $viewedPropertyIds)
                                              ->where('id', '!=', $currentProperty->id)
                                              ->where('status', '1')
                                              ->where(function($query) use ($stateQuery) {
                                                  $stateQuery($query);
                                              })
                                              ->orderBy('id', 'DESC')
                                              ->limit(3)
                                              ->get();

                // Add these properties to our collection
                $suggestedProperties = $suggestedProperties->concat($stateViewedProperties);
            }

            // 2. Second priority: Get properties from user's wishlist in the same state
            if ($suggestedProperties->count() < 3) {
                $wishlistPropertyIds = Wishlist::where('user_id', $userId)
                                             ->pluck('property_id')
                                             ->toArray();

                if (!empty($wishlistPropertyIds)) {
                    // Exclude properties we've already added
                    $existingIds = $suggestedProperties->pluck('id')->toArray();

                    $stateWishlistProperties = Property::whereIn('id', $wishlistPropertyIds)
                                                   ->where('id', '!=', $currentProperty->id)
                                                   ->whereNotIn('id', $existingIds)
                                                   ->where('status', '1')
                                                   ->where(function($query) use ($stateQuery) {
                                                       $stateQuery($query);
                                                   })
                                                   ->orderBy('id', 'DESC')
                                                   ->limit(3 - $suggestedProperties->count())
                                                   ->get();

                    // Add these properties to our collection
                    $suggestedProperties = $suggestedProperties->concat($stateWishlistProperties);
                }
            }
        }

        // 3. Third priority: Get properties from the same state with similar characteristics
        if ($suggestedProperties->count() < 3) {
            // Exclude properties we've already added
            $existingIds = $suggestedProperties->pluck('id')->toArray();

            $similarProperties = Property::where('id', '!=', $currentProperty->id)
                                      ->whereNotIn('id', $existingIds)
                                      ->where('status', '1')
                                      ->where(function($query) use ($stateQuery) {
                                          $stateQuery($query);
                                      })
                                      ->where(function($query) use ($currentProperty) {
                                          // Match by bedrooms (same or +/- 1)
                                          $query->whereBetween('bedrooms', [
                                              max(1, $currentProperty->bedrooms - 1),
                                              $currentProperty->bedrooms + 1
                                          ]);
                                      })
                                      ->orderBy('id', 'DESC')
                                      ->limit(3 - $suggestedProperties->count())
                                      ->get();

            // Add these properties to our collection
            $suggestedProperties = $suggestedProperties->concat($similarProperties);
        }

        // 4. Fourth priority: Get properties from the same state and same type
        if ($suggestedProperties->count() < 3) {
            // Exclude properties we've already added
            $existingIds = $suggestedProperties->pluck('id')->toArray();

            $sameTypeProperties = Property::where('ptype_id', $propertyType)
                                       ->where('id', '!=', $currentProperty->id)
                                       ->whereNotIn('id', $existingIds)
                                       ->where('status', '1')
                                       ->where(function($query) use ($stateQuery) {
                                           $stateQuery($query);
                                       })
                                       ->orderBy('id', 'DESC')
                                       ->limit(3 - $suggestedProperties->count())
                                       ->get();

            // Add these properties to our collection
            $suggestedProperties = $suggestedProperties->concat($sameTypeProperties);
        }

        // 5. Last resort: Get any properties from the same state
        if ($suggestedProperties->count() < 3) {
            // Exclude properties we've already added
            $existingIds = $suggestedProperties->pluck('id')->toArray();

            $anyStateProperties = Property::where('id', '!=', $currentProperty->id)
                                       ->whereNotIn('id', $existingIds)
                                       ->where('status', '1')
                                       ->where(function($query) use ($stateQuery) {
                                           $stateQuery($query);
                                       })
                                       ->orderBy('id', 'DESC')
                                       ->limit(3 - $suggestedProperties->count())
                                       ->get();

            // Add these properties to our collection
            $suggestedProperties = $suggestedProperties->concat($anyStateProperties);
        }

        // Ensure we only return up to 3 properties
        return $suggestedProperties->take(3);
    }

    public function PropertyMessage(Request $request){

        $pid = $request->property_id;
        $aid = $request->agent_id;

        if (Auth::check()) {

        PropertyMessage::insert([

            'user_id' => Auth::user()->id,
            'agent_id' => $aid,
            'property_id' => $pid,
            'msg_name' => $request->msg_name,
            'msg_email' => $request->msg_email,
            'msg_phone' => $request->msg_phone,
            'message' => $request->message,
            'created_at' => Carbon::now(),

        ]);

        $notification = array(
            'message' => 'Send Message Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);



        }else{

            $notification = array(
            'message' => 'Plz Login Your Account First',
            'alert-type' => 'error'
        );

        return redirect()->back()->with($notification);
        }

    }// End Method


    public function AgentDetails($id){

        $agent = User::findOrFail($id);
        $property = Property::where('agent_id',$id)->get();
        $featured = Property::where('featured','1')->limit(3)->get();
        $rentproperty = Property::where('property_status','rent')->get();
        $buyproperty = Property::where('property_status','buy')->get();


        return view('frontend.agent.agent_details',compact('agent','property','featured','rentproperty','buyproperty'));

    }// End Method


     public function AgentDetailsMessage(Request $request){

        $aid = $request->agent_id;

        if (Auth::check()) {

        PropertyMessage::insert([

            'user_id' => Auth::user()->id,
            'agent_id' => $aid,
            'msg_name' => $request->msg_name,
            'msg_email' => $request->msg_email,
            'msg_phone' => $request->msg_phone,
            'message' => $request->message,
            'created_at' => Carbon::now(),

        ]);

        $notification = array(
            'message' => 'Send Message Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);



        }else{

            $notification = array(
            'message' => 'Plz Login Your Account First',
            'alert-type' => 'error'
        );

        return redirect()->back()->with($notification);
        }

    }// End Method


    public function RentProperty(){

        $property = Property::where('status','1')->where('property_status','rent')->paginate(3);

        return view('frontend.property.rent_property',compact('property'));

    }// End Method


    public function BuyProperty(){

        $property = Property::where('status','1')->where('property_status','buy')->get();

        return view('frontend.property.buy_property',compact('property'));

    }// End Method


    public function PropertyType($id){

        $property = Property::where('status','1')->where('ptype_id',$id)->paginate(6);

        $pbread = PropertyType::where('id',$id)->first();

        return view('frontend.property.property_type',compact('property','pbread'));

    }// End Method


    public function StateDetails($id){
        // First try to find the state
        $bstate = State::where('id', $id)->first();

        // Create a query for properties with status 1
        $query = Property::where('status', '1');

        // If we found a state by ID
        if ($bstate) {
            // Look for properties with this state ID
            $query->where(function($q) use ($id, $bstate) {
                $q->where('state', $id)
                  ->orWhere('state', $bstate->state_name);
            });
        } else {
            // Maybe $id is actually a state name
            $bstate = State::where('state_name', $id)->first();

            if ($bstate) {
                // Look for properties with this state ID or name
                $query->where(function($q) use ($id, $bstate) {
                    $q->where('state', $bstate->id)
                      ->orWhere('state', $id);
                });
            } else {
                // Just search by the provided value as is
                $query->where('state', $id);
            }
        }

        // Get the properties
        $property = $query->get();

        return view('frontend.property.state_property', compact('property', 'bstate'));
    }// End Method

    public function BuyPropertySeach(Request $request){

        $request->validate(['search' => 'required']);
        $item = $request->search;
        $sstate = $request->state;
        $stype = $request->ptype_id;
        $min_price = $request->min_price;
        $max_price = $request->max_price;

        // Create a base query
        $query = Property::where('property_name', 'like', '%' . $item . '%')
                         ->where('property_status', 'buy')
                         ->with('type', 'pstate');

        // Handle property type filtering
        if ($stype && $stype != 'All Type') {
            $query->where(function($q) use ($stype) {
                // Check if it's a numeric ID or a type name
                if (is_numeric($stype)) {
                    $q->where('ptype_id', $stype);
                } else {
                    $q->whereHas('type', function($subq) use ($stype) {
                        $subq->where('type_name', 'like', '%' . $stype . '%');
                    });
                }
            });
        }

        // Handle state filtering - check both relationship and direct column
        if ($sstate && $sstate != 'Input location') {
            $query->where(function($q) use ($sstate) {
                // Check if it's a numeric ID or a state name
                if (is_numeric($sstate)) {
                    $q->where('state', $sstate);
                } else {
                    $q->where(function($subq) use ($sstate) {
                        $subq->whereHas('pstate', function($stateq) use ($sstate) {
                            $stateq->where('state_name', 'like', '%' . $sstate . '%');
                        })
                        ->orWhere('state', 'like', '%' . $sstate . '%');
                    });
                }
            });
        }

        // Add price range filters with flexibility (±5% range)
        if ($min_price) {
            // Remove any commas or formatting and ensure it's a number
            $min_price_clean = str_replace(',', '', $min_price);
            if (is_numeric($min_price_clean)) {
                // Calculate 5% lower than the minimum price entered
                $flexible_min_price = floor($min_price_clean * 0.95);
                $query->where('lowest_price', '>=', $flexible_min_price);
            }
        }

        if ($max_price) {
            // Remove any commas or formatting and ensure it's a number
            $max_price_clean = str_replace(',', '', $max_price);
            if (is_numeric($max_price_clean)) {
                // Calculate 5% higher than the maximum price entered
                $flexible_max_price = ceil($max_price_clean * 1.05);
                $query->where('lowest_price', '<=', $flexible_max_price);
            }
        }

        $property = $query->latest()->get();

        // Pass the filter parameters back to the view for maintaining state
        return view('frontend.property.property_search', compact(
            'property',
            'stype',
            'sstate',
            'min_price',
            'max_price'
        ));

    }// End Method


     public function RentPropertySeach(Request $request){

        $request->validate(['search' => 'required']);
        $item = $request->search;
        $sstate = $request->state;
        $stype = $request->ptype_id;
        $min_price = $request->min_price;
        $max_price = $request->max_price;

        // Create a base query
        $query = Property::where('property_name', 'like', '%' . $item . '%')
                         ->where('property_status', 'rent')
                         ->with('type', 'pstate');

        // Handle property type filtering
        if ($stype && $stype != 'All Type') {
            $query->where(function($q) use ($stype) {
                // Check if it's a numeric ID or a type name
                if (is_numeric($stype)) {
                    $q->where('ptype_id', $stype);
                } else {
                    $q->whereHas('type', function($subq) use ($stype) {
                        $subq->where('type_name', 'like', '%' . $stype . '%');
                    });
                }
            });
        }

        // Handle state filtering - check both relationship and direct column
        if ($sstate && $sstate != 'Input location') {
            $query->where(function($q) use ($sstate) {
                // Check if it's a numeric ID or a state name
                if (is_numeric($sstate)) {
                    $q->where('state', $sstate);
                } else {
                    $q->where(function($subq) use ($sstate) {
                        $subq->whereHas('pstate', function($stateq) use ($sstate) {
                            $stateq->where('state_name', 'like', '%' . $sstate . '%');
                        })
                        ->orWhere('state', 'like', '%' . $sstate . '%');
                    });
                }
            });
        }

        // Add price range filters with flexibility (±5% range)
        if ($min_price) {
            // Remove any commas or formatting and ensure it's a number
            $min_price_clean = str_replace(',', '', $min_price);
            if (is_numeric($min_price_clean)) {
                // Calculate 5% lower than the minimum price entered
                $flexible_min_price = floor($min_price_clean * 0.95);
                $query->where('lowest_price', '>=', $flexible_min_price);
            }
        }

        if ($max_price) {
            // Remove any commas or formatting and ensure it's a number
            $max_price_clean = str_replace(',', '', $max_price);
            if (is_numeric($max_price_clean)) {
                // Calculate 5% higher than the maximum price entered
                $flexible_max_price = ceil($max_price_clean * 1.05);
                $query->where('lowest_price', '<=', $flexible_max_price);
            }
        }

        $property = $query->latest()->get();

        // Pass the filter parameters back to the view for maintaining state
        return view('frontend.property.property_search', compact(
            'property',
            'stype',
            'sstate',
            'min_price',
            'max_price'
        ));

    }// End Method



    public function AllPropertySeach(Request $request){
        // Get all filter parameters
        $property_status = $request->property_status;
        $stype = $request->ptype_id;
        $sstate = $request->state;
        $bedrooms = $request->bedrooms;
        $bathrooms = $request->bathrooms;
        $min_price = $request->min_price;
        $max_price = $request->max_price;

        // Create a base query
        $query = Property::where('status', '1')
                         ->with('type', 'pstate');

        // Add property status filter if provided
        if ($property_status) {
            $query->where('property_status', $property_status);
        }

        // Add bedroom filter if provided
        if ($bedrooms) {
            $query->where('bedrooms', $bedrooms);
        }

        // Add bathroom filter if provided
        if ($bathrooms) {
            $query->where('bathrooms', $bathrooms);
        }

        // Add price range filters with flexibility (±5% range)
        if ($min_price) {
            // Remove any commas or formatting and ensure it's a number
            $min_price_clean = str_replace(',', '', $min_price);
            if (is_numeric($min_price_clean)) {
                // Calculate 5% lower than the minimum price entered
                $flexible_min_price = floor($min_price_clean * 0.95);
                $query->where('lowest_price', '>=', $flexible_min_price);
            }
        }

        if ($max_price) {
            // Remove any commas or formatting and ensure it's a number
            $max_price_clean = str_replace(',', '', $max_price);
            if (is_numeric($max_price_clean)) {
                // Calculate 5% higher than the maximum price entered
                $flexible_max_price = ceil($max_price_clean * 1.05);
                $query->where('lowest_price', '<=', $flexible_max_price);
            }
        }

        // Handle property type filtering
        if ($stype && $stype != 'All Type') {
            $query->where(function($q) use ($stype) {
                // Check if it's a numeric ID or a type name
                if (is_numeric($stype)) {
                    $q->where('ptype_id', $stype);
                } else {
                    $q->whereHas('type', function($subq) use ($stype) {
                        $subq->where('type_name', 'like', '%' . $stype . '%');
                    });
                }
            });
        }

        // Handle state filtering - check both relationship and direct column
        if ($sstate && $sstate != 'Input location') {
            $query->where(function($q) use ($sstate) {
                // Check if it's a numeric ID or a state name
                if (is_numeric($sstate)) {
                    $q->where('state', $sstate);
                } else {
                    $q->where(function($subq) use ($sstate) {
                        $subq->whereHas('pstate', function($stateq) use ($sstate) {
                            $stateq->where('state_name', 'like', '%' . $sstate . '%');
                        })
                        ->orWhere('state', 'like', '%' . $sstate . '%');
                    });
                }
            });
        }

        // Get the filtered properties
        $property = $query->latest()->get();

        // Pass the filter parameters back to the view for maintaining state
        return view('frontend.property.property_search', compact(
            'property',
            'property_status',
            'stype',
            'sstate',
            'bedrooms',
            'bathrooms',
            'min_price',
            'max_price'
        ));

    }// End Method


    public function AllPropertyList(){

        $property = Property::where('status','1')->latest()->paginate(6);
        return view('frontend.property.all_property_list',compact('property'));

    }// End Method


    public function AllPropertyTypes(){

        $propertyTypes = PropertyType::orderBy('type_name','ASC')->get();
        return view('frontend.property.all_property_types', compact('propertyTypes'));

    }// End Method

    public function AllAgents(){
        $agents = User::where('status','active')->where('role','agent')->orderBy('id','DESC')->get();
        return view('frontend.agent.all_agents', compact('agents'));
    }// End Method


    public function StoreSchedule(Request $request){

        $aid = $request->agent_id;
        $pid = $request->property_id;

        if (Auth::check()) {

            Schedule::insert([

                'user_id' => Auth::user()->id,
                'property_id' => $pid,
                'agent_id' => $aid,
                'tour_date' => $request->tour_date,
                'tour_time' => $request->tour_time,
                'message' => $request->message,
                'status' => '0', // Use '0' for pending to maintain compatibility
                'created_at' => Carbon::now(),
            ]);

             $notification = array(
            'message' => 'Send Request Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);


        }else{

           $notification = array(
            'message' => 'Plz Login Your Account First',
            'alert-type' => 'error'
        );

        return redirect()->back()->with($notification);

        }

    }// End Method




    public function AllCategories(){
        $propertyTypes = PropertyType::orderBy('type_name','ASC')->get();
        return view('frontend.property.all_property_types', compact('propertyTypes'));
    }// End Method

}