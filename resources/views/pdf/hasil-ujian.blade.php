<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Hasil Ujian</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 13px;
            color: #333;
            background-color: #fff;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header img {
            width: 100px;
            height: auto;
            margin-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2c3e50;
        }

        .header h2 {
            margin: 5px 0 0;
            font-size: 18px;
            font-weight: normal;
            color: #555;
        }

        .info {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 5px;
            background-color: #f9f9f9;
            border: 1px solid #ccc;
        }

        .info p {
            margin: 6px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        thead {
            background-color: #2c3e50;
            color: #fff;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ccc;
        }

        tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tbody td {
            text-align: center;
        }

        tbody td:first-child,
        tbody td:nth-child(2) {
            text-align: left;
        }

        .footer {
            margin-top: 40px;
            font-size: 11px;
            text-align: right;
            color: #888;
        }

        @media print {
            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            thead {
                display: table-header-group;
            }

            tfoot {
                display: table-footer-group;
            }

            .header,
            .info,
            .footer {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>

    <div class="header">
        <img src="{{ storage_path('app/public/assets/logo.png') }}" alt="Logo Sekolah">
        <h1>SMP Muhammadiyah 1 Kartosuro</h1>
        <h2>Laporan Hasil Ujian</h2>
    </div>

    <div class="info">
        <p><strong>Nama Ujian:</strong> {{ $hasilUjian->first()->ujian->nama_ujian ?? '-' }}</p>
        <p><strong>Mata Pelajaran:</strong> {{ $hasilUjian->first()->ujian->mapel->nama_mapel ?? '-' }}</p>
        <p><strong>Nama Guru:</strong> {{ $hasilUjian->first()->ujian->guru->nama_guru ?? '-' }}</p>
        <p><strong>Tanggal Cetak:</strong> {{ now()->format('d-m-Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                <th>Jumlah Benar</th>
                <th>Jumlah Salah</th>
                <th>Nilai</th>
            </tr>
        </thead>
        <tbody>
            @foreach($hasilUjian as $index => $hasil)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $hasil->siswa->nama_siswa }}</td>
                <td>{{ $hasil->jumlah_benar }}</td>
                <td>{{ $hasil->jumlah_salah }}</td>
                <td>{{ $hasil->nilai }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d-m-Y H:i') }}
    </div>

</body>

</html>