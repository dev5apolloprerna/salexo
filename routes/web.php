<?php

use App\Http\Controllers\HomeController;

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\StateController;
use App\Http\Controllers\Admin\CompanyClientController;
use App\Http\Controllers\admin\JoiningRequestsController;

use App\Http\Controllers\admin\RequestsForJoiningController;
use App\Http\Controllers\Admin\AdminQuotationTemplateController;


use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Company\ApiDataController;
use App\Http\Controllers\Company\CalenderLeadController;
/*-----------------------------------Employee Controller---------------------------------*/
use App\Http\Controllers\Company\CompanyClientLoginController;
use App\Http\Controllers\Company\CompanyClientHomeController;
use App\Http\Controllers\Company\CSVUploadController;
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
use App\Http\Controllers\Company\UdfMasterController;
use App\Http\Controllers\Employee\EmployeeLeadMasterController;
use App\Http\Controllers\RequestForJoiningController;
use App\Http\Controllers\Front\FrontController;
use App\Http\Controllers\RazorpayController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Company\PartyController;
use App\Http\Controllers\Company\QuotationController;
use App\Http\Controllers\Company\QuotationDetailController;
use App\Http\Controllers\Company\QuotationPdfController;

use App\Http\Controllers\Company\QuotationDesignController;
use App\Http\Controllers\Company\QuotationTemplateController;
use App\Http\Controllers\Company\YearController;


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
// Force /login to redirect to /admin-login
Route::redirect('/login', '/admin-login')->name('login');

Route::get('/login', [FrontController::class, 'login'])->name('user_login');
Route::fallback(function () {
    return view('errors.404');
});

Route::middleware('guest:admin')->group(function () {
    Route::get('/admin-login', [AdminLoginController::class, 'showAdminLoginForm'])->name('admin.login');
    Route::post('/admin-login', [AdminLoginController::class, 'adminLogin'])->name('admin.login.post');
    Route::get('/admin-logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
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

//Upload CSV 
Route::prefix('clients/')->name('lead.csvupload.')->middleware(['auth:web_employees'])->group(function () {
    Route::any('/CSV/index', [CSVUploadController::class, 'index'])->name('index');
    Route::any('/CSV/dummyexcel', [CSVUploadController::class, 'dummyexcel'])->name('dummyexcel');
    Route::post('/CSV/store', [CSVUploadController::class, 'create'])->name('store');
});

//Api Data
Route::prefix('clients/')->name('api_data.')->middleware(['auth:web_employees'])->group(function () {
    Route::any('/api/detail/index', [ApiDataController::class, 'index'])->name('index');

    Route::get('/api-docs/indiamart', [ApiDataController::class, 'indiamart'])->name('pdf.indiamart');
    Route::get('/api-docs/general', [ApiDataController::class, 'general'])->name('pdf.general');
});

//Follow Up
Route::prefix('clients')->middleware(['auth:web_employees'])->group(function () {
    Route::any('/todays/followup/index', [FollowUpController::class, 'todays_followup'])->name('clients.todays_followup');
    Route::any('/over/due/index', [FollowUpController::class, 'over_due_followup'])->name('clients.over_due_followup');
    Route::any('/followup/update', [FollowUpController::class, 'followup_update'])->name('clients.followup_update');
    Route::any('/followup/detail/{status}/{id?}', [FollowUpController::class, 'followup_detail'])->name('clients.followup_detail');
    Route::any('/{status?}', [FollowUpController::class, 'new_lead'])->name('clients.new_lead');
});

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

// UDF Master
Route::prefix('clients/udf')->name('udf.')->middleware(['auth:web_employees'])->group(function () {
    Route::any('/index', [UdfMasterController::class, 'index'])->name('index');
    Route::any('/store', [UdfMasterController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [UdfMasterController::class, 'edit'])->name('edit');
    Route::post('/update/{id?}', [UdfMasterController::class, 'update'])->name('update');
    Route::delete('/delete', [UdfMasterController::class, 'delete'])->name('delete');
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
Route::get('/', [FrontController::class, 'index'])->name('front.index');

Route::post('/request_for_demo', [FrontController::class, 'request_for_demo'])->name('front.request_for_demo');

Route::get('/registration', [FrontController::class, 'registration'])->name('front.registration');
Route::post('/registration', [FrontController::class, 'registration_store'])->name('front.registration_store');

Route::any('/privacy/policy', [FrontController::class, 'privacy_policy'])->name('privacy_policy');
Route::any('/term/and/condition', [FrontController::class, 'term_and_condition'])->name('front.term_and_condition');




//payment

Route::post('paysuccess', [RazorpayController::class, 'razorPaySuccess'])->name('razprpay.success');
Route::get('payment/success/{id?}', [RazorpayController::class, 'payment_success'])->name('razorpay.thankyou');
Route::any('payment/cancel/by/user', [RazorpayController::class, 'payment_cancel_by_user'])->name('razorpay.payment_cancel_by_user');
Route::get('payment/fail', [RazorpayController::class, 'RazorFail'])->name('razorpay.RazorFail');
Route::get('thank-you', [RazorpayController::class, 'thank_you'])->name('razorpay.thank_you');




Route::prefix('clients/quotation')->name('quotation.')->middleware(['auth:web_employees'])->group(function () 
{
    Route::get('/quotation', [QuotationController::class, 'index'])->name('index');
    Route::get('/create', [QuotationController::class, 'createview'])->name('create');
    Route::post('/store',       [QuotationController::class, 'create'])->name('store'); // store action
    Route::get('/{id}/edit', [QuotationController::class, 'editview'])->name('edit');
    Route::put('/{id}',      [QuotationController::class, 'update'])->name('update');
    Route::delete('/{id}', [QuotationController::class, 'delete'])->name('delete');
    Route::get('/showdetail/{id}', [QuotationController::class, 'showdetail'])->name('showDetails');
    Route::get('/detail-pdf/{id}', [QuotationController::class, 'detailPDF'])->name('DetailPDF');
    Route::post('/search', [QuotationController::class, 'search'])->name('search');
    Route::get('/mapping/{id}', [QuotationController::class, 'mapping'])->name('mapping');
    Route::get('/termcondition-fetch', [QuotationController::class, 'termconditionFetch'])->name('termconditionFetch');
    Route::get('/copy/{id}', [QuotationController::class, 'copyQuotation'])->name('copy');
    Route::get('/get-next-no/{companyId}', [QuotationController::class, 'getNextQuotationNo'])->name('getNextNo');
    // Route::post('/quotation/{id}/whatsapp', [QuotationController::class, 'sendWhatsApp'])->name('quotation.whatsapp');
    Route::post('/quotation/{id}/send-whatsapp', [QuotationController::class, 'sendWhatsApp'])
    ->name('sendWhatsApp');


})->whereNumber('id')->whereNumber('companyId');





Route::prefix('clients/quotationdetails')->name('quotationdetails.')->middleware('auth:web_employees')->group(function () {
    Route::get('/{getId}', [QuotationDetailController::class, 'index'])->name('index');
    Route::get('/create', [QuotationDetailController::class, 'createview'])->name('create');
    Route::post('/create', [QuotationDetailController::class, 'create'])->name('create');
    // Route::get('quotationdetails/{Id}', [QuotationDetailController::class, 'editview'])->name('edit');
Route::get('/{id}/edit',[QuotationDetailController::class, 'editview'])->name('edit');
    Route::post('/quotationdetails-update/{Id?}', [QuotationDetailController::class, 'update'])->name('update');
    Route::delete('/quotationdetails-delete/{Id}', [QuotationDetailController::class, 'delete'])->name('delete');
Route::get('/services/lookup', [QuotationDetailController::class, 'serviceLookup'])
    ->name('services.lookup');

Route::get('/services/{id}', [QuotationDetailController::class, 'serviceById'])
    ->whereNumber('id')
    ->name('services.byId');

Route::post('/{quotationId}/discount', [QuotationDetailController::class, 'applyDiscount'])->name('applyDiscount');

Route::post('/check-duplicate', [QuotationDetailController::class, 'checkDuplicate'])
    ->name('checkDuplicate');

});


// routes/web.php


Route::prefix('quotationdetails')->name('quotationdetails.')->group(function () {
    Route::get('/productfetch', [QuotationDetailController::class, 'productfetch'])
        ->name('productfetch');
    });

// routes/web.php
Route::middleware(['auth:web_employees'])->group(function () {
  Route::prefix('masters/party')->name('party.')->group(function () {
    Route::get('/',               [PartyController::class, 'index'])->name('index');
    Route::get('/create',         [PartyController::class, 'create'])->name('create');           // NEW
    Route::get('/{party}/edit',   [PartyController::class, 'edit'])->name('edit');               // NEW
    Route::post('/',              [PartyController::class, 'store'])->name('store');
    Route::put('/{party}',        [PartyController::class, 'update'])->name('update');
    Route::delete('/{party}',     [PartyController::class, 'destroy'])->name('destroy');
    Route::post('/bulk-delete',   [PartyController::class, 'bulkDestroy'])->name('bulk-delete');
    Route::patch('/{party}/toggle-status', [PartyController::class, 'toggleStatus'])->name('toggle-status');
    Route::get('/lookup-by-mobile', [PartyController::class, 'lookupByMobile'])->name('lookup-by-mobile');
    Route::get('/lookup-by-name', [PartyController::class, 'lookupByName'])->name('lookup-by-name');
    Route::get('/ajax/parties', [PartyController::class, 'search'])->name('search');

  });
});



Route::middleware(['auth'])->group(function () {
});



Route::prefix('company')->name('company.')->group(function () {
    Route::get('quotations/{id}/preview',  [QuotationPdfController::class, 'preview'])->name('quotations.pdf.preview');
    Route::get('quotations/{id}/pdf',      [QuotationPdfController::class, 'stream'])->name('quotations.pdf.stream');
    Route::get('quotations/{id}/download', [QuotationPdfController::class, 'download'])->name('quotations.pdf.download');
});


/*Route::prefix('company/quotations')->name('company.quotations.')->group(function () {
    // Design picker UI (shows latest quotation in chosen design)
    Route::get('designs',               [QuotationDesignController::class, 'picker'])->name('designs');
    Route::get('latest/preview/{d}',    [QuotationDesignController::class, 'previewLatest'])->name('latest.preview');
    Route::get('latest/pdf/{d}',        [QuotationDesignController::class, 'pdfLatest'])->name('latest.pdf');

    // (Optional) Preview/PDF for a specific quotation id
    Route::get('{id}/preview/{d}',      [QuotationDesignController::class, 'preview'])->name('preview');
    Route::get('{id}/pdf/{d}',          [QuotationDesignController::class, 'pdf'])->name('pdf');
});*/


Route::prefix('admin/quotations')->name('admin.quotations.')->middleware('auth')->group(function () {

    Route::get('/templates', [AdminQuotationTemplateController::class, 'index'])->name('templates');
    Route::get('/templates/new', [AdminQuotationTemplateController::class, 'create'])->name('templates.create');
    Route::post('/templates', [AdminQuotationTemplateController::class, 'store'])->name('templates.store');

    Route::patch('/templates/{template}/toggle', [AdminQuotationTemplateController::class, 'toggle'])->name('templates.toggle');
    Route::patch('/templates/{template}/default', [AdminQuotationTemplateController::class, 'setDefault'])->name('templates.default');
    Route::delete('/templates/{template}', [AdminQuotationTemplateController::class, 'destroy'])->name('templates.destroy');

    // ✅ Preview specific template
    Route::get('/template/preview/{template}/{quotationId}', [AdminQuotationTemplateController::class, 'preview'])
        ->name('templates.preview');

    // ✅ Preview default template
    Route::get('/designs/preview-default', [AdminQuotationTemplateController::class, 'previewDefaultForMyCompany'])
    ->name('designs.previewDefault');
});



Route::prefix('company/quotations')->name('quotations.')->middleware('auth:web_employees')->group(function () {

    Route::get('/templates', [QuotationTemplateController::class, 'index'])->name('templates');
    Route::get('/templates/new', [QuotationTemplateController::class, 'create'])->name('templates.create');
    Route::post('/templates', [QuotationTemplateController::class, 'store'])->name('templates.store');

    Route::patch('/templates/{template}/toggle', [QuotationTemplateController::class, 'toggle'])->name('templates.toggle');
    Route::patch('/templates/{template}/default', [QuotationTemplateController::class, 'setDefault'])->name('templates.default');
    Route::delete('/templates/{template}', [QuotationTemplateController::class, 'destroy'])->name('templates.destroy');

    // ✅ Preview specific template
    Route::get('/template/preview/{template}/{quotationId}', [QuotationTemplateController::class, 'preview'])
        ->name('templates.preview');

    // ✅ Preview default template
    Route::get('/designs/preview-default', [QuotationTemplateController::class, 'previewDefaultForMyCompany'])
    ->name('designs.previewDefault');
});



Route::prefix('company/year')->middleware(['auth:web_employees'])->group(function () {
    Route::get('/year',                 [YearController::class, 'index'])->name('year.index');
    Route::post('/year/store',          [YearController::class, 'store'])->name('year.store');
    Route::post('/year/{year}/update',  [YearController::class, 'update'])->name('year.update');
    Route::delete('/year/{year}',       [YearController::class, 'destroy'])->name('year.destroy');
    Route::post('/year/{year}/status',  [YearController::class, 'toggleStatus'])->name('year.status');
});
