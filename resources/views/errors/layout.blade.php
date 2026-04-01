<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | Union Savings Bank</title>
    <link rel="shortcut icon" href="{{ asset('uploads/logo.png') }}">
    <link href="https://fonts.googleapis.com/css?family=Fira+Sans:400,500,600" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Fira Sans', sans-serif;
            background: #f4f6f9;
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .error-header {
            background: #1a1f36;
            padding: 15px 30px;
            text-align: center;
        }

        .error-header img {
            height: 50px;
        }

        .error-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .error-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
            padding: 50px 40px;
            text-align: center;
            max-width: 520px;
            width: 100%;
        }

        .error-code {
            font-size: 96px;
            font-weight: 700;
            color: #3037ff;
            line-height: 1;
            margin-bottom: 10px;
        }

        .error-title {
            font-size: 24px;
            font-weight: 600;
            color: #1a1f36;
            margin-bottom: 12px;
        }

        .error-message {
            font-size: 16px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .error-icon {
            font-size: 48px;
            margin-bottom: 20px;
        }

        .btn-home {
            display: inline-block;
            background: #3037ff;
            color: #fff;
            text-decoration: none;
            padding: 12px 32px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 500;
            transition: background 0.2s;
        }

        .btn-home:hover {
            background: #252bc7;
        }

        .btn-secondary {
            display: inline-block;
            background: transparent;
            color: #3037ff;
            text-decoration: none;
            padding: 12px 32px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 500;
            border: 1px solid #3037ff;
            margin-left: 10px;
            transition: background 0.2s, color 0.2s;
        }

        .btn-secondary:hover {
            background: #3037ff;
            color: #fff;
        }

        .error-footer {
            text-align: center;
            padding: 20px;
            font-size: 13px;
            color: #999;
        }

        @media (max-width: 480px) {
            .error-code {
                font-size: 64px;
            }

            .error-title {
                font-size: 20px;
            }

            .error-card {
                padding: 30px 20px;
            }

            .btn-home,
            .btn-secondary {
                display: block;
                margin: 8px 0;
            }
        }
    </style>
</head>

<body>
    <div class="error-header">
        <a href="{{ url('/') }}"><img src="{{ asset('uploads/logo.png') }}" alt="Union Savings Bank"></a>
    </div>
    <div class="error-container">
        <div class="error-card">
            @yield('content')
            <div style="margin-top: 30px;">
                <a href="{{ url('/') }}" class="btn-home">Go to Homepage</a>
                <a href="javascript:history.back()" class="btn-secondary">Go Back</a>
            </div>
        </div>
    </div>
    <div class="error-footer">
        &copy; {{ date('Y') }} Union Savings Bank. All rights reserved.
    </div>
</body>

</html>