<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Restaurante Casa Colonial</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700,800" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- AOS for Animations -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- Custom Styles -->
    <style>
        .bg-pharmacy {
            background: url('https://scontent.flim6-4.fna.fbcdn.net/v/t39.30808-6/471415369_1672278780001052_383891410651156116_n.jpg?stp=dst-jpg_s960x960_tt6&_nc_cat=107&ccb=1-7&_nc_sid=cc71e4&_nc_eui2=AeGtXEdeKU6GbP7kF_LyeTXl72x6xJSA7yXvbHrElIDvJVXgSv2dkRRKF8ag3381Lc188WHYN7ocJCUxWirT5-n7&_nc_ohc=2CGtHH85gUAQ7kNvwGyijE_&_nc_oc=AdlYmUIZSzjsecXdhcoV6anVLnuf6LzlGBzyWikFor-DICzMjTsCDWoQoaC8LNW8Oz4&_nc_zt=23&_nc_ht=scontent.flim6-4.fna&_nc_gid=M1Y0qnoMnf-Gh22oYFeazA&oh=00_AfRVuobJbPB2wQFwhGddHvbdf1bHZkzFWh2nNQfKET11wA&oe=687A542C') no-repeat center center/cover;
            height: 100vh;
            position: relative;
            overflow: hidden;
        }

        .content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: #fff;
            padding: 20px;
        }

        .glass-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
        }

        .title-gradient {
            background: linear-gradient(90deg, #ff6b6b, #4ecdc4, #f7b733);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-weight: 800;
            font-size: 3.5rem;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
            animation: gradientShift 5s ease infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .subtitle-color {
            background: linear-gradient(90deg, #a3e4d7, #96ceb4);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-size: 1.5rem;
            text-shadow: 0 0 5px rgba(0, 0, 0, 0.4);
            margin-bottom: 2rem;
        }

        .button-primary {
            background-color: #ef4444;
            padding: 12px 24px;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 600;
            text-transform: uppercase;
        }

        .button-primary:hover {
            background-color: #dc2626;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(239, 68, 68, 0.4);
        }
    </style>
</head>
<body class="font-poppins">
    <!-- Main Content -->
    <div class="bg-pharmacy">
        <div class="content flex flex-col items-center justify-center h-full">
            <div class="glass-card">
                <h1 class="title-gradient mb-6" data-aos="fade-up" data-aos-duration="1000">Casa Colonial</h1>
                <p class="subtitle-color mb-8 text-white" data-aos="fade-up" data-aos-delay="200" data-aos-duration="1000">
                    Restaurante en una encantadora casa colonial urbana, con balcones de madera y un ambiente que combina historia y tradición gastronómica en pleno corazón de la ciudad.
                </p>
                <div class="space-x-4" data-aos="fade-up" data-aos-delay="400" data-aos-duration="1000">
                    <a href="{{ route('login') }}" class="button-primary text-white">Inicio</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Initialize AOS -->
    <script>
        AOS.init();
    </script>
</body>
</html>
