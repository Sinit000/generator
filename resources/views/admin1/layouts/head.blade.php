<meta charset="UTF-8">
<meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" href="{{ asset('img/logo.jpeg') }}">
<title>Admin @yield('title')</title>


<!-- General CSS Files -->
<link rel="stylesheet" href="{{ asset('backend/css/bootstrap.min.css') }}">
<!-- <link rel="stylesheet" href="{{ asset('backend/css/all.min.css') }}"> -->
<!-- <link rel="stylesheet" href="https://demo.getstisla.com/assets/modules/bootstrap/css/bootstrap.min.css"> -->
<!-- <link rel="stylesheet" href="https://demo.getstisla.com/assets/modules/fontawesome/css/all.min.css"> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<!-- <link rel="stylesheet" href="{{ asset('backend/fontawesome-6/all.min.css') }}" crossorigin="anonymous" referrerpolicy="no-referrer"> -->

@yield('css')

<!-- Template CSS -->
<link rel="stylesheet" href="{{ asset('backend/css/style.css') }}">
<link rel="stylesheet" href="{{ asset('backend/css/app.css') }}">
<link rel="stylesheet" href="{{ asset('backend/css/components.css') }}">
<link rel="icon" href="{{asset('img/users/BanbanHT.jpg')}}">
<!-- <link rel="stylesheet" href="{{ asset('backend/css/app.css') }}"> -->

<!-- <link rel="stylesheet" href="https://demo.getstisla.com/assets/css/style.css"> -->
<!-- <link rel="stylesheet" href="https://demo.getstisla.com/assets/css/components.css"> -->

<style>
    .en[data="language"]::before {
        content: 'English'
    }

    .kh[data="language"]::before {
        content: 'ភាសា'
    }

    .en[data="login"]::before {
        content: 'Login'

    }

    .kh[data="login"]::before {
        content: 'ចូលប្រើប្រាស់'
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
    .en[data="dashboard"]::before {
        content: 'Dashboard'
    }

    .kh[data="dashboard"]::before {
        content: 'ផ្ទាំងការងារ'
    }
    .en[data="employee"]::before {
        content: 'Employee'
    }

    .kh[data="employee"]::before {
        content: 'បុគ្គលិក'
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
        content: 'Overtime Compensation'
    }

    .kh[data="clear_ot"]::before {
        content: 'ការទូទាត់ថែមម៉ោង'
    }
    .en[data="change_day_off"]::before {
        content: 'Change Day Off'
    }

    .kh[data="change_day_off"]::before {
        content: 'ថ្ងៃឈប់សម្រាក'
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
        content: 'Overtime Report'
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
    .en[data="location"]::before {
        content: 'Location'
    }

    .kh[data="location"]::before {
        content: 'ទីតាំង'
    }
    .en[data="workday"]::before {
        content: 'Workday'
    }

    .kh[data="workday"]::before {
        content: 'ថ្ងៃធ្វើការ'
    }
    .en[data="date_time"]::before {
        content: 'Date Time'
    }

    .kh[data="date_time"]::before {
        content: 'ថ្ងៃ'
    }
    .en[data="department"]::before {
        content: 'Department'
    }

    .kh[data="department"]::before {
        content: 'ដេប៉ាតេម៉ង់'
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
        content: 'ផ្ទាង'
    }
    

</style>