<?php
http_response_code(503);
header('Retry-After: 3600');
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Maintenance en cours - INFPF</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0a1628 0%, #1a2a4a 50%, #0d1f3c 100%);
            color: #fff;
            overflow: hidden;
        }

        .bg-shapes {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            pointer-events: none;
            z-index: 0;
        }
        .bg-shapes .circle {
            position: absolute;
            border-radius: 50%;
            opacity: 0.07;
            background: #4a90d9;
        }
        .bg-shapes .c1 { width: 600px; height: 600px; top: -200px; right: -150px; }
        .bg-shapes .c2 { width: 400px; height: 400px; bottom: -100px; left: -100px; background: #6c63ff; }
        .bg-shapes .c3 { width: 250px; height: 250px; top: 40%; left: 60%; background: #00b4d8; }

        .container {
            position: relative;
            z-index: 1;
            text-align: center;
            max-width: 620px;
            padding: 60px 40px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3);
            animation: fadeUp 0.8s ease-out;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .icon-wrapper {
            width: 90px; height: 90px;
            margin: 0 auto 30px;
            background: linear-gradient(135deg, #4a90d9, #6c63ff);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2.5s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(74, 144, 217, 0.4); }
            50%      { box-shadow: 0 0 0 20px rgba(74, 144, 217, 0); }
        }

        .icon-wrapper svg {
            width: 42px; height: 42px;
            fill: none;
            stroke: #fff;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .logo {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: 3px;
            margin-bottom: 12px;
            background: linear-gradient(90deg, #fff, #a0c4ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        h1 {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 16px;
            color: #e8edf5;
        }

        .description {
            font-size: 16px;
            line-height: 1.7;
            color: #94a3b8;
            margin-bottom: 36px;
        }

        .progress-bar {
            width: 100%;
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
            overflow: hidden;
            margin-bottom: 36px;
        }

        .progress-bar .fill {
            height: 100%;
            width: 40%;
            background: linear-gradient(90deg, #4a90d9, #6c63ff, #4a90d9);
            background-size: 200% 100%;
            border-radius: 2px;
            animation: shimmer 2s ease-in-out infinite;
        }

        @keyframes shimmer {
            0%   { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        .contact {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 28px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: #a0c4ff;
            font-size: 15px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .contact:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .contact svg {
            width: 18px; height: 18px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .footer-text {
            margin-top: 40px;
            font-size: 13px;
            color: #475569;
        }
    </style>
</head>
<body>
    <div class="bg-shapes">
        <div class="circle c1"></div>
        <div class="circle c2"></div>
        <div class="circle c3"></div>
    </div>

    <div class="container">
        <div class="icon-wrapper">
            <svg viewBox="0 0 24 24">
                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
            </svg>
        </div>

        <div class="logo">INFPF</div>

        <h1>Maintenance en cours</h1>

        <p class="description">
            Notre site fait actuellement l'objet d'une mise à jour pour vous offrir 
            une meilleure expérience. Nous serons de retour très prochainement.
        </p>

        <div class="progress-bar">
            <div class="fill"></div>
        </div>

        <a href="mailto:contact@infpf.fr" class="contact">
            <svg viewBox="0 0 24 24">
                <rect x="2" y="4" width="20" height="16" rx="2"/>
                <path d="M22 4l-10 8L2 4"/>
            </svg>
            contact@infpf.fr
        </a>

        <p class="footer-text">&copy; <?= date('Y') ?> INFPF - Institut National de la Formation Professionnelle de France</p>
    </div>
</body>
</html>
