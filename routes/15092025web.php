<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\StateController;
use App\Http\Controllers\Admin\CompanyClientController;
use App\Http\Controllers\admin\JoiningRequestsController;

use App\Http\Controllers\admin\RequestsForJoiningController;
use App\Http\Controllers\Company\CalenderLeadController;
/*-----------------------------------Employee Controller---------------------------------*/
use App\Http\Controllers\Company\CompanyClientLoginController;
use App\Http\Controllers\Company\CompanyClientHomeController;
use App\Http\Controllers\Employee\EmployeeCalenderLeadController;
use App\Http\Controllers\Employee\EmployeeHomeController;
use App\Http\Controllers\Company\EmployeeController;
use App\Http\Controllers\Company\FollowUpController;
use App\Http\Controllers\Company\LeadSourceController;
use App\Http\Controllers\Company\LeadCancelReasonController;
use App\Http\Controllers\Company\LeadPipelineController;
use App\Http\Controllers\Company\ServiceController;
use App\Http\Controllers\Company\LeadMasterController;
use App\Http\Controllers\Company\ReportController;
use App\Http\Controllers\Company\RequestsForJoiningListController;
use App\Http\Controllers\Employee\EmployeeLeadMasterController;
use App\Http\Controllers\RequestForJoiningController;
use App\Http\Controllers\Front\FrontController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::fallback(function () {
    return view('errors.404');
});

Route::get('/', function () {
    return redirect()->route('user_login');
});

Route::get('/run-daily-lead-cron', function () {
    Artisan::call('run_daily_lead:cron');
    return response()->json([
        'status' => 'success',
        'message' => 'Daily lead cron executed manually'
    ]);
});

Route::get('/run-employer-cron', function () {
    Artisan::call('employer_followup:cron');

    return 'Employer Followup Cron executed!';
});

Route::get('/run-admin-cron', function () {
    Artisan::call('admin_followup:cron');

    return 'Admin Followup Cron executed!';
});

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    return 'Cache is cleared';
});

Auth::routes(['register' => false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Profile Routes
Route::prefix('profile')->name('profile.')->middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'getProfile'])->name('detail');
    Route::get('/edit', [HomeController::class, 'EditProfile'])->name('EditProfile');
    Route::post('/update', [HomeController::class, 'updateProfile'])->name('update');
    Route::post('/change-password', [HomeController::class, 'changePassword'])->name('change-password');
});

Route::get('logout', [LoginController::class, 'logout'])->name('logout');

// Roles
Route::resource('roles', App\Http\Controllers\RolesController::class);

// Permissions
Route::resource('permissions', App\Http\Controllers\PermissionsController::class);

//Plan
Route::prefix('admin')->name('plan.')->middleware('auth')->group(function () {
    Route::any('plan', [PlanController::class, 'index'])->name('index');
    Route::get('plan/create', [PlanController::class, 'create'])->name('create');
    Route::post('plan/store', [PlanController::class, 'store'])->name('store');
    Route::get('plan/edit/{id?}', [PlanController::class, 'edit'])->name('edit');
    Route::post('plan/update/{id?}', [PlanController::class, 'update'])->name('update');
    Route::delete('plan/delete', [PlanController::class, 'delete'])->name('delete');
});

//State
Route::prefix('admin')->name('state.')->middleware('auth')->group(function () {
    Route::any('state', [StateController::class, 'index'])->name('index');
    Route::any('state/create', [StateController::class, 'create'])->name('create');
    Route::get('/admin/state/edit/{id}', [StateController::class, 'edit'])->name('edit');
    Route::post('state/update/{id?}', [StateController::class, 'update'])->name('update');
    Route::delete('state/delete', [StateController::class, 'delete'])->name('delete');
});

Route::resource('admin/company-client', CompanyClientController::class);

Route::prefix('admin')->name('company-client.')->middleware('auth')->group(function () {
    Route::delete('/{company_client?}', [CompanyClientController::class, 'destroy'])->name('destroy');
    Route::get('/changepassword/{id}', [CompanyClientController::class, 'changepassword'])->name('changepassword');
    Route::post('/updatepassword/{id?}', [CompanyClientController::class, 'updatepassword'])->name('updatepassword');
});

Route::prefix('admin')->name('reports.')->middleware('auth')->group(function () {
    Route::any('subscription', [ReportController::class, 'subscriptionReport'])->name('subscription');
});

//Joining Requests 
Route::prefix('clients/requests/for/joining/')->name('joining_requests.')->middleware('auth')->group(function () {
    Route::any('index', [RequestsForJoiningListController::class, 'index'])->name('index');
});




/*----------------------------------------Employee Admin Route Start------------------------------------- */

Route::get('clients/login', [CompanyClientLoginController::class, 'loginform'])->name('user_login');
Route::post('clients/login', [CompanyClientLoginController::class, 'login'])->name('userLogin');

//Forgot-Password Page
Route::get('clients/forgot/password', [CompanyClientHomeController::class, 'password_forgot'])->name('password_forgot');
Route::post('/password-forgot-submit', [CompanyClientHomeController::class, 'PasswordForgot'])->name('password_forgot_submit');

//New-Password Page
Route::get('clients/home', [CompanyClientHomeController::class, 'index'])->name('userhome');
Route::get('clients/logout', [CompanyClientLoginController::class, 'logout'])->name('empuserlogout');

//New-Password Page
Route::get('reset-password/{token}', [CompanyClientHomeController::class, 'showResetForm'])->name('password.reset');
Route::post('reset-password', [CompanyClientHomeController::class, 'set_new_password_submit'])->name('set_new_password_submit');

Route::prefix('clients')->name('empprofile.')->middleware(['auth:web_employees'])->group(function () {
    Route::get('/userprofile', [CompanyClientHomeController::class, 'getProfile'])->name('employee-detail');
    Route::get('/edit', [CompanyClientHomeController::class, 'EditProfile'])->name('EditProfile');
    Route::post('/update', [CompanyClientHomeController::class, 'updateProfile'])->name('update');
    Route::any('/changePassword', [CompanyClientHomeController::class, 'changePassword'])->name('userchangepassword');
});
Route::get('/test-403', function () {
    abort(403);
});

Route::middleware(['auth:web_employees'])->group(function () {
    Route::resource('clients/employee', EmployeeController::class);
    Route::post('/clients/employee/password-update/{Id?}', [EmployeeController::class, 'passwordupdate'])->name('employee.passwordupdate');
});

Route::prefix('clients')->name('lead-source.')->middleware(['auth:web_employees'])->group(function () {
    Route::any('lead-source', [LeadSourceController::class, 'index'])->name('index');
    Route::any('clients/create', [LeadSourceController::class, 'create'])->name('create');
    Route::get('/admin/lead-source/edit/{id}', [LeadSourceController::class, 'edit'])->name('edit');
    Route::post('lead-source/update/{id?}', [LeadSourceController::class, 'update'])->name('update');
    Route::delete('lead-source/delete', [LeadSourceController::class, 'delete'])->name('delete');
});

Route::prefix('clients')->name('lead-cancel-reason.')->middleware(['auth:web_employees'])->group(function () {

    Route::any('lead-cancel-reason', [LeadCancelReasonController::class, 'index'])->name('index');
    Route::any('lead-cancel-reason/create', [LeadCancelReasonController::class, 'create'])->name('create');
    Route::get('/admin/lead-cancel-reason/edit/{id}', [LeadCancelReasonController::class, 'edit'])->name('edit');
    Route::post('lead-cancel-reason/update/{id?}', [LeadCancelReasonController::class, 'update'])->name('update');
    Route::delete('lead-cancel-reason/delete', [LeadCancelReasonController::class, 'delete'])->name('delete');
});

Route::prefix('clients')->name('lead-pipeline.')->middleware(['auth:web_employees'])->group(function () {
    Route::any('lead-pipeline', [LeadPipelineController::class, 'index'])->name('index');
    Route::any('lead-pipeline/create', [LeadPipelineController::class, 'create'])->name('create');
    Route::get('/admin/lead-pipeline/edit/{id}', [LeadPipelineController::class, 'edit'])->name('edit');
    Route::post('lead-pipeline/update/{id?}', [LeadPipelineController::class, 'update'])->name('update');
    Route::delete('lead-pipeline/delete', [LeadPipelineController::class, 'delete'])->name('delete');
});

//Service (Product)
Route::prefix('clients/service')->name('service.')->middleware(['auth:web_employees'])->group(function () {
    Route::any('/index', [ServiceController::class, 'index'])->name('index');
    Route::any('/store', [ServiceController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [ServiceController::class, 'edit'])->name('edit');
    Route::post('/update', [ServiceController::class, 'update'])->name('update');
    Route::delete('/delete', [ServiceController::class, 'destroy'])->name('destroy');
});

Route::prefix('clients/')->name('leads.')->middleware(['auth:web_employees'])->group(function () {
    // Route::resource('clients/leads', LeadMasterController::class);
    Route::any('/leads/index', [LeadMasterController::class, 'index'])->name('index');
    Route::any('/leads/create', [LeadMasterController::class, 'create'])->name('create');
    Route::any('/leads/store', [LeadMasterController::class, 'store'])->name('store');
    Route::get('/leads/edit/{id?}', [LeadMasterController::class, 'edit'])->name('edit');
    Route::post('/leads/update/{id?}', [LeadMasterController::class, 'update'])->name('update');
    Route::delete('/leads/delete', [LeadMasterController::class, 'destroy'])->name('destroy');

    Route::get('/leads/done', [LeadMasterController::class, 'leads_done'])->name('done');
    Route::get('/leads/cancel', [LeadMasterController::class, 'leads_cancel'])->name('cancel');
    
    Route::any('/leads/history/{status?}/{lead_id?}', [LeadMasterController::class, 'lead_history'])->name('lead_history');
    
    Route::any('/leads/export/to/excel/{search?}/{emp_id?}/{pipeline_id?}/{service_id?}', [LeadMasterController::class, 'export_to_excel'])->name('export_to_excel');
});

//Follow Up
Route::prefix('clients')->middleware(['auth:web_employees'])->group(function () {
    Route::any('/todays/followup/index', [FollowUpController::class, 'todays_followup'])->name('clients.todays_followup');
    Route::any('/over/due/index', [FollowUpController::class, 'over_due_followup'])->name('clients.over_due_followup');
    Route::any('/followup/update', [FollowUpController::class, 'followup_update'])->name('clients.followup_update');
    Route::any('/followup/detail/{status}/{id?}', [FollowUpController::class, 'followup_detail'])->name('clients.followup_detail');
    Route::any('/{status?}', [FollowUpController::class, 'new_lead'])->name('clients.new_lead');
});

//without login (register for joining)
Route::any('/request/for/joining', [RequestForJoiningController::class, 'request_for_joining'])->name('request_for_joining');
Route::any('/request/for/joining/store', [RequestForJoiningController::class, 'request_for_joining_store'])->name('request_for_joining_store');

Route::get('/thank-you', function () {
    return view('request_for_joining.thank_you');
})->name('thank.you');

//Calender  
Route::prefix('clients')->middleware(['auth:web_employees'])->group(function () {
    Route::any('/calender/lead/list', [CalenderLeadController::class, 'index'])->name('clients.calender.index');
    Route::any('/calender/getLeads', [CalenderLeadController::class, 'getLeads'])->name('clients.calender.getLeads');
});

//Reports  
Route::prefix('clients/reports')->middleware(['auth:web_employees'])->group(function () {
    Route::any('/ROI', [ReportController::class, 'roi_report'])->name('clients.reports.roi_report');
    Route::get('/ROI/export', [ReportController::class, 'exportROIReport'])->name('clients.reports.roi_report.export');

    Route::any('/emp/performance', [ReportController::class, 'emp_performance'])->name('clients.reports.emp_performance');
    Route::get('/emp/performance/export', [ReportController::class, 'exportEmpPerformance'])->name('clients.reports.emp_performance.export');


    Route::get('/lead/found/detail/{lead_source_id?}', [ReportController::class, 'lead_found_detail'])->name('clients.reports.lead_found_detail');
    Route::get('/lead/converted/detail/{lead_source_id}', [ReportController::class, 'lead_converted_detail'])->name('clients.reports.lead_converted_detail');

    Route::get('/lead/cancel/analysis/detail/{cancel_reason_id}', [ReportController::class, 'lead_cancel_analysis_detail'])->name('clients.reports.lead_cancel_analysis_detail');

    Route::get('/lead/generated/detail/{emp_id?}', [ReportController::class, 'lead_generated_detail'])->name('clients.reports.lead_generated_detail');
    Route::get('/lead/given/detail/{emp_id?}', [ReportController::class, 'lead_given_detail'])->name('clients.reports.lead_given_detail');
    Route::get('/lead/analysis/detail/{lead_source_id}/{pipeline_id}', [ReportController::class, 'lead_analysis_detail'])->name('clients.reports.lead_analysis_detail');

    Route::any('/lead/analysis', [ReportController::class, 'emp_lead_analysis'])->name('clients.reports.emp_lead_analysis');
    Route::any('/lead/analysis/export', [ReportController::class, 'exportLeadAnalysis'])->name('clients.reports.emp_lead_analysis.export');

    Route::any('/lead/cancel/analysis', [ReportController::class, 'emp_lead_cancel_analysis'])->name('clients.reports.emp_lead_cancel_analysis');
    Route::any('/lead/cancel/analysis/export', [ReportController::class, 'exportLeadCancelAnalysis'])->name('clients.reports.emp_lead_cancel_analysis.export');
});

/*----------------------------------------Employee Admin Route End------------------------------------- */


/*----------------------------------------Employee Route Start------------------------------------- */

//Employee Module Start
Route::prefix('employee')->name('employee.')->middleware(['auth:web_employees'])->group(function () {
    Route::get('/home', [EmployeeHomeController::class, 'index'])->name('home');
    // Route::get('/logout', [CompanyClientLoginController::class, 'logout'])->name('empuserlogout');
    Route::any('/todays/followup/index', [EmployeeHomeController::class, 'todays_followup'])->name('todays_followup');
    Route::any('/over/due/index', [EmployeeHomeController::class, 'over_due_followup'])->name('over_due_followup');

    Route::get('/lead/list', [EmployeeLeadMasterController::class, 'lead_list'])->name('leads.index');
    Route::get('/lead/add', [EmployeeLeadMasterController::class, 'lead_add'])->name('leads.create');
    Route::post('/lead/create', [EmployeeLeadMasterController::class, 'lead_create'])->name('leads.store');
    Route::get('/lead/edit/{id?}', [EmployeeLeadMasterController::class, 'lead_edit'])->name('leads.edit');
    Route::any('/lead/update/{id?}', [EmployeeLeadMasterController::class, 'lead_update'])->name('leads.update');
    Route::any('/lead/delete', [EmployeeLeadMasterController::class, 'lead_delete'])->name('leads.destroy');

    Route::get('/leads/done', [EmployeeLeadMasterController::class, 'leads_done'])->name('leads.done');
    Route::get('/leads/cancel', [EmployeeLeadMasterController::class, 'leads_cancel'])->name('leads.cancel');
    
    Route::any('/leads/history/{status?}/{lead_id?}', [EmployeeLeadMasterController::class, 'lead_history'])->name('lead_history');

    Route::any('/calender/lead/list', [EmployeeCalenderLeadController::class, 'index'])->name('calender.index');
    Route::any('/calender/getLeads', [EmployeeCalenderLeadController::class, 'getLeads'])->name('calender.getLeads');


    Route::any('/followup/update', [EmployeeHomeController::class, 'followup_update'])->name('followup_update');
    Route::any('/followup/detail/{status}/{id?}', [EmployeeHomeController::class, 'followup_detail'])->name('followup_detail');
    Route::any('/{status?}', [EmployeeHomeController::class, 'status'])->name('status');
});

/*----------------------------------------Employee Admin Route End------------------------------------- */

//FRONT START
Route::any('/privacy/policy', [FrontController::class, 'privacy_policy'])->name('privacy_policy');
