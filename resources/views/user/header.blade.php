<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        Account | Union Savings Bank </title>
    <meta property="og:title" content="Union Savings Bank">
    <meta name="author" content="Union Savings Bank">
    <meta name="description" content="Mobile Banking, Credit Cards, Mortgages, Auto Loan">
    <meta name="keywords" content="First Citizen Bank">
    <meta property="og:locale" content="en_US">
    <meta property="og:description" content="Mobile Banking, Credit Cards, Mortgages, Auto Loan">
    <meta name="og:keywords" content="First Citizen Bank">
    <meta property="og:url" content="https://unionsb.online">
    <meta property="og:site_name" content="Union Savings Bank">
    <meta property="og:image" content="uploads/logo.png" />
    <link rel="canonical" href="https://unionsb.online">
    <!-- favicon & bookmark -->
    <link rel="apple-touch-icon" sizes="144x144" href="uploads/logo.png">
    <link rel="shortcut icon" href="uploads/logo.png">

    <meta name="robots" content="index, follow" />
    <!-- Chrome, Firefox OS and Opera -->
    <meta name="theme-color" content="#3037ff">
    <!-- Windows Phone -->
    <meta name="msapplication-navbutton-color" content="#3037ff" />
    <!-- iOS Safari -->
    <meta name="apple-mobile-web-app-status-bar-style" content="#3037ff" />


    <!---**************COLORS*****************-->
    <style>
        :root {
            --primary_color: #3037ff;
            --secondary_color: #c92041;
        }
    </style>
    <!---**************COLORS*****************-->
    <link href="{{asset('themes/finapp-light/css/src/bootstrap/bootstrap.min.css')}}" rel="stylesheet"
        type="text/css" />
    <link href="{{asset('themes/finapp-light/css/src/splide/splide.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600&display=swap" rel="stylesheet"
        type="text/css" />
    <link href="{{asset('themes/finapp-light/css/fontawesome.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('themes/finapp-light/css/all.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('themes/finapp-light/js/plugins/datatable/datatables.min.css')}}" rel="stylesheet"
        type="text/css" />
    <link href="{{asset('themes/finapp-light/css/others.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('themes/finapp-light/css/style.css')}}" rel="stylesheet" type="text/css" />
    <!-- ========= Custom JS Files =========  -->
    <script src="{{asset('assets/javascript/jquery.min.js')}}"></script>
    <script src="{{asset('assets/javascript/clipboard.min.js')}}"></script>
    <script src="{{asset('assets/javascript/countries.js')}}"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- ========= Custom JS Files =========  -->

    <style>
        th {
            font-size: 16px;
        }
    </style>

    <link rel="stylesheet" href="{{asset('assets/stylesheets/others.css')}}">
    <script src="{{asset('assets/javascript/language.js')}}"></script>
    <script type="text/javascript"
        src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit2"></script>
    <style>
        .progress {
            width: 150px;
            height: 150px !important;
            text-align: center;
            line-height: 150px;
            background: none;
            margin: 20px;
            box-shadow: none;
            position: relative;
        }

        .progress:after {
            content: "";
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 12px solid #fff;
            position: absolute;
            top: 0;
            left: 0;
        }

        .progress>span {
            width: 50%;
            height: 100%;
            overflow: hidden;
            position: absolute;
            top: 0;
            z-index: 1;
        }

        .progress .progress-bar {
            width: 100%;
            height: 100%;
            background: none;
            border-width: 12px;
            border-style: solid;
            position: absolute;
            top: 0;
        }

        .progress .progress-right {
            right: 0;
        }

        .progress .progress-right .progress-bar {
            left: -100%;
            border-top-left-radius: 80px;
            border-bottom-left-radius: 80px;
            border-right: 0;
            -webkit-transform-origin: center right;
            transform-origin: center right;
            animation: loading-1 1.8s linear forwards;
        }

        .progress .progress-value {
            width: 90%;
            height: 90%;
            border-radius: 50%;
            background: #000;
            font-size: 10px;
            color: #fff;
            line-height: 135px;
            text-align: center;
            position: absolute;
            top: 5%;
            left: 5%;
        }

        .progress.blue .progress-bar {
            border-color: #049dff;
        }
    </style>
</head>

<body>
    <!-- App Header -->

    <div class="appHeader bg-primary text-light">
        <div class="left">
            <style>
                .gtranslate_wrapper>.notranslate:nth-child(1) {
                    color: white;
                }
            </style>
            <div class="gtranslate_wrapper"></div>
            <script>
                window.gtranslateSettings = {
                    "default_language": "en",
                    "detect_browser_language": true,
                    "wrapper_selector": ".gtranslate_wrapper",
                    "flag_size": 24
                }
            </script>
            <script src="https://cdn.gtranslate.net/widgets/latest/popup.js" defer></script>
        </div>
        <div class="pageTitle"> <a href="{{route('home')}}"><img src="{{asset('uploads/logo.png')}}" alt="logo"
                    class="logo"></a>
        </div>
        <div class="right">
            <a href="#" class="headerButton" data-bs-toggle="modal" data-bs-target="#ProfileModal"> <img
                    src="{{ asset(Auth::user()->photo ? 'storage/' . Auth::user()->photo : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=random') }}"
                    alt="Johnson" class="imaged" style="width:35px;height:35px;border-radius:50%"> <span
                    class="badge badge-danger">
                    0 </span> </a>
        </div>
    </div>

    <script type="text/javascript">
        (function () {
            var options = {
                whatsapp: "+14128196749", // WhatsApp number
                call_to_action: "Message us", // Call to action
                position: "left", // Position may be 'right' or 'left'
            };
            var proto = document.location.protocol, host = "getbutton.io", url = proto + "//static." + host;
            var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = url + '/widget-send-button/js/init.js';
            s.onload = function () { WhWidgetSendButton.init(host, proto, options); };
            var x = document.getElementsByTagName('script')[0]; x.parentNode.insertBefore(s, x);
        })();
    </script>