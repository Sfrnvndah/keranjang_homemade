<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Pesanan Berhasil</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                text-align: center;
                background: #f4f4f4;
                padding: 50px;
            }

            .success-container {
                background: white;
                border-radius: 10px;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                padding: 30px;
                max-width: 500px;
                margin: 50px auto;
            }

            .success-icon {
                width: 100px;
                height: 100px;
                margin: 0 auto 20px;
                background: #4caf50;
                border-radius: 50%;
                display: flex;
                justify-content: center;
                align-items: center;
                animation: pop 0.5s ease-in-out;
            }

            .success-icon::before {
                content: "✔";
                color: white;
                font-size: 50px;
                font-weight: bold;
            }

            .success-message {
                font-size: 20px;
                margin: 20px 0;
                color: #333;
            }

            .btn-back {
                display: inline-block;
                padding: 10px 20px;
                margin-top: 20px;
                background: #007bff;
                color: white;
                border: none;
                border-radius: 5px;
                text-decoration: none;
                font-size: 16px;
                cursor: pointer;
                transition: background 0.3s;
            }

            .btn-back:hover {
                background: #0056b3;
            }

            @keyframes pop {
                0% {
                    transform: scale(0);
                }
                50% {
                    transform: scale(1.2);
                }
                100% {
                    transform: scale(1);
                }
            }
        </style>
    </head>
    <body>
        <div class="success-container">
            <div class="success-icon"></div>
            <div class="success-message">
                <h2>Pesanan Anda Berhasil Dibuat!</h2>
                <p>Terima kasih telah berbelanja bersama kami.</p>
            </div>
            <a href="../index.php" class="btn-back">Kembali ke Beranda</a>
        </div>
    </body>
</html>
