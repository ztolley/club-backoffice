<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Player Contract</title>
    <link rel="icon" type="image/svg+xml" href="/img/logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&amp;display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/public.css') }}">
    <style>
        canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: -1;
            pointer-events: none;
        }

        .thank {
            text-align: center;
            margin-top: 5rem;
        }
    </style>
</head>

<body>
    <div id="page-content">
        <header id="header" class="hide-in-iframe">
            <img src="https://www.hartlandgirlsacademy.co.uk/wp-content/uploads/2024/08/cropped-HARTLAND-LOGO-2048.png"
                alt="Hartland Logo" height="256" width="256" />
            <h1>25/26 - Player Contract</h1>
        </header>

        <div id="container" class="container">
            <h1 class="thank">Thank you!</h1>
        </div>
    </div>
    <canvas id="fireworks"></canvas>
    <script src="{{ asset('js/fireworks.js') }}"></script>
    <script>
        if (window.self !== window.top) {
            // Page is in an iframe
            document.body.classList.add('in-iframe');
        }
    </script>
</body>

</html>
