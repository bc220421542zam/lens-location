<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Redirecting to JazzCash...</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            text-align: center;
        }
        .spinner {
            border: 4px solid rgba(0, 0, 0, 0.1);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border-left-color: #4f46e5;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        p {
            color: #374151;
            font-size: 1.1em;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="spinner"></div>
        <p>Redirecting to secure payment gateway. Please wait...</p>
        
        <form id="jazzcash-form" method="POST" action="{{ $url }}">
            @foreach($data as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
        </form>
    </div>
    <script>
        document.getElementById('jazzcash-form').submit();
    </script>
</body>
</html>