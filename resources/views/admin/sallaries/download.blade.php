<!DOCTYPE html>
<ht class="fw-bold m-0 p-0"ml lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/core.css" class="template-customizer-core-css" />
    {{-- <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/theme-default.css" class="template-customizer-theme-css" /> --}}
    <link rel="stylesheet" href="{{ asset('assets') }}/css/demo.css" />
    <title>Download Slip Gaji</title>
</head>
<body class="bg-white">
    <main class="bg-white">
        <div class="row border border-dark">
            <table class="table table-borderless">
                <tr>
                    <td>
                        <img src="{{ asset('assets') }}/img/favicon/favicon.png" alt="Logo" class="img-fluid" width="150">
                    </td>
                    <td class="text-center">
                        <h1 class="fw-bold m-0 p-0">SHAKA PRATAMA</h1>
                        <h3 class="mb-2 p-0">Perum Green Alvino Blok B3 No. 5 008/007 Ds. Jamali</h3>
                        <h3 class="mb-2 p-0">Kec. Mande, Kab. Cianjur</h3>
                        <h3 class="mb-2 p-0">Email: shakapratama3823@gmail.com</h3>
                        <h3 class="mb-2 p-0">Tlp: 0858-6055-5516</h3>
                    </td>
                </tr>
            </table>
        </div>
        <div class="row border border-top-0 border-bottom-0 border-dark">
            <div class="col text-center">
                <h4 class="text-decoration-underline fw-bold mt-3 mb-1">SLIP GAJI KARYAWAN</h4>
                <p class="fw-normal text-dark fs-4">Periode {{ \App\Helpers\Muwiza::convertPeriodLitleLong("{$sallary->period_start} - {$sallary->period_end}") }}</p>
            </div>
        </div>
        <div class="row border border-top-0 border-bottom-0 border-dark">
            <div class="col-6">
                <table class="fw-normal fs-3 text-dark table table-borderless">
                    <tr>
                        <td class="text-dark">NIK</td>
                        <td class="text-dark">: {{ $sallary->user->nik }}</td>
                    </tr>
                    <tr>
                        <td class="text-dark">Nama</td>
                        <td class="text-dark">: {{ $sallary->user->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-dark">Jabatan</td>
                        <td class="text-dark">: {{ $sallary->user->access->name }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row border border-top-0 border-bottom-0 border-dark">
            <table class="table table-borderless fw-normal fs-3 text-dark m-0">
                <tr>
                    <td colspan="2" class="text-decoration-underline w-50 ps-5 text-dark">
                        PENGHASILAN
                    </td>
                    <td colspan="2" class="text-decoration-underline w-50 ps-5 text-dark">
                        POTONGAN
                    </td>
                </tr>
                <tr>
                    <td class="text-dark">Gaji</td>
                    <td class="text-dark">: {{ $gaji }}</td>
                    <td class="text-dark">Kasbon</td>
                    <td class="text-dark">: {!! $sallary->kasbon != 0 ? \App\Helpers\Muwiza::ribuan($sallary->kasbon) : '<i class="text-white">..........</i>' !!}</td>
                </tr>
                <tr>
                    <td class="text-dark">Absen</td>
                    <td class="text-dark">: {{ $sallary->uang_absen != 0 ? \App\Helpers\Muwiza::ribuan($sallary->uang_absen) : '' }}</td>
                    @php
                        $showKeep = $sallary->unpaid_keep != 0;
                    @endphp
                    <td class="text-dark">{{ $showKeep ? 'Keep' : ''}}</td>
                    <td class="text-dark">{{ $showKeep ? ': '. \App\Helpers\Muwiza::ribuan($sallary->unpaid_keep) : '' }}</td>
                </tr>
                <tr>
                    <td class="text-dark">Insentif</td>
                    <td class="text-dark">: {{ $sallary->insentive != 0 ? \App\Helpers\Muwiza::ribuan($sallary->insentive) : '' }}</td>
                    <td class="text-dark"></td>
                    <td class="text-dark"></td>
                </tr>
                <tr>
                    <td class="text-dark">Bonus Target</td>
                    <td class="text-dark">: {{ $bonusTarget }}</td>
                    <td class="text-dark"></td>
                    <td class="text-dark"></td>
                </tr>
                <tr>
                    <td class="fw-bold text-dark">
                        Total (A)
                    </td>
                    <td>
                        {{ \App\Helpers\Muwiza::rupiah($sallary->main_sallary + $sallary->uang_absen + $sallary->insentive) }}
                    </td>
                    <td class="fw-bold text-dark">
                        Total (B)
                    </td>
                    <td>
                        {{ \App\Helpers\Muwiza::rupiah($sallary->total_kasbon) }}
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="fw-bold text-dark text-center border border-start-0 border-end-0 border-dark" style="background: #eee;">
                        @php
                            $nominal = \App\Helpers\Muwiza::rupiah(abs($sallary->total));
                        @endphp
                        PENERIMAAN BERSIH (A - B) = {{ ($sallary->total < 0 ? '- ' : '') . $nominal }} 
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="text-dark text-center border border-start-0 border-end-0 border-dark" style="background: #eee; font-style:italic">
                        @php
                            $terbilang = \App\Helpers\Muwiza::terbilang($sallary->total);
                        @endphp
                        Terbilang : {{ $terbilang }}
                    </td>
                </tr>
            </table>
        </div>
        <div class="row border border-top-0 border-dark ">
            <table class="table table-borderless fw-normal fs-3 text-dark">
                <tr>
                    <td class="w-50 ps-5 text-dark text-start">
                        Cianjur, {{ \App\Helpers\Muwiza::simpleDate($sallary->period_end) }} <br> Dirut Shaka Pratama
                    </td>
                    <td class="w-50 pe-5 text-dark text-end">
                        Diterima Oleh
                    </td>
                </tr>
                <tr>
                    <td class="w-50 ps-5 text-dark text-start">
                        <img src="{{ asset('assets') }}/img/cap-shaka.png" alt="" class="img-fluid" width="150">
                    </td>
                    <td class="w-50 pe-5 text-dark text-end">
                        
                    </td>
                </tr>
                <tr>
                    <td class="w-50 ps-5 text-dark text-start">
                        Hendri Mulyana Sanusi, S. T
                    </td>
                    <td class="w-50 pe-5 text-dark text-end">
                        {{ $sallary->user->name }}
                    </td>
                </tr>
            </table>
        </div>
    </main>
</body>
</ht>