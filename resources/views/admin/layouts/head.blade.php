<meta charset="UTF-8">
<meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" href="{{ asset('img/logo.jpeg') }}">
<title>Admin @yield('title')</title>


<!-- General CSS Files -->
<link rel="stylesheet" href="{{ asset('public/backend/css/bootstrap.min.css') }}">
<!-- <link rel="stylesheet" href="{{ asset('backend/css/all.min.css') }}"> -->
<!-- <link rel="stylesheet" href="https://demo.getstisla.com/assets/modules/bootstrap/css/bootstrap.min.css"> -->
<!-- <link rel="stylesheet" href="https://demo.getstisla.com/assets/modules/fontawesome/css/all.min.css"> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<!-- <link rel="stylesheet" href="{{ asset('backend/fontawesome-6/all.min.css') }}" crossorigin="anonymous" referrerpolicy="no-referrer"> -->

@yield('css')

<!-- Template CSS -->
<link rel="stylesheet" href="{{ asset('public/backend/css/style.css') }}">
<link rel="stylesheet" href="{{ asset('public/backend/css/app.css') }}">
<link rel="stylesheet" href="{{ asset('public/backend/css/components.css') }}">
<link rel="icon" href="{{asset('public/img/users/BanbanHT.jpg')}}">
<style>
    .en[data="language"]::before {
        content: 'English'
    }

    .kh[data="language"]::before {
        content: 'ភាសា'
    }
    .en[data-key="lang_en"]::before {
        content: 'English'
    }

    .kh[data-key="lang_en"]::before {
        content: 'ភាសាអង់គ្លេស'
    }

    .en[data-key="lang_kh"]::before {
        content: 'Khmer'

    }
    .kh[data-key="lang_kh"]::before {
        content: 'ភាសាខ្មែរ'
    }


    .kh[data="login"]::before {
        content: 'ចូលប្រើប្រាស់'
    }
     .en[data="change_password"]::before {
        content: 'Change Password'

    }
     .kh[data="logout"]::before {
        content: 'ចាកចេញ'
    }
     .en[data="logout"]::before {
        content: 'Logout'

    }

    .kh[data="change_password"]::before {
        content: 'ផ្លាស់ប្តូរលេខសំងាត់'
    }
    .kh[data="change_user"]::before {
        content: 'ផ្លាស់ប្តូរលេខសំងាត់បុគ្គលិក'
    }
    .en[data="change_user"]::before {
        content: 'Change Employee Password'

    }
    .en[data="dashboard"]::before {
        content: 'Dashboard'
    }

    .kh[data="dashboard"]::before {
        content: 'ផ្ទាំងការងារ'
    }

    
   #title-family{
        font-family:'Arial','moul';
        /* font-size: 30px; */
    }
    #subtitle-family{
        font-family:'Arial','battambang';
        font-size: 18px;
    }
    .en-hidden {
        display: none;
    }
    .kh-hidden {
        display: none;
    }
     .en[data="employee"]::before {
        content: 'Member'
    }

    .kh[data="employee"]::before {
        content: 'សមាជិក'
    }
    .en[data="attendances"]::before {
        content: 'Attendance'
    }

    .kh[data="attendances"]::before {
        content: 'វត្តមាន'
    }
    .en[data="approve_board"]::before {
        content: 'Approve Board'
    }

    .kh[data="approve_board"]::before {
        content: 'ការអនុម័ត'
    }
    .en[data="leave"]::before {
        content: 'Leave'
    }

    .kh[data="leave"]::before {
        content: 'ច្បាប់'
    }
    .en[data="leave_out"]::before {
        content: 'Leave out'
    }

    .kh[data="leave_out"]::before {
        content: 'សុំចេញក្រៅ'
    }
    .en[data="clear_ot"]::before {
        content: 'OT Compensation'
    }

    .kh[data="clear_ot"]::before {
        content: 'ការទូទាត់ថែមម៉ោង'
    }
    .en[data="change_day_off"]::before {
        content: 'Notification Change'
    }

    .kh[data="change_day_off"]::before {
        content: 'ថ្ងៃឈប់សម្រាក'
    }
        .en[data="payslip"]::before {
        content: 'Payslip'
    }

    .kh[data="payslip"]::before {
        content: 'ប្រាក់ខែ'
    }
    .en[data="overtime"]::before {
        content: 'Overtime'
    }

    .kh[data="overtime"]::before {
        content: 'ថែមម៉ោង'
    }
    .en[data="payroll"]::before {
        content: 'Payroll'
    }

    .kh[data="payroll"]::before {
        content: 'ប្រាក់ខែ'
    }
    .en[data="structure"]::before {
        content: 'Structure'
    }

    .kh[data="structure"]::before {
        content: 'រចនាសម្ព័ន្ធ'
    }
    .en[data="contract"]::before {
        content: 'Contract'
    }

    .kh[data="contract"]::before {
        content: 'កុងត្រា'
    }
    .en[data="overtime_report"]::before {
        content: 'Overtime Report'
    }

    .kh[data="overtime_report"]::before {
        content: 'របាយការណ៏ថែមម៉ោង'
    }
    .en[data="attendance_report"]::before {
        content: 'Attendance Report'
    }

    .kh[data="attendance_report"]::before {
        content: 'របាយការណ៏វត្តមាន'
    }
    .en[data="report"]::before {
        content: 'Report'
    }

    .kh[data="report"]::before {
        content: 'របាយការណ៏'
    }
    .en[data="setting"]::before {
        content: 'Setting'
    }

    .kh[data="setting"]::before {
        content: 'ប្រតិបត្តិការ'
    }
    .en[data="user"]::before {
        content: 'User'
    }

    .kh[data="user"]::before {
        content: 'អ្នកប្រើប្រាស់'
    }
    .en[data="district"]::before {
        content: 'District'
    }

    .kh[data="district"]::before {
        content: 'ស្រុក'
    }
    .en[data="school"]::before {
        content: 'School'
    }

    .kh[data="school"]::before {
        content: 'សាលា'
    }
    .en[data="province"]::before {
        content: 'Province'
    }

    .kh[data="province"]::before {
        content: 'ខេត្ត'
    }
    .en[data="position"]::before {
        content: 'Position'
    }

    .kh[data="position"]::before {
        content: 'មុខតំណែង'
    }
    .en[data="timetable"]::before {
        content: 'Timetable'
    }

    .kh[data="timetable"]::before {
        content: 'ម៉ោងធ្វើការ'
    }
    .en[data="leavetype"]::before {
        content: 'Leavetype'
    }

    .kh[data="leavetype"]::before {
        content: 'ប្រភេទច្បាប់'
    }
    .en[data="holiday"]::before {
        content: 'Holiday'
    }

    .kh[data="holiday"]::before {
        content: 'ថ្ងៃបុណ្យជាតិ'
    }
    .en[data="notification"]::before {
        content: 'Notification'
    }

    .kh[data="notification"]::before {
        content: 'សារជូនដំណឺង'
    }
    .en[data="counter"]::before {
        content: 'Counter'
    }

    .kh[data="counter"]::before {
        content: 'បញ្ជរ'
    }
    .en[data="checkin_history"]::before {
        content: 'Checkin History'
    }

    .kh[data="checkin_history"]::before {
        content: 'ប្រវត្តកេប្រែលវត្តមាន'
    }
    .en[data="admin_panel"]::before {
        content: 'Admin Panel'
    }

    .kh[data="admin_panel"]::before {
        content: 'ផ្ទាំងការងារសម្រាប់អេនមីន'
    }
    .en[data="accounta_panel"]::before {
        content: 'Accountant Panel'
    }

    .kh[data="accounta_panel"]::before {
        content: 'ផ្ទាំងការងារសម្រាប់គណនី'
    }
    .en[data="company"]::before {
        content: 'Company'
    }

    .kh[data="company"]::before {
        content: 'ក្រុមហ៊ុន'
    }
</style>
<!-- <link rel="stylesheet" href="https://demo.getstisla.com/assets/css/style.css"> -->
<!-- <link rel="stylesheet" href="https://demo.getstisla.com/assets/css/components.css"> -->
