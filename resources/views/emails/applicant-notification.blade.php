<!DOCTYPE html>
<html>
<head>
    <title>{{ $mailData['subject'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
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
            background-color: #4CAF50;
            color: white;
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
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }
        .warning {
            color: #d9534f;
            font-size: 14px;
            margin-top: 20px;
            font-style: italic;
        }
    </style>
</head>
<body style="background-color: #f9f9f9; padding: 20px;">
    <div class="email-container">
        <div class="header">
            Terima Kasih, {{ $mailData['user']->full_name }}!
        </div>
        <div class="content">
            <p>Halo <strong>{{ $mailData['user']->full_name }}</strong>,</p>
            <p>Terima kasih telah mengirimkan lamaran untuk posisi <strong>{{ $mailData['job']->title }}</strong> di <strong>{{ $mailData['job']->company_name }}</strong>.</p>
            <p>Lamaran Anda telah kami terima, dan kami akan segera meninjau dokumen Anda. Kami akan memberikan pembaruan terkait proses selanjutnya.</p>
            <p>Setiap perusahaan memiliki proses rekrutmen yang berbeda, jadi mohon bersabar jika belum ada kabar. Anda juga dapat melacak status lamaran Anda melalui <a href="{{ route('applications.proposed') }}">Lamaran Saya</a>.</p>
            <p class="warning">Peringatan: Jangan pernah memberikan informasi sensitif seperti data perbankan atau kartu kredit selama proses rekrutmen.</p>
            <p>Semoga sukses!</p>
            <p>Salam hangat,</p>
            <p><strong>Tim Rekrutmen {{ $mailData['job']->company_name }}</strong></p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Powered by Ayo Berkarier. Semua Hak Dilindungi.
        </div>
    </div>
</body>
</html>
