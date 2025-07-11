<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Backend\PropertyTypeController;
use App\Http\Controllers\Backend\PropertyController;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Controllers\Backend\StateController;
use App\Http\Controllers\Backend\TestimonialController;
use App\Http\Controllers\Backend\BlogController;
use App\Http\Controllers\Backend\SettingController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\ChatController;
use App\Http\Controllers\Auth\AuthController;

use App\Http\Controllers\Agent\AgentPropertyController;
use App\Http\Controllers\Frontend\IndexController;
use App\Http\Controllers\Frontend\WishlistController;
use App\Http\Controllers\Frontend\CompareController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

// User Frontend All Route
Route::get('/', [UserController::class, 'Index']);

// NEW UNIFIED AUTHENTICATION ROUTES
Route::controller(AuthController::class)->group(function () {
    Route::get('login', 'showLoginForm')->middleware('guest')->name('login');
    Route::post('login', 'login')->middleware('guest');
    // Registration route remains POST only, handled by the 'Register' tab/form on the login page
    Route::post('register', 'register')->middleware('guest')->name('register');
    Route::post('logout', 'logout')->middleware('auth')->name('logout');
});

Route::get('/dashboard', [UserController::class, 'UserDashboard'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

 Route::get('/user/profile', [UserController::class, 'UserProfile'])->name('user.profile');

  Route::post('/user/profile/store', [UserController::class, 'UserProfileStore'])->name('user.profile.store');

 Route::get('/user/change/password', [UserController::class, 'UserChangePassword'])->name('user.change.password');

  Route::post('/user/password/update', [UserController::class, 'UserPasswordUpdate'])->name('user.password.update');


  Route::get('/user/schedule/request', [UserController::class, 'UserScheduleRequest'])->name('user.schedule.request');

 // Route::get('/live/chat', [UserController::class, 'LiveChat'])->name('live.chat'); // Ensure this line is removed or commented out

 // --- Chat Routes within Auth Middleware ---
  Route::post('/send-message', [ChatController::class, 'SendMsg'])->name('send.msg'); // Keep generic send message
  Route::get('/user-all', [ChatController::class, 'GetAllUsers']); // Keep generic get users
  Route::get('/user-message/{id}', [ChatController::class, 'UserMsgById']); // Keep generic get messages
  Route::post('/mark-message-read', [ChatController::class, 'MarkMessageRead'])->name('mark.message.read'); // Mark message as read
  Route::post('/user-typing', [ChatController::class, 'UserTyping'])->name('user.typing'); // User typing indicator
 // --- End Chat Routes ---

 // User WishlistAll Route
Route::controller(WishlistController::class)->group(function(){

     Route::get('/user/wishlist', 'UserWishlist')->name('user.wishlist');
     Route::get('/get-wishlist-property', 'GetWishlistProperty');
      Route::get('/wishlist-remove/{id}', 'WishlistRemove');


});

 // User Recommendation Routes
Route::controller(\App\Http\Controllers\Frontend\RecommendationController::class)->middleware('auth')->group(function(){
     Route::get('/api/recommendations', 'getRecommendations')->name('api.recommendations');
     Route::post('/api/recommendations/clear-cache', 'clearCache')->name('api.recommendations.clear-cache');
     Route::get('/api/recommendations/stats', 'getStats')->name('api.recommendations.stats');
     Route::get('/api/recommendations/debug-collaborative', 'debugCollaborative')->name('api.recommendations.debug-collaborative');
});


 // User Compare All Route
Route::controller(CompareController::class)->group(function(){

     Route::get('/user/compare', 'UserCompare')->name('user.compare');
     Route::get('/get-compare-property', 'GetCompareProperty');
      Route::get('/compare-remove/{id}', 'CompareRemove');


});




});

// require __DIR__.'/auth.php'; // COMMENT OUT THIS LINE


 /// Admin Group Middleware
Route::middleware(['auth','roles:admin'])->group(function(){

 Route::get('/admin/dashboard', [AdminController::class, 'AdminDashboard'])->name('admin.dashboard');
 Route::get('/admin/dashboard/download-report', [AdminController::class, 'DownloadDashboardReport'])->name('admin.dashboard.download');

 Route::get('/admin/logout', [AdminController::class, 'AdminLogout'])->name('admin.logout');

  Route::get('/admin/profile', [AdminController::class, 'AdminProfile'])->name('admin.profile');

   Route::post('/admin/profile/store', [AdminController::class, 'AdminProfileStore'])->name('admin.profile.store');

   Route::get('/admin/change/password', [AdminController::class, 'AdminChangePassword'])->name('admin.change.password');

   Route::post('/admin/update/password', [AdminController::class, 'AdminUpdatePassword'])->name('admin.update.password');

   // Admin Chat Route
   Route::get('/admin/live/chat', [ChatController::class, 'AdminLiveChat'])->name('admin.live.chat');

}); // End Group Admin Middleware



 /// Agent Group Middleware
Route::middleware(['auth','roles:agent'])->group(function(){

Route::get('/agent/dashboard', [AgentController::class, 'AgentDashboard'])->name('agent.dashboard');
Route::get('/agent/dashboard/download-report', [AgentController::class, 'DownloadDashboardReport'])->name('agent.dashboard.download');

Route::get('/agent/logout', [AgentController::class, 'AgentLogout'])->name('agent.logout');

Route::get('/agent/profile', [AgentController::class, 'AgentProfile'])->name('agent.profile');

Route::post('/agent/profile/store', [AgentController::class, 'AgentProfileStore'])->name('agent.profile.store');

Route::get('/agent/change/password', [AgentController::class, 'AgentChangePassword'])->name('agent.change.password');

Route::post('/agent/update/password', [AgentController::class, 'AgentUpdatePassword'])->name('agent.update.password');

// Agent Live Chat Route
Route::get('/agent/live/chat', [ChatController::class, 'AgentLiveChat'])->name('agent.live.chat');


}); // End Group Agent Middleware

 /// User Group Middleware - Add specific user chat route here
 Route::middleware(['auth','roles:user'])->group(function(){
    // User Live Chat Route
    Route::get('/user/live/chat', [ChatController::class, 'UserLiveChat'])->name('user.live.chat');
    Route::get('/user/chat/property/{propertyId}', [ChatController::class, 'UserPropertyChat'])->name('user.chat.property');
 });




 // Route::get('/admin/login', [AdminController::class, 'AdminLogin'])->name('admin.login')->middleware(RedirectIfAuthenticated::class); // REMOVE THIS LINE



  /// Admin Group Middleware
Route::middleware(['auth','roles:admin'])->group(function(){


 // Property Type All Route
Route::controller(PropertyTypeController::class)->group(function(){

     Route::get('/all/type', 'AllType')->name('all.type')->middleware('permission:all.type');
     Route::get('/add/type', 'AddType')->name('add.type')->middleware('permission:add.type');
     Route::post('/store/type', 'StoreType')->name('store.type');
     Route::get('/edit/type/{id}', 'EditType')->name('edit.type');
     Route::post('/update/type', 'UpdateType')->name('update.type');
     Route::get('/delete/type/{id}', 'DeleteType')->name('delete.type');

});


 // Amenities Type All Route
Route::controller(PropertyTypeController::class)->group(function(){

     Route::get('/all/amenitie', 'AllAmenitie')->name('all.amenitie');
     Route::get('/add/amenitie', 'AddAmenitie')->name('add.amenitie');
     Route::post('/store/amenitie', 'StoreAmenitie')->name('store.amenitie');
     Route::get('/edit/amenitie/{id}', 'EditAmenitie')->name('edit.amenitie');
     Route::post('/update/amenitie', 'UpdateAmenitie')->name('update.amenitie');
     Route::get('/delete/amenitie/{id}', 'DeleteAmenitie')->name('delete.amenitie');

});


 // Property All Route
Route::controller(PropertyController::class)->group(function(){

     Route::get('/all/property', 'AllProperty')->name('all.property');
     Route::get('/add/property', 'AddProperty')->name('add.property');
     Route::post('/store/property', 'StoreProperty')->name('store.property');
     Route::get('/edit/property/{id}', 'EditProperty')->name('edit.property');
     Route::post('/update/property', 'UpdateProperty')->name('update.property');

     Route::post('/update/property/thambnail', 'UpdatePropertyThambnail')->name('update.property.thambnail');

      Route::post('/update/property/multiimage', 'UpdatePropertyMultiimage')->name('update.property.multiimage');

    Route::get('/property/multiimg/delete/{id}', 'PropertyMultiImageDelete')->name('property.multiimg.delete');

    Route::post('/store/new/multiimage', 'StoreNewMultiimage')->name('store.new.multiimage');

     Route::post('/update/property/facilities', 'UpdatePropertyFacilities')->name('update.property.facilities');

     Route::get('/delete/property/{id}', 'DeleteProperty')->name('delete.property');

     Route::get('/details/property/{id}', 'DetailsProperty')->name('details.property');

     Route::post('/inactive/property', 'InactiveProperty')->name('inactive.property');

      Route::post('/active/property', 'ActiveProperty')->name('active.property');

       Route::get('/admin/package/history', 'AdminPackageHistory')->name('admin.package.history');

       Route::get('/package/invoice/{id}', 'PackageInvoice')->name('package.invoice');

       Route::get('/admin/property/message/', 'AdminPropertyMessage')->name('admin.property.message');

});



 // Agent All Route from admin
Route::controller(AdminController::class)->group(function(){

     Route::get('/all/agent', 'AllAgent')->name('all.agent');
     Route::get('/add/agent', 'AddAgent')->name('add.agent');
     Route::post('/store/agent', 'StoreAgent')->name('store.agent');
     Route::get('/edit/agent/{id}', 'EditAgent')->name('edit.agent');
     Route::post('/update/agent', 'UpdateAgent')->name('update.agent');
     Route::get('/delete/agent/{id}', 'DeleteAgent')->name('delete.agent');

     Route::get('/changeStatus', 'changeStatus');

});


 // State  All Route
Route::controller(StateController::class)->group(function(){

     Route::get('/all/state', 'AllState')->name('all.state');
     Route::get('/add/state', 'AddState')->name('add.state');
     Route::post('/store/state', 'StoreState')->name('store.state');
     Route::get('/edit/state/{id}', 'EditState')->name('edit.state');
     Route::post('/update/state', 'UpdateState')->name('update.state');
     Route::get('/delete/state/{id}', 'DeleteState')->name('delete.state');

});


 // Testimonials  All Route
Route::controller(TestimonialController::class)->group(function(){

     Route::get('/all/testimonials', 'AllTestimonials')->name('all.testimonials');
     Route::get('/add/testimonials', 'AddTestimonials')->name('add.testimonials');
     Route::post('/store/testimonials', 'StoreTestimonials')->name('store.testimonials');
     Route::get('/edit/testimonials/{id}', 'EditTestimonials')->name('edit.testimonials');
     Route::post('/update/testimonials', 'UpdateTestimonials')->name('update.testimonials');
     Route::get('/delete/testimonials/{id}', 'DeleteTestimonials')->name('delete.testimonials');

});


 // Blog Cateory All Route
Route::controller(BlogController::class)->group(function(){

     Route::get('/all/blog/category', 'AllBlogCategory')->name('all.blog.category');
     Route::post('/store/blog/category', 'StoreBlogCategory')->name('store.blog.category');
     Route::get('/blog/category/{id}', 'EditBlogCategory');
     Route::post('/update/blog/category', 'UpdateBlogCategory')->name('update.blog.category');
     Route::get('/delete/blog/category/{id}', 'DeleteBlogCategory')->name('delete.blog.category');

});


 // Testimonials  All Route
Route::controller(BlogController::class)->group(function(){

     Route::get('/all/post', 'AllPost')->name('all.post');
     Route::get('/add/post', 'AddPost')->name('add.post');
     Route::post('/store/post', 'StorePost')->name('store.post');
     Route::get('/edit/post/{id}', 'EditPost')->name('edit.post');
     Route::post('/update/post', 'UpdatePost')->name('update.post');
     Route::get('/delete/post/{id}', 'DeletePost')->name('delete.post');

});


 // SMTP Setting  All Route
Route::controller(SettingController::class)->group(function(){

     Route::get('/smtp/setting', 'SmtpSetting')->name('smtp.setting');
     Route::post('/update/smpt/setting', 'UpdateSmtpSetting')->name('update.smpt.setting');

});

 // Site Setting  All Route
Route::controller(SettingController::class)->group(function(){

     Route::get('/site/setting', 'SiteSetting')->name('site.setting');
     Route::post('/update/site/setting', 'UpdateSiteSetting')->name('update.site.setting');

});


 // Permission All Route
Route::controller(RoleController::class)->group(function(){

     Route::get('/all/permission', 'AllPermission')->name('all.permission');
     Route::get('/add/permission', 'AddPermission')->name('add.permission');
     Route::post('/store/permission', 'StorePermission')->name('store.permission');
     Route::get('/edit/permission/{id}', 'EditPermission')->name('edit.permission');
     Route::post('/update/permission', 'UpdatePermission')->name('update.permission');
     Route::get('/delete/permission/{id}', 'DeletePermission')->name('delete.permission');


     Route::get('/import/permission', 'ImportPermission')->name('import.permission');
      Route::get('/export', 'Export')->name('export');
      Route::post('/import', 'Import')->name('import');

});


// Roles All Route
Route::controller(RoleController::class)->group(function(){

     Route::get('/all/roles', 'AllRoles')->name('all.roles');
     Route::get('/add/roles', 'AddRoles')->name('add.roles');
     Route::post('/store/roles', 'StoreRoles')->name('store.roles');
     Route::get('/edit/roles/{id}', 'EditRoles')->name('edit.roles');
     Route::post('/update/roles', 'UpdateRoles')->name('update.roles');
     Route::get('/delete/roles/{id}', 'DeleteRoles')->name('delete.roles');


     Route::get('/add/roles/permission', 'AddRolesPermission')->name('add.roles.permission');
     Route::post('/role/permission/store', 'RolePermissionStore')->name('role.permission.store');

     Route::get('/all/roles/permission', 'AllRolesPermission')->name('all.roles.permission');

     Route::get('/admin/edit/roles/{id}', 'AdminEditRoles')->name('admin.edit.roles');

     Route::post('/admin/roles/update/{id}', 'AdminRolesUpdate')->name('admin.roles.update');

     Route::get('/admin/delete/roles/{id}', 'AdminDeleteRoles')->name('admin.delete.roles');

});


 // Admin User All Route
Route::controller(AdminController::class)->group(function(){

     Route::get('/all/admin', 'AllAdmin')->name('all.admin');
     Route::get('/add/admin', 'AddAdmin')->name('add.admin');
     Route::post('/store/admin', 'StoreAdmin')->name('store.admin');
     Route::get('/edit/admin/{id}', 'EditAdmin')->name('edit.admin');
     Route::post('/update/admin/{id}', 'UpdateAdmin')->name('update.admin');
     Route::get('/delete/admin/{id}', 'DeleteAdmin')->name('delete.admin');

});


}); // End Group Admin Middleware






 /// Agent Group Middleware
Route::middleware(['auth','roles:agent'])->group(function(){

      // Agent All Property
Route::controller(AgentPropertyController::class)->group(function(){

     Route::get('/agent/all/property', 'AgentAllProperty')->name('agent.all.property');
     Route::get('/agent/add/property', 'AgentAddProperty')->name('agent.add.property');

     Route::post('/agent/store/property', 'AgentStoreProperty')->name('agent.store.property');

     Route::get('/agent/edit/property/{id}', 'AgentEditProperty')->name('agent.edit.property');

     Route::post('/agent/update/property', 'AgentUpdateProperty')->name('agent.update.property');

     Route::post('/agent/update/property/thambnail', 'AgentUpdatePropertyThambnail')->name('agent.update.property.thambnail');

     Route::post('/agent/update/property/multiimage', 'AgentUpdatePropertyMultiimage')->name('agent.update.property.multiimage');

     Route::get('/agent/property/multiimg/delete/{id}', 'AgentPropertyMultiimgDelete')->name('agent.property.multiimg.delete');

     Route::post('/agent/store/new/multiimage', 'AgentStoreNewMultiimage')->name('agent.store.new.multiimage');

      Route::post('/agent/update/property/facilities', 'AgentUpdatePropertyFacilities')->name('agent.update.property.facilities');

      Route::get('/agent/details/property/{id}', 'AgentDetailsProperty')->name('agent.details.property');

      Route::get('/agent/delete/property/{id}', 'AgentDeleteProperty')->name('agent.delete.property');

      Route::get('/buy/package', 'BuyPackage')->name('buy.package');
      Route::get('/buy/business/plan', 'BuyBusinessPlan')->name('buy.business.plan');
      Route::get('/buy/professional/plan', 'BuyProfessionalPlan')->name('buy.professional.plan');
      // Add route for Basic plan
      Route::get('/buy/basic/plan', 'BuyBasicPlan')->name('buy.basic.plan');
      // Add POST route for storing Basic Plan activation
      Route::post('/store/basic/plan', 'StoreBasicPlan')->name('store.basic.plan');

      Route::post('/store/business/plan', 'StoreBusinessPlan')->name('store.business.plan');
       Route::post('/store/professional/plan', 'StoreProfessionalPlan')->name('store.professional.plan');

       Route::get('/package/history', 'PackageHistory')->name('package.history');
       Route::get('/agent/package/invoice/{id}', 'AgentPackageInvoice')->name('agent.package.invoice');

       // Agent Property Message Route
       Route::get('/agent/property/message', 'AgentPropertyMessage')->name('agent.property.message');

        Route::get('/agent/message/details/{id}', 'AgentMessageDetails')->name('agent.message.details');

        // Schedule Request Route
        Route::get('/agent/schedule/request/', 'AgentScheduleRequest')->name('agent.schedule.request');

        Route::get('/agent/details/schedule/{id}', 'AgentDetailsSchedule')->name('agent.details.schedule');
         Route::post('/agent/update/schedule/', 'AgentUpdateSchedule')->name('agent.update.schedule');
         Route::post('/agent/reject/schedule/', 'AgentRejectSchedule')->name('agent.reject.schedule');

});


}); // End Group Agent Middleware

// Frontend Property Details All Route

 Route::get('/property/details/{id}/{slug}', [IndexController::class, 'PropertyDetails']);

// Wishlist Add Route
  Route::post('/add-to-wishList/{property_id}', [WishlistController::class, 'AddToWishList']);

  // Compare Add Route
  Route::post('/add-to-compare/{property_id}', [CompareController::class, 'AddToCompare']);

 // Send Message from Property Details Page
   Route::post('/property/message', [IndexController::class, 'PropertyMessage'])->name('property.message');
// Agent Details Page in Frontend
  Route::get('/agent/details/{id}', [IndexController::class, 'AgentDetails'])->name('agent.details');
 // Send Message from Agent Details Page
   Route::post('/agent/details/message', [IndexController::class, 'AgentDetailsMessage'])->name('agent.details.message');

   // Get All Rent Property
   Route::get('/rent/property', [IndexController::class, 'RentProperty'])->name('rent.property');

     // Get All Buy Property
   Route::get('/buy/property', [IndexController::class, 'BuyProperty'])->name('buy.property');

// Get All Property Type Data
 Route::get('/property/type/{id}', [IndexController::class, 'PropertyType'])->name('property.type');

 // Get All Property Types Page
 Route::get('/property/types', [IndexController::class, 'AllPropertyTypes'])->name('all.property.types');

 // Get All Categories Page
 Route::get('/categories', [IndexController::class, 'AllCategories'])->name('all.categories');

 // Get All Property List Page
 Route::get('/properties', [IndexController::class, 'AllPropertyList'])->name('all.property.list');

 // Get All Agents Page
 Route::get('/agents', [IndexController::class, 'AllAgents'])->name('all.agents');

 // Get State Details Data
 Route::get('/state/details/{id}', [IndexController::class, 'StateDetails'])->name('state.details');

  // Home Page Buy Seach Option
   Route::post('/buy/property/search', [IndexController::class, 'BuyPropertySeach'])->name('buy.property.search');

     // Home Page Rent Seach Option
   Route::post('/rent/property/search', [IndexController::class, 'RentPropertySeach'])->name('rent.property.search');

        // All Property Seach Option
   Route::post('/all/property/search', [IndexController::class, 'AllPropertySeach'])->name('all.property.search');


 // Blog Details Route
 Route::get('/blog/details/{slug}', [BlogController::class, 'BlogDetails']);
 Route::get('/blog/cat/list/{id}', [BlogController::class, 'BlogCatList']);
 Route::get('/blog', [BlogController::class, 'BlogList'])->name('blog.list');
  Route::post('/store/comment', [BlogController::class, 'StoreComment'])->name('store.comment');

 Route::get('/admin/blog/comment', [BlogController::class, 'AdminBlogComment'])->name('admin.blog.comment');

  Route::get('/admin/comment/reply/{id}', [BlogController::class, 'AdminCommentReply'])->name('admin.comment.reply');

  Route::post('/reply/message', [BlogController::class, 'ReplyMessage'])->name('reply.message');


  // Schedule Message Request Route
   Route::post('/store/schedule', [IndexController::class, 'StoreSchedule'])->name('store.schedule');

     // Chat Post Request Route
 //  Route::post('/send-message', [ChatController::class, 'SendMsg'])->name('send.msg');

 //  Route::get('/user-all', [ChatController::class, 'GetAllUsers']);

 //  Route::get('/user-message/{id}', [ChatController::class, 'UserMsgById']);

Route::middleware(['auth'])->group(function() {
    // Chat Routes (Common for User/Agent/Admin)
    Route::post('/send-message', [ChatController::class, 'SendMsg'])->name('send.msg');
    Route::get('/user-all', [ChatController::class, 'GetAllUsers'])->name('user.all');
    Route::get('/user-message/{id}', [ChatController::class, 'UserMsgById'])->name('user.message');
    // New direct HTML message loading route
    Route::get('/chat-messages/{id}', [ChatController::class, 'GetChatMessages'])->name('chat.messages');
    // Typing indicator route
    Route::post('/user-typing', [ChatController::class, 'UserTyping'])->name('user.typing');
    // Mark message as read route
    Route::post('/mark-message-read', [ChatController::class, 'MarkMessageRead'])->name('mark.message.read');

    // Debug routes
    Route::get('/debug-agents', function() {
        $controller = new \App\Http\Controllers\Backend\ChatController();
        return $controller->GetAllUsers();
    });

    Route::get('/test-agents', function() {
        $agents = \App\Models\User::whereIn('role', ['agent', 'admin'])
                    ->where('status', 'active')
                    ->orderBy('name')
                    ->get();

        return response()->json([
            'count' => $agents->count(),
            'agents' => $agents
        ]);
    });

    // Test route for chat messages
    Route::get('/test-messages/{id}', function($id) {
        $userId = auth()->id();
        $messages = \App\Models\ChatMessage::where(function($q) use ($id, $userId) {
            $q->where(function($subq) use ($id, $userId) {
                $subq->where('sender_id', $userId);
                $subq->where('receiver_id', $id);
            });
            $q->orWhere(function($subq) use ($id, $userId) {
                $subq->where('sender_id', $id);
                $subq->where('receiver_id', $userId);
            });
        })->orderBy('created_at', 'asc')->get();

        $user = \App\Models\User::find($id);

        return response()->json([
            'user' => $user,
            'messages' => $messages,
            'count' => $messages->count()
        ]);
    });

    // Guaranteed working test route that returns hardcoded agents
    Route::get('/hardcoded-agents', function() {
        return response()->json([
            [
                'id' => 1,
                'name' => 'Test Agent 1',
                'role' => 'agent',
                'status' => 'active',
                'last_message' => null,
                'unread_count' => 0
            ],
            [
                'id' => 2,
                'name' => 'Test Agent 2',
                'role' => 'agent',
                'status' => 'active',
                'last_message' => null,
                'unread_count' => 0
            ],
            [
                'id' => 3,
                'name' => 'Test Admin',
                'role' => 'admin',
                'status' => 'active',
                'last_message' => null,
                'unread_count' => 0
            ]
        ]);
    });

    // Wishlist routes
    Route::get('/user/wishlist', [WishlistController::class, 'UserWishlist'])->name('user.wishlist');
    Route::get('/get-wishlist-property', [WishlistController::class, 'GetWishlistProperty']);
    Route::get('/wishlist-remove/{id}', [WishlistController::class, 'WishlistRemove']);

}); // End Group Auth Middleware for common user functions

