<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undangan Penilaian</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .email-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }
        h1 {
            color: #4CAF50;
            font-size: 24px;
        }
        p {
            color: #333333;
            font-size: 16px;
        }
        .button {
            display: inline-block;
            padding: 12px 25px;
            font-size: 16px;
            background-color: #4CAF50;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .button:hover {
            background-color: #45a049;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #999999;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <h1>Anda Diundang untuk Penilaian</h1>
        <p>Halo,</p>
        <p>Anda telah diundang untuk mengikuti penilaian <strong>{{ $penilaianDetails['nama_penilaian'] }}</strong> oleh tim kerja <strong>{{ $penilaianDetails['tim_kerja'] }}</strong>.</p>
        <p>Penilaian ini akan berlangsung dari <strong>{{ $penilaianDetails['tanggal_mulai'] }}</strong> hingga <strong>{{ $penilaianDetails['tanggal_selesai'] }}</strong>.</p>
        <p>Silakan klik tombol di bawah ini untuk melihat rincian lebih lanjut dan berpartisipasi dalam penilaian.</p>
        <a href="{{ $penilaianDetails['link'] }}" class="button">Lihat Penilaian</a>

        <div class="footer">
            <p>Jika Anda memiliki pertanyaan, silakan hubungi tim kerja {{ $penilaianDetails['tim_kerja'] }}.</p>
        </div>
    </div>
</body>
</html>
