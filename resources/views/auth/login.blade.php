<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LensLocation</title>

    @vite('resources/css/app.css')
</head>
<body>

<div class="container">
     <!-- NAVBAR -->
        <div class="top-nav">
            <h2 class="logo">
                <img src="{{ asset('images/Logo.png') }}" alt="LensLocation logo" width="190">
            </h2>
            <div>
                <a href="#" class="btn-outline">Sign in</a>
                <a href="#" class="btn">Register</a>
            </div>
        </div>
    <!-- LEFT SIDE -->
    <div class="left" style="background: url('{{ asset('images/Image-login.png') }}') no-repeat center/cover; height:100vh;">
        <div class="overlay">
            <h1>Photographer Marketplace</h1>
            <p>More Agent Client</p>
        </div>
    </div>

    <!-- RIGHT SIDE -->
    <div class="right">

       

        <!-- LOGIN CARD -->
        <div class="login-card">
            <img src="{{ asset('images/Logo.png') }}" alt="LensLocation logo" width="250" style="display:block; margin:0 auto;">
            <p>Welcome Back</p>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="email....." required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="000000" required>
                </div>

                <div class="forgot">
                    <a href="#">Forgot Password?</a>
                </div>

                <button type="submit" class="login-btn">Login</button>

                <p class="signup">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="underline">Sign Up</a>
                </p>
            </form>
        </div>

    </div>

</div>

</body>
</html>