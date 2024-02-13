<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApproveController;
use App\Http\Controllers\AttendanceController;

;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CheckinController;
use App\Http\Controllers\CheckinHistoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\CounterController;
use App\Http\Controllers\DepartmentControler;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\LeavetypeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\PayslipController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\QrcodeController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\StructureController;
use App\Http\Controllers\StructuretypeController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\TimetableEmployeeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkdayController;
use App\Models\User;
use BaconQrCode\Encoder\QrCode as EncoderQrCode;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use FontLib\Table\Type\name;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

Auth::routes();
// Auth::routes(['register' => false, 'reset' => false]);
// Route::get('/', function () {

//    return view('welcome');
// })->name('welcome');
// Route::get('/', [LoginController::class,'adminLogin'])->name('adminLogin');


// Route::get('/admin/login', [LoginController::class,'adminLogin'])->name('admin/login');

// Route::get('/', 'AbsenController@index')->name('home');
// Route::get('/attendance/in', 'AbsenController@in')->name('scan.masuk');
// Route::get('/attendance/out', 'AbsenController@out')->name('scan.pulang');
// Route::get('/attendance/in/{id}', 'AbsenController@checkin')->name('in');
// // Route::get('/attendance/out/{id}', 'AbsenController@checkout')->name('out');
// Route::get('upload',function(){
//     return view('upload');
// });
// Route::post('upload',function(Request $request){
//     $uploadedFileUrl = Cloudinary ::upload($request->file('file')->getRealPath(),[
//         'folder'=>'employee'
//     ])->getSecurePath();
//     $data = User::find(2);
//     $data->profile_url = $uploadedFileUrl;
//     $data->update();

// });
// });
Route::get('/admin/login', [LoginController::class, 'adminLogin'])->name('adminLogin');

// Route::get('/login', [LoginController::class,'adminLogin'])->name('login');
Route::get('/', function () {
    
    return redirect('/admin/login');
});
Route::get('/privacy', function () {
    
    return view('admin.privacy.privacy');
});
Route::get('/accountant/login', [LoginController::class, 'accountantLogin'])->name('accountantLogin');
// Route::resource('absen', 'AbsenController');
// Route::get('lang/{lang}', ['as' => 'lang.switch', 'uses' => [LanguageController::class,'switchLang']]);
// ROUTE FOR ADMIN ONLY
Route::name('admin/')->prefix('admin')->middleware(['auth', 'admin', 'active', 'check.session'])->group(function () {

    Route::get('locale/{locale}', function ($locale) {
        Session::put('locale', $locale);
        Session::save();
       
        // App::setLocale($locale);
        return redirect()->back();
    });
   
    // Dashboard
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('reports', [AttendanceController::class, 'index']);
    // Route::get('imports-user', [UserController::class, 'importUsers']);
    Route::post('uploads-user', [UserController::class, 'importUser']);
    Route::get('users', [UserController::class, 'index'])->name('user');
    Route::get('attendances/imports', [AttendanceController::class, 'index'])->name('user');
    Route::post('uploads-attendance', [AttendanceController::class, 'store']);
    Route::get('report/attendances/{userId}/{startDate}/{endDate}/{export} ', [AttendanceController::class, 'attendanceReport']);
    Route::get('report/attendances ', [AttendanceController::class, 'attendance']);

    Route::get('report/attendances/total/{userId}/{startDate}/{endDate}/{export} ', [AttendanceController::class, 'totalAttendance']);

    // Route::get('report/attendances/{startDate}/{endDate}/{export} ', [AdminController::class, 'attendanceReport'])->name('user_report');



    // Route::post('attendances/checkin/{userId}', [AdminController::class, 'checkin']);
    // Route::put('attendances/checkout/{checkinId}', [AdminController::class, 'checkout']);
    Route::get('qr', [AdminController::class, 'qr']);
    Route::get('qrs', [AdminController::class, 'qrs']);
     // holiday
     Route::get('holidays', [HolidayController::class, 'index']);
     Route::post('holidays/store', [HolidayController::class, 'store']);
     Route::delete('holidays/delete/{id}', [HolidayController::class, 'destroy']);
     Route::get('holidays/edit/{id}', [HolidayController::class, 'edit']);
     Route::put('holidays/update/{id}', [HolidayController::class, 'update']);
    //  qr
     Route::get('qrs', [QrcodeController::class, 'index']);
     Route::post('qrs/store', [QrcodeController::class, 'store']);
     Route::delete('qrs/delete/{id}', [QrcodeController::class, 'destroy']);
     Route::get('qrs/edit/{id}', [QrcodeController::class, 'edit']);
     Route::put('qrs/update/{id}', [QrcodeController::class, 'update']);
    // Route::get('imageQr', [QrController::class, 'imageQr']);
    // //    admin report

    // Route::get('reports', function () {
    //     return view('admin.report.report_admin');
    // });
    // Route::get('report/system/pdf', [AdminController::class, 'systemReport'])->name('system/report/pdf');
    // // Route::get('report/employee', [AdminController::class,'employee']);
    // // Route::get('report/employee/pdf', [AdminController::class, 'employeeReport'])->name('system/employee/pdf');
    // // Route::get('card', 'AdminController@card')->name('card');
    // Route::get('report/system', [AdminController::class, 'report'])->name('system_report');
    // // Route::get('report/employee', [AdminController::class,'employee'])->name('user_report');
    // Route::get('reports/attendances', [AdminController::class, 'viewAttendance'])->name('system_report');
    // Route::get('report/attendance/view', [AdminController::class, 'viewAttendanceAll'])->name('system_report');
    // Route::get('report/attendances/{startDate}/{endDate}/{export} ', [AdminController::class, 'attendanceReport'])->name('user_report');
    // Route::get('report/attendance/employee/{id}/{startDate}/{endDate}/{export} ', [AdminController::class, 'attendanceEmployee'])->name('user_report');
    // Route::get('reports/employee', [AdminController::class, 'viewEmployee'])->name('user_report');
    // Route::get('report/employee/{startDate}/{endDate}/{export} ', [AdminController::class, 'employee'])->name('user_report');
    // // send employee to account
    // Route::post('employee/updates', [AdminController::class, 'sendEmployee']);
    // Route::get('report/test', [AdminController::class, 'test'])->name('user_report');
    // Route::get('report/export/{list}', [AdminController::class, 'export'])->name('user_report');
    // Route::get('reports/attendance', [AdminController::class, 'attendanceReport']);
    // Route::get('report/leaves', [AdminController::class, 'leaveView'])->name('user_report');
    // Route::get('report/leaves/{userId}/{startDate}/{endDate}/{export}', [AdminController::class, 'leaveReport'])->name('user_report');
    // Route::get('reports/overtimes', [AdminController::class, 'overtimeView'])->name('user_report');
    // Route::get('report/overtime/{userId}/{startDate}/{endDate}/{export}', [AdminController::class, 'overtimeReport'])->name('user_report');
    // // send to account
    // Route::post('attendance/updates/{user_id}/{from_date}/{to_date}', [AdminController::class, 'sendAttencanetoAccount']);
    // // admn cofirm leave
    // Route::post('leave/updates', [ApproveController::class, 'confirmLeave']);

    // // Data Karyawan
    // Route::get('user', [UserController::class, 'index'])->name('user');
    // Route::get('user/componet', [UserController::class, 'getComponent'])->name('user');
    // Route::get('user/details/{id}', [UserController::class, 'detail'])->name('user');
    // Route::get('user/create', [UserController::class, 'create']);
    // Route::post('user/store', [UserController::class, 'store']);
    // Route::delete('user/delete/{id}', [UserController::class, 'destroy']);
    // Route::get('user/edit/{id}', [UserController::class, 'edit']);
    // Route::post('user/update/{id}', [UserController::class, 'update']);
    // // workday
    // Route::get('datetime', [WorkdayController::class, 'showfdate']);
    // Route::post('datetime/update', [WorkdayController::class, 'updateDate']);
    // Route::get('workday', [WorkdayController::class, 'index']);
    // Route::post('workday/store', [WorkdayController::class, 'store']);
    // Route::delete('workday/delete/{id}', [WorkdayController::class, 'destroy']);
    // Route::get('workday/edit/{id}', [WorkdayController::class, 'edit']);
    // Route::put('workday/update/{id}', [WorkdayController::class, 'update']);
    // location = user
    // Route::get('user', [LocationController::class, 'index']);
    // Route::get('location/componet', [UserController::class, 'getComponent']);
    // Route::post('location/store', [LocationController::class, 'store']);
    // Route::delete('location/delete/{id}', [LocationController::class, 'destroy']);
    // Route::get('location/edit/{id}', [LocationController::class, 'edit']);
    // Route::put('location/update/{id}', [LocationController::class, 'update']);

    // position
    // Route::get('position', [PositionController::class, 'index']);
    // Route::post('position/store', [PositionController::class, 'store']);
    // Route::delete('position/delete/{id}', [PositionController::class, 'destroy']);
    // Route::get('position/edit/{id}', [PositionController::class, 'edit']);
    // Route::put('position/update/{id}', [PositionController::class, 'update']);
    // holiday
    // Route::get('holiday', [HolidayController::class, 'index']);
    // Route::post('holiday/store', [HolidayController::class, 'store']);
    // Route::delete('holiday/delete/{id}', [HolidayController::class, 'destroy']);
    // Route::get('holiday/edit/{id}', [HolidayController::class, 'edit']);
    // Route::put('holiday/update/{id}', [HolidayController::class, 'update']);

    // notification
    // Route::get('notification', [NotificationController::class, 'index']);
    // Route::get('notification/componet', [NotificationController::class, 'getComponent']);
    // Route::post('notification/store', [NotificationController::class, 'store']);
    // Route::delete('notification/delete/{id}', [NotificationController::class, 'destroy']);
    // Route::get('notification/edit/{id}', [NotificationController::class, 'edit']);
    // Route::put('notification/update/{id}', [NotificationController::class, 'update']);

    // timetable
    // Route::get('timetable', [TimetableController::class, 'index']);

    // Route::post('timetable/store', [TimetableController::class, 'store']);
    // Route::delete('timetable/delete/{id}', [TimetableController::class, 'destroy']);
    // Route::get('timetable/edit/{id}', [TimetableController::class, 'edit']);
    // Route::put('timetable/update/{id}', [TimetableController::class, 'update']);

    // leavetype
    // Route::get('leavetype', [LeavetypeController::class, 'index']);
    // Route::get('leavetype/componet', [LeavetypeController::class, 'getComponent']);
    // Route::post('leavetype/store', [LeavetypeController::class, 'store']);
    // Route::delete('leavetype/delete/{id}', [LeavetypeController::class, 'destroy']);
    // Route::get('leavetype/edit/{id}', [LeavetypeController::class, 'edit']);
    // Route::put('leavetype/update/{id}', [LeavetypeController::class, 'update']);

    // department
    // Route::get('department', [DepartmentControler::class, 'index']);
    // Route::get('department/componet', [DepartmentControler::class, 'getComponent']);
    // Route::post('department/store', [DepartmentControler::class, 'store']);
    // Route::delete('department/delete/{id}', [DepartmentControler::class, 'destroy']);
    // Route::get('department/edit/{id}', [DepartmentControler::class, 'edit']);
    // Route::put('department/update/{id}', [DepartmentControler::class, 'update']);

    // employee schedule
    // Route::get('schedule', [TimetableEmployeeController::class, 'index']);
    // Route::get('schedule/componet', [TimetableEmployeeController::class, 'getComponent']);
    // Route::post('schedule/store', [TimetableEmployeeController::class, 'store']);
    // Route::delete('schedule/delete/{id}', [TimetableEmployeeController::class, 'destroy']);
    // Route::get('schedule/edit/{id}', [TimetableEmployeeController::class, 'edit']);
    // Route::put('schedule/update/{id}', [TimetableEmployeeController::class, 'update']);

    // approve leave
    // Route::get('approve/leaves', [ApproveController::class, 'index']);
    // Route::get('approve/leaves/edit/{id}', [ApproveController::class, 'edit']);
    // Route::put('approve/leaves/update/{id}', [ApproveController::class, 'update']);
    // Route::get('approve/overtimecompesations', [ApproveController::class, 'getOtCompesation']);
    // Route::get('approve/overtimecompesations/edit/{id}', [ApproveController::class, 'editOtCompesation']);
    // Route::put('approve/overtimecompesations/update/{id}', [ApproveController::class, 'updateOTCompestion']);
    // change dayoff
    // Route::get('approve/changedayoffs', [ApproveController::class, 'getChangeDayoff']);
    // Route::get('approve/changedayoffs/edit/{id}', [ApproveController::class, 'editChangeDayOff']);
    // Route::put('approve/changedayoffs/update/{id}', [ApproveController::class, 'updateChangeDayoff']);
    // leaveout
    // Route::get('approve/leaveouts', [ApproveController::class, 'getLeaveout']);
    // Route::get('approve/leaveouts/edit/{id}', [ApproveController::class, 'editLeaveout']);
    // Route::put('approve/leaveouts/update/{id}', [ApproveController::class, 'updateLeaveout']);

    // attendance
    // Route::get('checkin', [CheckinController::class, 'checkin']);
    // Route::get('checkout', [CheckinController::class, 'checkout']);
    // Route::get('late', [CheckinControlleUr::class, 'late']);
    // Route::get('overtime', [CheckinController::class,'overtime']);
    // Route::get('absent', [CheckinController::class, 'absent']);
    Route::get('change-password', [UserController::class, 'changePassword'])->name('changePassword');
    Route::post('update-password', [UserController::class, 'updatePassword'])->name('updatePassword');
    // admin reset for user
    Route::get('user/change-password', [UserController::class, 'changeUserPassword'])->name('changePassword');
    Route::post('user/update-password', [UserController::class, 'updateUserPassword'])->name('updatePassword');

    // overtime
    // Route::get('overtimes', function(){
    //     return view('admin.overtime.overtimes');
    // });
    // Route::get('overtimes/getDate',[OvertimeController::class, 'index']);
    // Route::get('overtimes', [OvertimeController::class, 'index']);
    // Route::get('overtimes/componet', [OvertimeController::class, 'getComponent']);
    // Route::post('overtimes/store', [OvertimeController::class, 'store']);
    // Route::delete('overtimes/delete/{id}', [OvertimeController::class, 'destroy']);
    // Route::get('overtimes/edit/{id}', [OvertimeController::class, 'edit']);
    // Route::put('overtimes/update/{id}', [OvertimeController::class, 'update']);
    // send overtime to account
    // Route::post('overtimes/updates', [OvertimeController::class, 'sendtoAccount']);
    // Route::resource('attedance', 'AttendanceController');
    // Route::get('salary', [SalaryController::class, 'index'])->name('salary');
    // Route::get('salary/create', [SalaryController::class, 'create'])->name('salary/create');
    // Route::post('salary/store', [SalaryController::class, 'store']);
    // Route::delete('salary/delete/{id}', [SalaryController::class, 'destroy']);
    // Route::get('salary/edit/{id}', [SalaryController::class, 'edit']);
    // Route::post('salary/update/{id}', [SalaryController::class, 'update']);
    // Route::get('attedance_filter', 'AttendanceController@filter')->name('attedance.filter');
    // Route::post('attedance_download', 'AttendanceController@download')->name('attedance.download');
    //  structure type
    // Route::get('structuretype', [StructuretypeController::class, 'index']);
    // Route::post('structuretype/store', [StructuretypeController::class, 'store']);
    // Route::delete('structuretype/delete/{id}', [StructuretypeController::class, 'destroy']);
    // Route::get('structuretype/edit/{id}', [StructuretypeController::class, 'edit']);
    // Route::put('structuretype/update/{id}', [StructuretypeController::class, 'update']);

    // structur

    // Route::get('structure', [StructureController::class, 'index']);
    // //  Route::get('structure/componet',[StructureController::class,'getComponent'] );
    // Route::post('structure/store', [StructureController::class, 'store']);
    // Route::delete('structure/delete/{id}', [StructureController::class, 'destroy']);
    // Route::get('structure/edit/{id}', [StructureController::class, 'edit']);
    // Route::put('structure/update/{id}', [StructureController::class, 'update']);

    // contract
    // Route::get('contract', [ContractController::class, 'index']);
    // Route::get('contract/componet', [ContractController::class, 'getComponent']);
    // Route::post('contract/store', [ContractController::class, 'store']);
    // Route::delete('contract/delete/{id}', [ContractController::class, 'destroy']);
    // Route::get('contract/edit/{id}', [ContractController::class, 'edit']);
    // Route::put('contract/update/{id}', [ContractController::class, 'update']);
    // payslip for account
    // 
    
    // counter
    // Route::get('counter', [CounterController::class, 'index']);
    // Route::get('counter/edit/{id}', [CounterController::class, 'edit']);
    // Route::put('counter/update/{id}', [CounterController::class, 'update']);
    // Route::post('workdays/{date}', [WorkdayController::class, 'updateTime']);
    // // checkin  
    // Route::put('checkins/update/{id}', [AdminController::class, 'editcheckin']);
    // // export excel
    // Route::get('exports/users',[UserController::class,
    //         'exportUsers']);
    // Route::get('exports/overtimes',[OvertimeController::class,
    //         'exportOvertimes']);
    //         Route::get('exports/leaves',[ApproveController::class,
    //         'exportLeaves']);
    //         Route::get('exports/users',[UserController::class,
    //         'exportUsers']);
    // checkin history
    // Route::get('checkins/histories', [CheckinHistoryController::class, 'index']);
    // Route::delete('dayoff/delete/{id}', [ApproveController::class, 'deleteChangeDayoff']);
    // Route::delete('leave/delete/{id}', [ApproveController::class, 'deleteleave']);
    // Route::delete('ot/compensation/delete/{id}', [ApproveController::class, 'deletecompensation']);
    // discrit
    // Route::get('district', [DistrictController::class, 'index'])->name('user');
    // Route::get('district/componet', [DistrictController::class, 'getComponent']);
    // Route::post('district/store', [DistrictController::class, 'store']);
    // Route::delete('district/delete/{id}', [DistrictController::class, 'destroy']);
    // Route::get('district/edit/{id}', [DistrictController::class, 'edit']);
    // Route::post('district/update/{id}', [DistrictController::class, 'update']);
    // // school
    // Route::get('school', [SchoolController::class, 'index'])->name('user');
    // Route::get('school/componet', [SchoolController::class, 'getComponent']);
    // Route::post('school/store', [SchoolController::class, 'store']);
    // Route::delete('school/delete/{id}', [SchoolController::class, 'destroy']);
    // Route::get('school/edit/{id}', [SchoolController::class, 'edit']);
    // Route::post('school/update/{id}', [SchoolController::class, 'update']);
    // member
    
    // company
    // Route::get('company', [CompanyController::class, 'index'])->name('user');
    // Route::post('company/store', [CompanyController::class, 'store']);
    // Route::get('company/edit/{id}', [CompanyController::class, 'edit']);
    // Route::post('company/update/{id}', [CompanyController::class, 'update']);

    // Route::get('member', [MemberController::class, 'index'])->name('user');
    // Route::get('member/edit/{id}', [MemberController::class, 'edit']);
    // Route::get('member/create', [MemberController::class, 'create'])->name('user');
    // Route::post('member/store', [MemberController::class, 'store']);
    // Route::post('member/update/{id}', [MemberController::class, 'update']);
});
// Route::name('accountant/')->prefix('accountant')->middleware(['auth', 'accountant', 'active', 'check.session'])->group(function () {

//     Route::get('report/employees', [PayslipController::class, 'getemployeeView'])->name('employeeReport');
//     Route::get('employees/details/{id}', [UserController::class, 'detail']);
//     Route::get('payslip', [PayslipController::class, 'index'])->name('salary');
//     Route::get('payslip/create', [PayslipController::class, 'create'])->name('salary/create');
//     // Route::post('payslip/store', [PayslipController::class, 'store']);
//     Route::post('payslip/store/{id}', [PayslipController::class, 'addPayslip']);

//     // multiple  payslip
//     Route::post('payslip/add', [PayslipController::class, 'addMultiple']);
//     Route::delete('payslip/delete/{id}', [PayslipController::class, 'destroy']);
//     Route::get('payslip/edit/{id}', [PayslipController::class, 'edit']);
//     Route::put('payslip/update/{id}', [PayslipController::class, 'updatePayslip']);
//     Route::get('report/overtime', [PayslipController::class, 'overtimeView']);
//     Route::get('report/overtime/{startDate}/{endDate}/{export}/{account}', [PayslipController::class, 'overtimeReport'])->name('a');
//     Route::get('report/attendances', [PayslipController::class, 'attendanceView']);
//     Route::get('report/attendances/get/{startDate}/{endDate}/{export}/{account}', [PayslipController::class, 'attendaceReport']);
//     Route::get('report/employees', [PayslipController::class, 'getemployeeView'])->name('employeeReport');
//     Route::get('report/employees/{startDate}/{endDate}/{export}/{account}', [PayslipController::class, 'employeeView']);

// });
