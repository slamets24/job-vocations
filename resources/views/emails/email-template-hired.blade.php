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
            background-color: #28a745;
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
            background-color: #28a745;
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
            Selamat, Anda Diterima!
        </div>
        <div class="content">
            <p>Halo <strong>{{ $mailData['user']->full_name }}</strong>,</p>
            <p>Selamat! Kami dengan senang hati memberitahukan bahwa Anda telah diterima untuk posisi <strong>{{ $mailData['user']->title }}</strong> di <strong>{{ $mailData['user']->company_name }}</strong>.</p>
            <p>Kami sangat antusias menyambut Anda sebagai bagian dari tim kami. Informasi lebih lanjut terkait proses onboarding dan dokumen yang diperlukan akan segera kami kirimkan ke email Anda.</p>
            <p>Terima kasih atas ketertarikan dan dedikasi Anda. Kami berharap dapat segera bekerja sama dengan Anda!</p>
            <p>Salam hangat,</p>
            <p><strong>Tim Rekrutmen {{ $mailData['user']->company_name }}</strong></p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Powered by Ayo Berkarier. Semua Hak Dilindungi.
        </div>
    </div>
</body>
</html>
