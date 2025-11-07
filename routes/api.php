<?php

use App\Http\Controllers\Api\EmployeeApiController;
use App\Http\Controllers\Api\EmployeeMasterApiController;
use App\Http\Controllers\Api\LeadCancelReasonApiController;
use App\Http\Controllers\Api\LeadPipelineApiController;
use App\Http\Controllers\Api\LeadServiceApiController;
use App\Http\Controllers\Api\LeadSourceApiController;
use App\Http\Controllers\Api\MetaIntegrationController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\WebHookController;
use App\Http\Controllers\Api\MetaWebhookController;
use App\Http\Controllers\Api\UDFMasterApiController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\PartyApiController;
use App\Http\Controllers\Api\QuotationApiController;
use App\Http\Controllers\Api\ListingController;
use App\Http\Controllers\Api\QuotationDetailApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    return 'Cache is cleared';
});


Route::prefix('employee')->group(function () {
    Route::post('login', [EmployeeApiController::class, 'login']);

    Route::middleware(['auth:employee_api'])->group(function () {

        // lead-pipeline as status
        Route::post('lead/pipeline/list', [EmployeeApiController::class, 'lead_pipeline']);
        Route::post('lead/cancel/reason/list', [EmployeeApiController::class, 'lead_cancel_reason_list']);
        Route::post('service/list', [EmployeeApiController::class, 'service_list']);
        Route::post('lead/source/list', [EmployeeApiController::class, 'lead_source_list']);
        Route::post('lead/list', [EmployeeApiController::class, 'lead_list']);
        Route::post('lead/detail', [EmployeeApiController::class, 'lead_detail']);
        Route::post('profile/detail', [EmployeeApiController::class, 'profile_detail']);
        Route::post('profile/update', [EmployeeApiController::class, 'profile_update']);

        Route::post('lead/active', [EmployeeApiController::class, 'lead_active']);
        Route::post('lead/done', [EmployeeApiController::class, 'lead_done']);
        Route::post('lead/cancel', [EmployeeApiController::class, 'lead_cancel']);


        Route::post('add/lead', [EmployeeApiController::class, 'add_lead']);
        Route::post('change/password', [EmployeeApiController::class, 'change_password']);
        Route::post('followup/update', [EmployeeApiController::class, 'followup_update']);
        Route::post('/list', [EmployeeApiController::class, 'employee_list']);

        Route::post('/todays/followup/list', [EmployeeApiController::class, 'todays_followup_list']);
        Route::post('/over/due/followup/list', [EmployeeApiController::class, 'over_due_followup_list']);


        Route::post('lead/pipeline/list', [LeadPipelineApiController::class, 'lead_pipeline_list']);
        Route::post('lead/pipeline/create', [LeadPipelineApiController::class, 'lead_pipeline_create']);
        Route::post('lead/pipeline/edit', [LeadPipelineApiController::class, 'lead_pipeline_edit']);
        Route::post('lead/pipeline/update', [LeadPipelineApiController::class, 'lead_pipeline_update']);
        Route::post('lead/pipeline/delete', [LeadPipelineApiController::class, 'lead_pipeline_delete']);

        Route::post('lead/cancel/reason/list', [LeadCancelReasonApiController::class, 'lead_cancel_reason_list']);
        Route::post('lead/cancel/reason/create', [LeadCancelReasonApiController::class, 'lead_cancel_reason_create']);
        Route::post('lead/cancel/reason/edit', [LeadCancelReasonApiController::class, 'lead_cancel_reason_edit']);
        Route::post('lead/cancel/reason/update', [LeadCancelReasonApiController::class, 'lead_cancel_reason_update']);
        Route::post('lead/cancel/reason/delete', [LeadCancelReasonApiController::class, 'lead_cancel_reason_delete']);

        Route::post('lead/source/list', [LeadSourceApiController::class, 'lead_source_list']);
        Route::post('lead/source/create', [LeadSourceApiController::class, 'lead_source_create']);
        Route::post('lead/source/edit', [LeadSourceApiController::class, 'lead_source_edit']);
        Route::post('lead/source/update', [LeadSourceApiController::class, 'lead_source_update']);
        Route::post('lead/source/delete', [LeadSourceApiController::class, 'lead_source_delete']);

        Route::post('service/list', [LeadServiceApiController::class, 'lead_service_list']);
        Route::post('service/create', [LeadServiceApiController::class, 'lead_service_create']);
        Route::post('service/update', [LeadServiceApiController::class, 'lead_service_update']);
        Route::post('service/delete', [LeadServiceApiController::class, 'lead_service_delete']);

        Route::post('master/list', [EmployeeMasterApiController::class, 'employee_list']);
        Route::post('master/create', [EmployeeMasterApiController::class, 'employee_create']);
        Route::post('master/update', [EmployeeMasterApiController::class, 'employee_update']);
        Route::post('master/delete', [EmployeeMasterApiController::class, 'employee_delete']);

        Route::post('/report/roi', [ReportController::class, 'roi_report']);
        Route::post('/report/performance', [ReportController::class, 'emp_performance']);
        Route::post('/report/lead/analysis', [ReportController::class, 'emp_lead_analysis']);
        Route::post('/report/lead/cancel/analysis', [ReportController::class, 'emp_lead_cancel_analysis']);


        Route::post('/lead/found/detail', [ReportController::class, 'lead_found_detail']);
        Route::post('/lead/converted/detail', [ReportController::class, 'lead_converted_detail']);
        Route::post('/lead/cancel/analysis/detail', [ReportController::class, 'lead_cancel_analysis_detail']);

        Route::post('/lead/generated/detail', [ReportController::class, 'lead_generated_detail']);
        Route::post('/lead/given/detail', [ReportController::class, 'lead_given_detail']);
        Route::post('/lead/analysis/detail', [ReportController::class, 'lead_analysis_detail']);

        Route::post('/firebase/device/update', [EmployeeApiController::class, 'firebase_device_update']);

//party 

            Route::post('party', [PartyApiController::class, 'index']);
            Route::post('party/detail',        [PartyApiController::class, 'show']);
            Route::post('party/create',        [PartyApiController::class, 'store']);
            Route::post('party/update',        [PartyApiController::class, 'update']);
            Route::post('party/delete',        [PartyApiController::class, 'destroy']);

            Route::post('/party/search', [PartyApiController::class, 'search']);
            Route::post('/party/lookup/name', [PartyApiController::class, 'lookupByName']);
            Route::post('/party/lookup/mobile', [PartyApiController::class, 'lookupByMobile']);

            Route::post('party/toggle-status', [PartyApiController::class, 'toggleStatus']);




        Route::prefix('quotations')->group(function () {
            Route::post('/',              [QuotationApiController::class, 'index']);
            Route::post('/next-number',   [QuotationApiController::class, 'getNextQuotationNo']);
            Route::post('/create',       [QuotationApiController::class, 'store']);
            Route::post('/{id}/show',          [QuotationApiController::class, 'show']);
            Route::post('/{id}/update',          [QuotationApiController::class, 'update']);
            Route::post('/{id}/delete',       [QuotationApiController::class, 'destroy']);

            Route::post('/{id}/details',  [QuotationApiController::class, 'details']);
            Route::post('/{id}/pdf',      [QuotationApiController::class, 'pdf']);
            Route::post('/{id}/copy',    [QuotationApiController::class, 'copy']);
            Route::post('/{id}/send-whatsapp', [QuotationApiController::class, 'sendWhatsApp']);
        });

         // 07-11

        // Quotation details (scoped to quotation)
        Route::post('/quotations-detail/list', [QuotationDetailApiController::class, 'index']);
        Route::post('/quotations-detail/create', [QuotationDetailApiController::class, 'store']);

        // Single detail by id
        Route::post('/quotation-detail/productshow',  [QuotationDetailApiController::class, 'productshow']);
        Route::post('/quotation-detail/update',  [QuotationDetailApiController::class, 'update']);
        Route::post('/quotation-detail/delete',  [QuotationDetailApiController::class, 'destroy']);


        // 14-10-2025
        Route::post('udf/list', [UDFMasterApiController::class, 'udf_list']);
        Route::post('udf/create', [UDFMasterApiController::class, 'udf_create']);
        Route::post('udf/update', [UDFMasterApiController::class, 'udf_update']);
        Route::post('udf/delete', [UDFMasterApiController::class, 'udf_delete']);


    Route::post('/party-mapping',     [QuotationApiController::class, 'mapping'])->name('api.party.mapping');
    Route::post('/term-conditions',   [QuotationApiController::class, 'termConditions'])->name('api.termconditions.index');

   
    });
});

Route::post('/dropdown-list', [ListingController::class, 'dropdown_list'])->name('api.dropdown.list');


Route::post('forgot/password', [EmployeeApiController::class, 'forgot_password']);
Route::post('otp/verify', [EmployeeApiController::class, 'otp_verify']);
Route::post('set/new/password', [EmployeeApiController::class, 'set_new_password']);

Route::get('/report/download/{token}/{filename}', [ReportController::class, 'downloadExcel'])->name('report.download');

Route::post('/webhook/{guid}', [WebHookController::class, 'web_hook']);
Route::post('/inquiry/{guid}', [WebHookController::class, 'crm_inquiry']);

// 06-10-2025 


Route::get('/meta/webhook', [MetaWebhookController::class, 'verify']);
Route::post('/meta/webhook', [MetaWebhookController::class, 'receive']);
