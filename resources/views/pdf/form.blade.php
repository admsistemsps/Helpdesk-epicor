<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{{ $judul }}</title>
    <style>
        @page {
            size: A5 portrait;
            margin: 5mm;
        }

        body {
            font-family: sans-serif;
            font-size: 10px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        td,
        th {
            border: 1px solid #000;
            padding: 2px;
            vertical-align: middle;
        }

        .no-border {
            border: none !important;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .border-0 {
            border: none !important;
        }

        .border-left {
            border-left: 1px solid !important;
        }

        .border-right {
            border-right: 1px solid !important;
        }
    </style>
</head>

<body>
    <table>
        <tr>
            <td rowspan="2" width="15%" class="center">
                @php
                $logoPath = public_path('storage/pt-surya-pangan-semesta-logo.jpg');
                @endphp

                @if(file_exists($logoPath))
                <img src="{{ $logoPath }}" width="50" alt="Logo">
                @endif
            </td>

            <td class="center" colspan="2" style="font-weight:bold;">PT SURYA PANGAN SEMESTA</td>
            <td width="15%">No. Dokumen</td>
            <td width="25%">{{ $ticket->nomor_fuhd ?? '-' }}</td>
        </tr>

        <tr>
            <td colspan="2" class="center" style="font-weight:bold;">FORM UBAH HAPUS DATABASE ALL</td>
            <td>Tanggal Efektif</td>
            <td>{{ $ticket->created_date ? $ticket->created_date->translatedFormat('d F Y') : '-' }}</td>
        </tr>

        <tr>
            <td class="border-0 border-left align-top"> jenis transaksi:</td>
            <td class="border-0" colspan="2">
                1. Database Barang dan Jasa <br>
                2. Database Barang dan Jasa <br>
                3. Database Barang dan Jasa <br>
                4. Database Barang dan Jasa
            </td>
            <td class="border-0 border-right" colspan="2">
                1. Database Barang dan Jasa <br>
                2. Database Barang dan Jasa <br>
                3. Database Barang dan Jasa <br>
                4. Database Barang dan Jasa
            </td>
        </tr>
    </table>

    {{-- Detail perubahan (maks 12 baris) --}}
    <table>
        <tr>
            <th width="5%">No.</th>
            <th width="25%">Alasan Perubahan</th>
            <th width="35%">Sebelum</th>
            <th width="35%">Sesudah</th>
        </tr>

        @php
        $maxRow = 12;
        @endphp

        @foreach ($details as $detail)
        <tr style="height:20px;">
            <td class="center">{{ $detail->ticket_line ?? '' }}</td>
            <td>{{ $ticket->subMenu->name }} <br> {{ $detail->reason ?? '' }}</td>
            <td>{{ ($detail->nomor ? $detail->nomor . ': ' : '') . ($detail->desc_before ?? '') }}</td>
            <td>{{ $detail->desc_after ?? '' }}</td>
        </tr>
        @endforeach

        @for ($i = $details->count() + 1; $i <= $maxRow; $i++)
            <tr style="height:20px;">
            <td class="center">{{ $i }}</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            </tr>
            @endfor
    </table>

    <br><br>

    <table class="no-border">
        <tr class="no-border">
            <td class="no-border right">
                Kediri,
                {{ $approve && $approve->approved_at
                ? \Carbon\Carbon::parse($approve->approved_at)->translatedFormat('d F Y')
                : '_____________' }}
            </td>
        </tr>
    </table>

    <br>

    <table>
        <tr>
            <th>Pembuat</th>
            <th>Approve 1</th>
            <th>Approve 2</th>
            <th>Approve 3</th>
            <th>Approve 4</th>
            <th>Diselesaikan oleh</th>
        </tr>

        <tr style="height:50px;" class="center">
            <td>
                {{ $ticket->created_date ? $ticket->created_date->format('d M Y H:i') : '-' }}<br>
                <img src="{{ public_path('storage/ttd-tester.png') }}" width="50">
                <br>
                {{ $ticket->user->name ?? '-' }}
            </td>

            @for ($i = 0; $i < 4; $i++)
                <td>
                @if(isset($approvals[$i]))
                {{ $approvals[$i]->approved_at ? \Carbon\Carbon::parse($approvals[$i]->approved_at)->format('d M Y H:i') : '-' }}<br>
                <img src="{{ public_path('storage/ttd-tester.png') }}" width="50">
                <br>
                {{ $approvals[$i]->user->name ?? '-' }}
                @endif
                </td>
                @endfor

                <td>
                    {{ $assigned && $assigned->completed_date ? \Carbon\Carbon::parse($assigned->completed_date)->format('d M Y H:i') : '' }}<br>
                    @if($ticket->closed_date)
                    <img src="{{ public_path('storage/ttd-tester.png') }}" width="50">
                    @endif
                    <br>
                    {{ $assigned && $assigned->assignedUser ? $assigned->assignedUser->name : '' }}
                </td>
        </tr>
    </table>

</body>

</html>