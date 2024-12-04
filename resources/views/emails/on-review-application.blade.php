<!DOCTYPE html>
<html>
<head>
    <title>{{ $mailData['subject'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            background-color: #ffffff;
            margin: 20px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-width: 600px;
        }
        .header {
            background-color: #007bff;
            color: #ffffff;
            padding: 15px;
            text-align: center;
            border-radius: 8px 8px 0 0;
            font-size: 24px;
            font-weight: bold;
        }
        .content {
            color: #333333;
            font-size: 16px;
            line-height: 1.6;
            padding: 20px;
        }
        .footer {
            text-align: center;
            color: #888888;
            font-size: 14px;
            margin-top: 20px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }
        .note {
            color: #6c757d;
            font-size: 14px;
            margin-top: 20px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            Lamaran Anda Sedang Ditinjau
        </div>
        <div class="content">
            <p>Halo <strong>{{ $mailData['user']->full_name }}</strong>,</p>
            <p>Terima kasih telah melamar posisi <strong>{{ $mailData['user']->title }}</strong> di <strong>{{ $mailData['user']->company_name }}</strong>. Kami sangat menghargai ketertarikan Anda untuk bergabung dengan tim kami.</p>
            <p>Lamaran Anda saat ini sedang dalam proses peninjauan oleh tim rekrutmen kami. Kami akan memberikan kabar kepada Anda segera setelah ada pembaruan terkait langkah selanjutnya.</p>

            <p>Terima kasih, dan semoga sukses!</p>
            <p>Salam hangat,</p>
            <p><strong>Tim Rekrutmen {{ $mailData['user']->company_name }}</strong></p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Powered by Ayo Berkarier. Semua Hak Dilindungi.
        </div>
    </div>
</body>
</html>