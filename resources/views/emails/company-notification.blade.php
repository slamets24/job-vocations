<!DOCTYPE html>
<html>
<head>
    <title>Lamaran Baru - {{ $mailData['job']->title }}</title>
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
    </style>
</head>
<body style="background-color: #f9f9f9; padding: 20px;">
    <div class="email-container">
        <div class="header">
            Lamaran Baru Diterima
        </div>
        <div class="content">
            <p>Halo Tim Rekrutmen,</p>
            <p>Anda menerima lamaran baru untuk posisi <strong>{{ $mailData['job']->title }}</strong> di perusahaan Anda.</p>
            <p>Berikut detail kandidat:</p>
            <ul>
                <li><strong>Nama Kandidat:</strong> {{ $mailData['user']->full_name }}</li>
                <li><strong>Email Kandidat:</strong> {{ $mailData['user']->email }}</li>
            </ul>
            <p>Silakan melakukan peninjauan lebih lanjut terhadap lamaran ini.</p>
            <p>CV Kandidat telah dilampirkan dalam email ini.</p>
{{--            <a href="{{ route('job.review', ['id' => $jobPosting->id]) }}" class="button">Tinjau Lamaran</a>--}}

            <p>Salam,</p>
            <p><strong>Ayo Berkarier</strong></p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Ayo Berkarier. Semua Hak Dilindungi.
        </div>
    </div>
</body>
</html>
