<?php

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

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::group(['prefix' => 'user', 'middleware' => ['auth']], function (){
    Route::get          ('/',                            'UserController@index'                          )->name('page');
    Route::get          ('/get',                         'UserController@get'                            )->name('get');
    Route::post         ('/save',                        'UserController@save'                           )->name('save');
    Route::get          ('/edit/{id}',                   'UserController@edit'                           )->name('reason');
    Route::post         ('/reset-password/{id}',         'UserController@resetPassword'                  )->name('reason');
    Route::post         ('/update/{id}',                 'UserController@update'                         )->name('update');
    Route::get          ('/destroy/{id}',                'UserController@destroy'                        )->name('destroy');
});


Route::group(['prefix' => 'role', 'middleware' => ['auth']], function (){
    Route::get          ('/',                            'RoleController@index'                          )->name('page');
    Route::get          ('/get',                         'RoleController@get'                            )->name('get');
    Route::post         ('/save',                        'RoleController@save'                           )->name('save');
    Route::get          ('/edit/{id}',                   'RoleController@edit'                           )->name('reason');
    Route::post         ('/update/{id}',                 'RoleController@update'                         )->name('update');
    Route::get          ('/destroy/{id}',                'RoleController@destroy'                        )->name('destroy');
});

Route::group(['prefix' => 'calendar', 'middleware' => ['auth']], function (){
    Route::get          ('/',                            'CalendarController@index'                          )->name('page');
    Route::get          ('/get',                         'CalendarController@get'                            )->name('get');
});

Route::group(['prefix' => 'activity_logs', 'middleware' => ['auth']], function (){
    Route::get          ('/',                            'ActivityLogsController@index'                          )->name('page');
    Route::get          ('/get',                         'ActivityLogsController@get'                            )->name('get');
    Route::post         ('/save',                        'ActivityLogsController@save'                           )->name('save');
});

Route::group(['prefix' => 'source', 'middleware' => ['auth']], function (){
    Route::get          ('/',                            'SourceController@index'                          )->name('page');
    Route::get          ('/get',                         'SourceController@get'                            )->name('get');
    Route::post         ('/save',                        'SourceController@save'                           )->name('save');
    Route::get          ('/edit/{id}',                   'SourceController@edit'                           )->name('reason');
    Route::post         ('/update/{id}',                 'SourceController@update'                         )->name('update');
    Route::get          ('/destroy/{id}',                'SourceController@destroy'                        )->name('destroy');
});

Route::group(['prefix' => 'sales_associate', 'middleware' => ['auth']], function (){
    Route::get          ('/',                            'SalesAssociateController@index'                          )->name('page');
    Route::get          ('/get',                         'SalesAssociateController@get'                            )->name('get');
    Route::post         ('/save',                        'SalesAssociateController@save'                           )->name('save');
    Route::get          ('/edit/{id}',                   'SalesAssociateController@edit'                           )->name('reason');
    Route::post         ('/update/{id}',                 'SalesAssociateController@update'                         )->name('update');
    Route::get          ('/destroy/{id}',                'SalesAssociateController@destroy'                        )->name('destroy');
});

Route::group(['prefix' => 'merchandiser', 'middleware' => ['auth']], function (){
    Route::get          ('/',                            'MerchandiserController@index'                          )->name('page');
    Route::get          ('/get',                         'MerchandiserController@get'                            )->name('get');
    Route::post         ('/save',                        'MerchandiserController@save'                           )->name('save');
    Route::get          ('/edit/{id}',                   'MerchandiserController@edit'                           )->name('reason');
    Route::post         ('/update/{id}',                 'MerchandiserController@update'                         )->name('update');
    Route::get          ('/destroy/{id}',                'MerchandiserController@destroy'                        )->name('destroy');
});

Route::group(['prefix' => 'branch', 'middleware' => ['auth']], function (){
    // Route::get          ('/',                            'BranchController@index'                          )->name('page');
    Route::get          ('/get/{id}',                    'BranchController@get'                            )->name('get');
    Route::post         ('/save',                        'BranchController@save'                           )->name('save');
    Route::get          ('/edit/{id}',                   'BranchController@edit'                           )->name('reason');
    Route::post         ('/update/{id}',                 'BranchController@update'                         )->name('update');
    Route::get          ('/destroy/{id}',                'BranchController@destroy'                        )->name('destroy');
    Route::get          ('/get_list/{id}',               'BranchController@get_list'                        )->name('destroy');
});

Route::group(['prefix' => 'brand', 'middleware' => ['auth']], function (){
    Route::get          ('/',                            'BrandController@index'                          )->name('page');
    Route::get          ('/get',                         'BrandController@get'                            )->name('get');
    Route::post         ('/save',                        'BrandController@save'                           )->name('save');
    Route::get          ('/edit/{id}',                   'BrandController@edit'                           )->name('reason');
    Route::post         ('/update/{id}',                 'BrandController@update'                         )->name('update');
    Route::get          ('/destroy/{id}',                'BrandController@destroy'                        )->name('destroy');
});

Route::group(['prefix' => 'personnel', 'middleware' => ['auth']], function (){
    Route::get          ('/',                            'PersonnelController@index'                          )->name('page');
    Route::get          ('/get',                         'PersonnelController@get'                            )->name('get');
    Route::post         ('/save',                        'PersonnelController@save'                           )->name('save');
    Route::get          ('/edit/{id}',                   'PersonnelController@edit'                           )->name('reason');
    Route::post         ('/update/{id}',                 'PersonnelController@update'                         )->name('update');
    Route::get          ('/destroy/{id}',                'PersonnelController@destroy'                        )->name('destroy');
});

Route::group(['prefix' => 'division', 'middleware' => ['auth']], function (){
    Route::get          ('/',                            'DivisionController@index'                          )->name('page');
    Route::get          ('/get',                         'DivisionController@get'                            )->name('get');
    Route::post         ('/save',                        'DivisionController@save'                           )->name('save');
    Route::get          ('/edit/{id}',                   'DivisionController@edit'                           )->name('reason');
    Route::post         ('/update/{id}',                 'DivisionController@update'                         )->name('update');
    Route::get          ('/destroy/{id}',                'DivisionController@destroy'                        )->name('destroy');
});

Route::group(['prefix' => 'province', 'middleware' => ['auth']], function (){
    Route::get          ('/',                            'ProvinceNameController@index'                          )->name('page');
    Route::get          ('/get',                         'ProvinceNameController@get'                            )->name('get');
    Route::post         ('/save',                        'ProvinceNameController@save'                           )->name('save');
    Route::get          ('/edit/{id}',                   'ProvinceNameController@edit'                           )->name('reason');
    Route::post         ('/update/{id}',                 'ProvinceNameController@update'                         )->name('update');
    Route::get          ('/destroy/{id}',                'ProvinceNameController@destroy'                        )->name('destroy');
});

Route::group(['prefix' => 'company', 'middleware' => ['auth']], function (){
    Route::get          ('/',                            'CompanyController@index'                          )->name('page');
    Route::get          ('/get',                         'CompanyController@get'                            )->name('get');
    Route::post         ('/save',                        'CompanyController@save'                           )->name('save');
    Route::get          ('/edit/{id}',                   'CompanyController@edit'                           )->name('reason');
    Route::post         ('/update/{id}',                 'CompanyController@update'                         )->name('update');
    Route::get          ('/destroy/{id}',                'CompanyController@destroy'                        )->name('destroy');
});

Route::group(['prefix' => 'item', 'middleware' => ['auth']], function (){
    Route::get          ('/',                            'ItemController@index'                          )->name('page');
    Route::get          ('/get',                         'ItemController@get'                            )->name('get');
    Route::post         ('/save',                        'ItemController@save'                           )->name('save');
    Route::get          ('/edit/{id}',                   'ItemController@edit'                           )->name('reason');
    Route::post         ('/update/{id}',                 'ItemController@update'                         )->name('update');
    Route::get          ('/destroy/{id}',                'ItemController@destroy'                        )->name('destroy');
    Route::get          ('/generate_duration',           'ItemController@generate_duration'                        )->name('destroy');
});

Route::group(['prefix' => 'item_duration', 'middleware' => ['auth']], function (){
    Route::get          ('/item/{id}',                   'ItemDurationController@item'                            )->name('get');
    Route::post         ('/save',                        'ItemDurationController@save'                           )->name('save');
    Route::get          ('/edit/{id}',                   'ItemDurationController@edit'                           )->name('reason');
    Route::post         ('/update/{id}',                 'ItemDurationController@update'                         )->name('update');
    Route::get          ('/destroy/{id}',                'ItemDurationController@destroy'                        )->name('destroy');
});

Route::group(['prefix' => 'sale', 'middleware' => ['auth']], function (){
    Route::get          ('/',                            'SaleController@index'                          )->name('page');
    Route::get          ('/get',                         'SaleController@get'                            )->name('get');
    Route::post         ('/save',                        'SaleController@save'                           )->name('save');
    Route::get          ('/edit/{id}',                   'SaleController@edit'                           )->name('reason');
    Route::post         ('/update/{id}',                 'SaleController@update'                         )->name('update');
    Route::get          ('/destroy/{id}',                'SaleController@destroy'                        )->name('destroy');
    Route::post         ('/filter',                      'SaleController@filter'                         )->name('filter');
});

Route::group(['prefix' => 'otc', 'middleware' => ['auth']], function (){
    Route::get          ('/',                            'OtcSalesController@index'                          )->name('page');
    Route::get          ('/get',                         'OtcSalesController@get'                            )->name('get');
    Route::post         ('/save',                        'OtcSalesController@save'                           )->name('save');
    Route::get          ('/edit/{id}',                   'OtcSalesController@edit'                           )->name('reason');
    Route::post         ('/update/{id}',                 'OtcSalesController@update'                         )->name('update');
    Route::get          ('/destroy/{id}',                'OtcSalesController@destroy'                        )->name('destroy');
    Route::post         ('/filter',                      'OtcSalesController@filter'                         )->name('filter');
    Route::get          ('/dashboard',                   'OtcSalesController@dashboard'                      )->name('page');
    Route::post         ('/dashboard/filtered',          'OtcSalesController@getFilteredDashboard'           )->name('data');
    Route::post         ('/dashboard/filterBy/{type}',   'OtcSalesController@getFilterBy'                    )->name('data');
    Route::get          ('/getDashboard',                'OtcSalesController@getDashboard'                            )->name('get');
    Route::get          ('/getDashboard_range/{id}/{start}/{end}',  'OtcSalesController@getDashboard_range'                            )->name('get');
});

Route::group(['prefix' => 'csd', 'middleware' => ['auth']], function (){
    Route::get          ('/dashboard',                   'CsdSalesController@dashboard'                      )->name('page');
    Route::post         ('/dashboard/filtered',          'CsdSalesController@getFilteredDashboard'           )->name('data');
    Route::post         ('/dashboard/filterBy/{type}',   'CsdSalesController@getFilterBy'                    )->name('data');
});

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth']], function (){
    Route::get          ('/',                           'GeneralController@dashboard'                      )->name('page');
    Route::post         ('/filtered',                   'GeneralController@getFilteredDashboard'           )->name('data');
    Route::post         ('/filterBy/{type}',            'GeneralController@getFilterBy'                    )->name('data');
});

// Route::group(['prefix' => 'otc', 'middleware' => ['auth']], function (){
//     Route::get          ('/dashboard',                   'SaleController@otcDashboard'                          )->name('page');
// });


Route::group(['prefix' => 'fsd', 'middleware' => ['auth']], function (){
    Route::get          ('/',                            'FSDController@index'                          )->name('page');
    Route::get          ('/dashboard',                   'FSDController@userDashboard'                          )->name('page');
    Route::get          ('/get',                         'FSDController@get'                            )->name('get');
    Route::post         ('/save',                        'FSDController@save'                           )->name('save');
    Route::get          ('/edit/{id}',                   'FSDController@edit'                           )->name('reason');
    Route::post         ('/update/{id}',                 'FSDController@update'                         )->name('update');
    Route::get          ('/destroy/{id}',                'FSDController@destroy'                        )->name('destroy');
    Route::post         ('/filter',                      'FSDController@filter'                         )->name('filter');
    Route::post         ('/upload',                      'SaleAttachmentController@upload'              )->name('upload');
    Route::post         ('/view_attachment',             'SaleAttachmentController@view_attachment'     )->name('view_attachment');
    Route::get          ('/delete_file/{id}',            'SaleAttachmentController@deleteFile'          )->name('delete_file');
});

Route::group(['prefix' => 'asd', 'middleware' => ['auth']], function (){
    Route::get          ('/',                            'ASDSaleController@index'                          )->name('page');
    Route::get          ('/get',                         'ASDSaleController@get'                            )->name('get');
    Route::post         ('/save',                        'ASDSaleController@save'                           )->name('save');
    Route::get          ('/edit/{id}',                   'ASDSaleController@edit'                           )->name('reason');
    Route::post         ('/update/{id}',                 'ASDSaleController@update'                         )->name('update');
    Route::get          ('/destroy/{id}',                'ASDSaleController@destroy'                        )->name('destroy');
    Route::post         ('/filter',                      'ASDSaleController@filter'                         )->name('filter');
    Route::post         ('/upload',                      'ASDSaleController@upload'                         )->name('upload');
    Route::post         ('/view_attachment',             'ASDSaleController@view_attachment'                )->name('view_attachment');
    Route::get          ('/delete_file/{id}',            'ASDSaleController@deleteFile'                     )->name('delete_file');
});

Route::group(['prefix' => 'store', 'middleware' => ['auth']], function (){
    Route::get          ('/',                            'StoreController@index'                          )->name('page');
    Route::get          ('/get',                         'StoreController@get'                            )->name('get');
    Route::get          ('/list/get',                    'StoreController@all_store'                            )->name('get');
    Route::get          ('/list/{id}',                   'StoreController@store_list'                            )->name('get');
    Route::post         ('/save',                        'StoreController@save'                           )->name('save');
    Route::get          ('/edit/{id}',                   'StoreController@edit'                           )->name('reason');
    Route::post         ('/update/{id}',                 'StoreController@update'                         )->name('update');
    Route::get          ('/destroy/{id}',                'StoreController@destroy'                        )->name('destroy');
});

Route::group(['prefix' => 'referral', 'middleware' => ['auth']], function (){
    Route::get          ('/',                            'ReferralController@index'                          )->name('page');
    Route::get          ('/tree',                        'ReferralController@referral_tree'                          )->name('page');
    Route::get          ('/parent',                      'ReferralController@parent'                         )->name('get');
    Route::get          ('/parseJSON/{id}',              'ReferralController@parseJSON'                      )->name('get');
    Route::get          ('/get',                         'ReferralController@get'                            )->name('get');
    Route::post         ('/save',                        'ReferralController@save'                           )->name('save');
    Route::get          ('/edit/{id}',                   'ReferralController@edit'                           )->name('reason');
    Route::post         ('/update/{id}',                 'ReferralController@update'                         )->name('update');
    Route::get          ('/destroy/{id}',                'ReferralController@destroy'                        )->name('destroy');
});

Route::group(['prefix' => 'sale_details', 'middleware' => ['auth']], function (){
    Route::get          ('/get/{id}',                    'SaleDetailController@get'                            )->name('get');
    Route::get          ('/item/{id}',                   'SaleDetailController@item'                            )->name('get');
    Route::post         ('/save',                        'SaleDetailController@save'                           )->name('save');
    Route::get          ('/edit/{id}',                   'SaleDetailController@edit'                           )->name('reason');
    Route::post         ('/update/{id}',                 'SaleDetailController@update'                         )->name('update');
    Route::get          ('/destroy/{id}',                'SaleDetailController@destroy'                        )->name('destroy');
    Route::get          ('/list/{id}',                   'SaleDetailController@list'                           )->name('list');
    Route::get          ('/generate_task',               'SaleDetailController@generate_task'                           )->name('list');
    Route::get          ('/test',               'SaleDetailController@test'                           )->name('list');
});


Route::group(['prefix' => 'otc_details', 'middleware' => ['auth']], function (){
    Route::get          ('/get/{id}',                    'SaleDetailController@get'                            )->name('get');
    Route::get          ('/item/{id}',                   'SaleDetailController@item'                            )->name('get');
    Route::post         ('/save',                        'SaleDetailController@save'                           )->name('save');
    Route::get          ('/edit/{id}',                   'SaleDetailController@edit'                           )->name('reason');
    Route::post         ('/update/{id}',                 'SaleDetailController@update'                         )->name('update');
    Route::get          ('/destroy/{id}',                'SaleDetailController@destroy'                        )->name('destroy');
    Route::get          ('/list/{id}',                   'SaleDetailController@list'                           )->name('list');
    Route::get          ('/generate_task',               'SaleDetailController@generate_task'                           )->name('list');
});

Route::group(['prefix' => 'serial', 'middleware' => ['auth']], function (){
    Route::get          ('/get/{id}',                    'SalesSerialNoController@get'                            )->name('get');
    Route::post          ('/save',                       'SalesSerialNoController@save'                            )->name('save');
});

Route::group(['prefix' => 'telemarketing', 'middleware' => ['auth']], function (){
    Route::get          ('/',                            'TelemarketingController@index'                          )->name('page');
    Route::get          ('/dashboard',                   'TelemarketingController@dashboard'                  )->name('page');
    Route::get          ('/getDashboard',                'TelemarketingController@getDashboard'                            )->name('get');
    Route::get          ('/getDashboard_range/{user}/{start}/{end}',  'TelemarketingController@getDashboard_range'                            )->name('get');
    Route::get          ('/status_range/{id}/{start}/{end}',  'TelemarketingController@status_range'                            )->name('get');
    Route::get          ('/get',                         'TelemarketingController@get'                            )->name('get');
    Route::get          ('/counters',                    'TelemarketingController@counters'                       )->name('counters');
    Route::post         ('/save',                        'TelemarketingController@save'                           )->name('save');
    Route::post         ('/assign',                      'TelemarketingController@assignedTask'                   )->name('save');
    Route::get          ('/edit/{id}',                   'TelemarketingController@edit'                           )->name('reason');
    Route::post         ('/update/{id}',                 'TelemarketingController@update'                         )->name('update');
    Route::get          ('/destroy/{id}',                'TelemarketingController@destroy'                        )->name('destroy');
    Route::get          ('/resetDate/{id}',                'TelemarketingController@resetDate'                        )->name('destroy');
    Route::get          ('/list',                        'TelemarketingController@getList'                        )->name('list');
    Route::post         ('/filter',                      'TelemarketingController@filter'                         )->name('filter');
});



Route::group(['prefix' => 'prospect', 'middleware' => ['auth']], function (){
    Route::get          ('/',                            'ProspectController@index'                          )->name('page');
    Route::get          ('/get',                         'ProspectController@get'                            )->name('get');
    Route::post         ('/save',                        'ProspectController@save'                           )->name('save');
    Route::get          ('/edit/{id}',                   'ProspectController@edit'                           )->name('reason');
    Route::post         ('/update/{id}',                 'ProspectController@update'                         )->name('update');
    Route::get          ('/destroy/{id}',                'ProspectController@destroy'                        )->name('destroy');
});

Route::group(['prefix' => 'engage', 'middleware' => ['auth']], function (){
    Route::get          ('/',                            'EngageController@index'                          )->name('page');
    Route::get          ('/get',                         'EngageController@get'                            )->name('get');
    Route::post         ('/save',                        'EngageController@save'                           )->name('save');
    Route::get          ('/edit/{id}',                   'EngageController@edit'                           )->name('reason');
    Route::post         ('/update/{id}',                 'EngageController@update'                         )->name('update');
    Route::get          ('/destroy/{id}',                'EngageController@destroy'                        )->name('destroy');
});

Route::group(['prefix' => 'acquire', 'middleware' => ['auth']], function (){
    Route::get          ('/',                            'AcquireController@index'                          )->name('page');
    Route::get          ('/get',                         'AcquireController@get'                            )->name('get');
    Route::post         ('/save',                        'AcquireController@save'                           )->name('save');
    Route::get          ('/edit/{id}',                   'AcquireController@edit'                           )->name('reason');
    Route::post         ('/update/{id}',                 'AcquireController@update'                         )->name('update');
    Route::get          ('/destroy/{id}',                'AcquireController@destroy'                        )->name('destroy');
});

Route::group(['prefix' => 'retention', 'middleware' => ['auth']], function (){
    Route::get          ('/',                            'RetentionController@index'                          )->name('page');
    Route::get          ('/get',                         'RetentionController@get'                            )->name('get');
    Route::post         ('/save',                        'RetentionController@save'                           )->name('save');
    Route::get          ('/edit/{id}',                   'RetentionController@edit'                           )->name('reason');
    Route::post         ('/update/{id}',                 'RetentionController@update'                         )->name('update');
    Route::get          ('/destroy/{id}',                'RetentionController@destroy'                        )->name('destroy');
    Route::get          ('/generate_retention',          'RetentionController@generate_retention'                        )->name('destroy');
});


Route::group(['prefix' => 'telemarketing_details', 'middleware' => ['auth']], function (){
    Route::get          ('/get/{id}',                    'TelemarketingDetailController@get'                            )->name('get');
    Route::get          ('/company-pofo/{companyId}',    'TelemarketingDetailController@companyPofo'                    )->name('company_pofo');
    Route::post         ('/save',                        'TelemarketingDetailController@save'                           )->name('save');
    Route::get          ('/edit/{id}',                   'TelemarketingDetailController@edit'                           )->name('reason');
    Route::post         ('/update/{id}',                 'TelemarketingDetailController@update'                         )->name('update');
    Route::get          ('/destroy/{id}',                'TelemarketingDetailController@destroy'                        )->name('destroy');
    Route::get          ('/list/{id}',                   'TelemarketingDetailController@list'                           )->name('list');
});

Route::group(['prefix' => 'home', 'middleware' => ['auth']], function (){
    Route::get          ('/',                            'HomeController@index'                                     )->name('page');
    Route::get          ('/get_record/{date}',           'HomeController@get_record'                                )->name('records');
    Route::post         ('/get_daily',                   'HomeController@get_daily'                                 )->name('daily');
    Route::post         ('/get_calls',                   'HomeController@get_calls'                                 )->name('calls');
    Route::post         ('/get_division',                'HomeController@get_division'                              )->name('division');
    Route::post         ('/get_branch',                  'HomeController@get_branch'                                )->name('branch');
    Route::post         ('/get_agent',                   'HomeController@get_agent'                                 )->name('agent');
    Route::post         ('/get_associate',               'HomeController@get_associate'                             )->name('associate');
    Route::post         ('/get_industry',                'HomeController@get_industry'                              )->name('industry');
    Route::post         ('/get_source',                  'HomeController@get_source'                                )->name('source');
});

Route::group(['prefix' => 'monthly', 'middleware' => ['auth']], function (){
    Route::get          ('/',                            'MonthlyController@index'                                     )->name('page');
    Route::get          ('/get_record/{date}',           'MonthlyController@get_record'                                )->name('records');
    Route::post         ('/get_daily',                   'MonthlyController@get_daily'                                 )->name('daily');
    Route::post         ('/get_calls',                   'MonthlyController@get_calls'                                 )->name('calls');
    Route::post         ('/get_division',                'MonthlyController@get_division'                              )->name('division');
    Route::post         ('/get_branch',                  'MonthlyController@get_branch'                                )->name('branch');
    Route::post         ('/get_agent',                   'MonthlyController@get_agent'                                 )->name('agent');
    Route::post         ('/get_associate',               'MonthlyController@get_associate'                             )->name('associate');
    Route::post         ('/get_industry',                'MonthlyController@get_industry'                              )->name('industry');
    Route::post         ('/get_source',                  'MonthlyController@get_source'                                )->name('source');
});

Route::group(['prefix' => 'reports', 'middleware' => ['auth']], function (){
    Route::get          ('/sales',                       'SalesReportController@index'                                 )->name('sales_report');
    Route::post         ('/sales/data',                  'SalesReportController@data'                                  )->name('sales_report_data');
    Route::post         ('/sales/summary',               'SalesReportController@summary'                               )->name('sales_report_summary');
    Route::get          ('/telemarketing',               'TelemarketingReportController@index'                         )->name('telemarketing_report');
    Route::post         ('/telemarketing/data',          'TelemarketingReportController@data'                          )->name('telemarketing_report_data');
    Route::post         ('/telemarketing/summary',       'TelemarketingReportController@summary'                       )->name('telemarketing_report_summary');
});

Route::group(['prefix' => 'annual', 'middleware' => ['auth']], function (){
    Route::get          ('/',                            'AnnualController@index'                                     )->name('page');
    Route::get          ('/get_record/{date}/{growth}',  'AnnualController@get_record'                                )->name('records');
    Route::post         ('/get_daily',                   'AnnualController@get_daily'                                 )->name('daily');
    Route::post         ('/get_calls',                   'AnnualController@get_calls'                                 )->name('calls');
    Route::post         ('/get_division',                'AnnualController@get_division'                              )->name('division');
    Route::post         ('/get_branch',                  'AnnualController@get_branch'                                )->name('branch');
    Route::post         ('/get_agent',                   'AnnualController@get_agent'                                 )->name('agent');
    Route::post         ('/get_associate',               'AnnualController@get_associate'                             )->name('associate');
    Route::post         ('/get_industry',                'AnnualController@get_industry'                              )->name('industry');
    Route::post         ('/get_source',                  'AnnualController@get_source'                                )->name('source');
});

Route::get('/manual_tagging', 'HomeController@manual_tagging')->name('home');

Route::post('change-password', 'UserController@changepass')->name('change.password');
Route::post('change-photo', 'UserController@changePicture')->name('change.picture');
