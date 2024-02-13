<div class="navbar-bg" style="background-color: #34ace0"></div>
<!-- style="background-color: #445760" -->
<!-- background-color: #34ace0 -->
<nav class="navbar navbar-expand-lg main-navbar">
    <ul class="navbar-nav">
        <li>
            <a href="javascript:void(0)" data-toggle="sidebar" class="nav-link nav-link-lg">
                <i class="fas fa-bars"> </i>

            </a>
        </li>
        @include('admin.layouts.title')
    </ul>
    <ul class="ml-auto navbar-nav navbar-right">
        <li class="dropdown">
            <a href="javascript:void(0)" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                @if(auth()->user()->role->name === "Admin")
                    <img alt="image" src="{{asset('public/img/users/BanbanHT.jpg')}}" class="rounded-circle mr-1" width="30px" height="30px">

                @else
                    @if(auth()->user()->profile_url)
                        <img alt="image" src="https://banban-hr.com/hotel/public/{{ auth()->user()->profile_url }}" class="rounded-circle mr-1" width="30px" height="30px">
                    @else
                        <img alt="image" src="{{asset('public/img/users/BanbanHT.jpg')}}" class="rounded-circle mr-1" width="30px" height="30px">
                    @endif
                    
                @endif

                <!-- <img alt="image" src="{{ asset('img/users/' . auth()->user()->profile_url) }}"
                    class="rounded-circle mr-1" width="30px" height="30px"> -->
                <!-- <div class="d-sm-none d-lg-inline-block">Hi, {{ auth()->user()->name }}</div> -->

            </a>

           
                    <div class="dropdown-menu dropdown-menu-right">
                <div class="dropdown-divider"></div>
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                    class="dropdown-item has-icon text-dark">
                    <i class="fas fa-sign-out-alt"></i> <p class="en" data="logout" id="subtitle-family" ></p>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
                </a>
                 @if(auth()->user()->role->name === "Admin")
                    <a href="{{ url('admin/change-password') }}"
                   
                    class="dropdown-item has-icon text-dark">
                        <i class="fas fa-key "></i> <p class="en" data="change_password" id="subtitle-family" ></p>
                    
                </a>
                
                <a href="{{ url('admin/user/change-password') }}"
                   
                    class="dropdown-item has-icon text-dark">
                    
                    <i class="fas fa-key"></i>  <p class="en" data="change_user" id="subtitle-family" ></p>
                </a>
                  @else
                    @endif
                
               
            </div>

           
            
            <!-- <div class="dropdown-menu dropdown-menu-right">
                <div class="dropdown-divider"></div>
                <a href=""
                    onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                    class="dropdown-item has-icon text-danger">
                    <i class="fas fa-key"></i> Change password
                </a>

               
            </div> -->
        </li>
    </ul>
    <ul class=" navbar-nav navbar-right">
        <li class="dropdown">
            <a href="javascript:void(0)" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                <img alt="image" src="{{asset('public/img/icon/en_us.png')}}" class="rounded-circle mr-1 en-hidden" id="img-en" width="30px" height="30px">
                <img alt="image" src="{{asset('public/img/icon/km_kh.png')}}" class="rounded-circle mr-1 kh-hidden" id="img-kh" width="30px" height="30px">
            <!--<i class="fas fa-globe-americas" style='font-size:26px'></i>-->
            <!-- <i class="fas fa-globe"> -->
                <!-- <img alt="image" src="{{asset('img/users/admin.jpg')}}" class="rounded-circle mr-1" width="30px" height="30px"> -->
                <!-- <img alt="image" src="{{ asset('img/users/' . auth()->user()->profile_url) }}"
                    class="rounded-circle mr-1" width="30px" height="30px"> -->
                <!-- <div class="d-sm-none d-lg-inline-block">Hi, {{ auth()->user()->name }}</div> -->
                <!-- <i class="fas fa-globe">
    
                </i> -->
                {{ csrf_field() }}
                
                <!-- {{ __('item.language') }} -->
                <!--  -->
            </a>


            <div class="dropdown-menu dropdown-menu-right">
                <div class="dropdown-divider"></div>
                <!-- get all language from config.php -->
                {{ csrf_field() }}
              
                <a class="dropdown-item"   > <p class="en" data-key="lang_en" data="language" id="toggleLang"></p> </a>
                <a class="dropdown-item"    > <p class="kh" data-key="lang_kh" data="language" id="toggleLang1"></p> </a>
                <!-- <a class="dropdown-item" onclick="switchLanguage('en')" href="{{ url('admin/locale/kh') }}" > {{ __('item.english') }}</a>
                <a class="dropdown-item" onclick="switchLanguage('km')" > {{ __('item.khmer') }}</a> -->
                <script>
                    let langCode;
                    checkLanguage();
                    showImage();

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
                     function showImage() {
                        if (langCode === "en") {
                            console.log('en img')
                            var imgEn = document.getElementById("img-en");
                            imgEn.classList.remove('en-hidden')
                            document.getElementById("img-kh").classList.add('kh-hidden')
                            console.log('add classkh')
                        } 
                        else {
                            console.log('kh img')
                            var imgEn = document.getElementById("img-kh");
                            imgEn.classList.remove('kh-hidden')
                            document.getElementById("img-en").classList.add('en-hidden')
                            console.log('add class en')

                        }
                    }

                    function onBtnLangEn() {
                        if(langCode === "kh"){
                            langCode = "en",
                                localStorage.setItem("lang-code", "en")
                        }
                        showImage();
                       
                        checkLanguage();
                    }
                    function onBtnLangKh() {
                        if (langCode === "en") {
                            langCode = "kh";
                            localStorage.setItem("lang-code", "kh")
                        } 
                        showImage();
                        
                        checkLanguage();
                    }
                    document.getElementById("toggleLang").addEventListener("click", onBtnLangEn)
                    document.getElementById("toggleLang1").addEventListener("click", onBtnLangKh)
                </script>



            </div>
        </li>
    </ul>
</nav>