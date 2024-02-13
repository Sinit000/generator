<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Login</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="{{ asset('backend/css/bootstrap.min.css') }}">
  <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous"> -->
  <!-- <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous"> -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- CSS Libraries -->
  <!-- <link rel="stylesheet" href="../node_modules/bootstrap-social/bootstrap-social.css"> -->

  <!-- Template CSS -->
  <link rel="stylesheet" href="{{ asset('backend/css/style.css') }}">
  <!-- <link rel="stylesheet" href="https://demo.getstisla.com/assets/css/style.css">
<link rel="stylesheet" href="{{ asset('backend/css/style.css') }}"> -->
  <!-- <link rel="stylesheet" href="https://demo.getstisla.com/assets/css/components.css"> -->
  <style>
    #app {
      /* background-color: #34ace0;
       */
      width: 100%;
      /* padding: 100px; */
    }

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
    .en[data="e"]::before {
      content: 'Email Address'
      
    }

    .kh[data="e"]::before {
      content: 'អ៊ីម៉ែល'
    }
    .en[data="btn_login"]::before {
      content: 'Login'
      
    }

    .kh[data="btn_login"]::before {
      content: 'ចូលគនណី'
    }
    .en[data="d"]::before {
      content: 'Password'
      
    }

    .kh[data="d"]::before {
      content: 'លេខសំងាត់'
    }
    .dropbtn {
      background-color: #34ace0;
  color: white;
  padding: 16px;
  font-size: 16px;
  border: none;
  cursor: pointer;
}

/* The container <div> - needed to position the dropdown content */
.dropdown {
  position: relative;
  display: inline-block;
}

/* Dropdown Content (Hidden by Default) */
.dropdown-content {
  display: none;
  position: absolute;
  /* background-color: #f9f9f9; */
  min-width: 160px;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 1;
}

/* Links inside the dropdown */
.dropdown-content a {
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
}

/* Change color of dropdown links on hover */
.dropdown-content a:hover {background-color: #f1f1f1}

/* Show the dropdown menu on hover */
.dropdown:hover .dropdown-content {
  display: block;
}

/* Change the background color of the dropdown button when the dropdown content is shown */
.dropdown:hover .dropbtn {
  background-color: #3e8e41;
}
body  {
  /* background-image:url("../img/users/backgroud.jpg"); */
  /* background-color: #cccccc;
  background-repeat: no-repeat;
  background-attachment: fixed; */
  /* background-position: center;  */
  /* background-size: auto; */
  width: 100%;
    background: linear-gradient(to top,rgba(0,0,0,0.5)50%,rgba(0,0,0,0.5)50%),url("../img/users/backgroud.jpg");
    background-position: center;
    background-size: cover;
    /* height: 100vh; */
    font-family: sans-serif;
  /* width: 300px;
  height: 300px; */
}
#title-family{
        font-family:'Khmer OS Moul Light';
        font-size: 30px;
    }
    #subtitle-family{
        font-family:'Khmer OS';
        font-size: 18px;
    }
  </style>
</head>


<body>
  <div id="app">
    <!-- <div class="dropdown">
      <button class="dropbtn"><i class="fas fa-language "></i> </button>
      <div class="dropdown-content">
        <a href="#">Link 1</a>
        <a href="#">Link 2</a>
        <a href="#">Link 3</a>
      </div>
    </div> -->
    <div class="row">
      <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
        <div class="login-brand">

          <img src="{{asset('img/users/BanbanHT.jpg')}}" alt="logo" width="100" class=" rounded-circle">
        </div>
        <!-- <button class="en" data="language" id="toggleLang" ></button> -->
        <div class="card card-info">
          <div class="card-header">
            <h1 class="en" data="login" id="title-family"  ></h1>
          </div>

          <div class="card-body">
            @if ($errors->any())
            <div class="alert alert-danger">
              <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
              {{ csrf_field() }}
              <div class="form-group">
                <label for="email" class="en"  data="e" id="subtitle-family"></label>
                <input type="text" class="form-control {{ $errors->has('username') || $errors->has('email') ? 'is-invalid' : '' }}" name="login" placeholder="username or email" value="{{ old('username') ?: old('email') }}" autofocus>
                <div class="invalid-feedback">
                  Please fill in your email
                </div>
              </div>

              <div class="form-group">
                <div class="d-block">
                  <label for="password" class="en control-label"   data="d" id="subtitle-family"></label>
                  <!-- <div class="float-right">
                        <a href="auth-forgot-password.html" class="text-small">
                          Forgot Password?
                        </a>
                      </div> -->
                </div>
                <input type="password" class="form-control @error('password') is-invalid @enderror" placeholder="password" name="password" autocomplete="current-password">
                <div class="invalid-feedback">
                  please fill in your password
                </div>
              </div>



              <div class="form-group" >
                <button type="submit" class="en btn btn-info btn-lg btn-block" tabindex="4" data="btn_login" id="subtitle-family">
                  
                </button>
              </div>
            </form>


          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- General JS Scripts -->
  <!-- <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.6/jquery.nicescroll.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script> -->
  <!-- <script src="../assets/js/stisla.js"></script> -->

  <!-- JS Libraies -->

  <!-- Template JS File -->
  <!-- <script src="../assets/js/scripts.js"></script>
  <script src="../assets/js/custom.js"></script> -->
  <script>
    let langCode;
    checkLanguage()

    function checkLanguage() {
      langCode = localStorage.getItem("lang-code");
      if (!langCode) {
        langCode = "en";
        localStorage.setItem("lang-code", "en")
      }
      let needTranslates = [];
      if (langCode === "en") {
        needTranslates = [...document.getElementsByClassName('kh')];
        for (let n of needTranslates) {
          n.classList.replace("kh", "en")
        }
      } else {
        needTranslates = [...document.getElementsByClassName('en')];
        for (let n of needTranslates) {
          n.classList.replace("en", "kh")

        }
      }
    }

    function onBtnLangClick() {
      if (langCode === "en") {
        langCode = "kh";
        localStorage.setItem("lang-code", "kh")
      } else {
        langCode = "en",
          localStorage.setItem("lang-code", "en")
      }
      checkLanguage();
    }
    document.getElementById("toggleLang").addEventListener("click", onBtnLangClick)
  </script>

  <!-- Page Specific JS File -->
</body>

</html>