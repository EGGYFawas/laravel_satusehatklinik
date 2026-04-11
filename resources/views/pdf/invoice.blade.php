<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pembayaran</title>
    @php
        // Mengambil data pengaturan klinik dari database
        $setting = \App\Models\ClinicSetting::first();
        $namaKlinik = $setting?->name ?? $klinik_name ?? 'Klinik Sehat';
        $alamatKlinik = $setting?->address ?? $klinik_address ?? 'Jl. Kesehatan No. 123, Jakarta';
        $telpKlinik = $setting?->phone ?? '(021) 555-1234';
        $emailKlinik = $setting?->email ?? 'info@kliniksehat.com';
    @endphp
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10pt; color: #333; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #008080; font-size: 16pt; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 9pt; color: #666; }
        .meta-info { width: 100%; margin-bottom: 20px; }
        .meta-info td { padding: 3px; vertical-align: top; }
        .label { font-weight: bold; width: 130px; }
        .table-items { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table-items th, .table-items td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table-items th { background-color: #f2f2f2; font-weight: bold; font-size: 9pt; }
        .text-right { text-align: right; }
        .total-row td { font-weight: bold; background-color: #f9f9f9; }
        .footer { margin-top: 30px; text-align: center; font-size: 8pt; color: #888; }
        .status-paid { color: #008000; border: 1px solid #008000; padding: 2px 8px; border-radius: 4px; font-weight: bold; display: inline-block; font-size: 9pt; }
        .status-bpjs { color: #0284c7; border: 1px solid #0284c7; padding: 2px 8px; border-radius: 4px; font-weight: bold; display: inline-block; font-size: 9pt; background-color: #e0f2fe; }
        .petugas-section { margin-top: 40px; text-align: right; padding-right: 20px; }
        .petugas-name { margin-top: 60px; font-weight: bold; text-decoration: underline; }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ $namaKlinik }}</h1>
        <p>{{ $alamatKlinik }}</p>
        <p>Telp: {{ $telpKlinik }} | Email: {{ $emailKlinik }}</p>
    </div>

    <table class="meta-info">
        <tr>
            <td class="label">No. Invoice</td>
            <td>: {{ $prescription->midtrans_booking_code ?? 'INV-' . $prescription->id }}</td>
            <td class="label">Tanggal Cetak</td>
            <td>: {{ $date_print }}</td>
        </tr>
        <tr>
            <td class="label">Nama Pasien</td>
            <td>: {{ $patient->full_name }}</td>
            <td class="label">Metode Bayar</td>
            <td>: {{ ucfirst($prescription->payment_method ?? 'Tunai/Cashless') }}</td>
        </tr>
        <tr>
            <td class="label">Status Pembayaran</td>
            <td>: 
                @if($is_bpjs)
                    <span class="status-bpjs">DITANGGUNG BPJS</span>
                @else
                    <span class="status-paid">LUNAS</span>
                @endif
            </td>
            <td class="label">Waktu Ambil Obat</td>
            <td>: {{ $taken_time }}</td>
        </tr>
    </table>

    <table class="table-items">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th>Nama Obat / Layanan</th>
                <th style="width: 10%; text-align: center;">Total Kuantitas</th>
                <th style="width: 20%; text-align: right;">Harga Satuan</th>
                <th style="width: 20%; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($details as $detail)
            <tr>
                <td style="text-align: center;">{{ $no++ }}</td>
                <td>{{ $detail->medicine->name }}</td>
                <td style="text-align: center;">{{ $detail->quantity }}</td>
                <td class="text-right">Rp {{ number_format($detail->medicine->price, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($detail->medicine->price * $detail->quantity, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            
            @if(isset($actions) && count($actions) > 0)
                @foreach($actions as $action)
                <tr>
                    <td style="text-align: center;">{{ $no++ }}</td>
                    <td>
                        {{ $action->action_name }}
                        @if($action->result_notes && $action->result_notes !== '-')
                            <br><small style="color: #666; font-size: 8pt;">Hasil: {{ $action->result_notes }}</small>
                        @endif
                    </td>
                    <td style="text-align: center;">1</td>
                    <td class="text-right">Rp {{ number_format($action->price, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($action->price, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td style="text-align: center;">{{ $no++ }}</td>
                    <td>Jasa Konsultasi Dokter (Default)</td>
                    <td style="text-align: center;">1</td>
                    <td class="text-right">Rp 15.000</td>
                    <td class="text-right">Rp 15.000</td>
                </tr>
            @endif
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="text-right">Total Tagihan</td>
                <td class="text-right">Rp {{ number_format($prescription->total_price, 0, ',', '.') }}</td>
            </tr>
            @if(!$is_bpjs)
                <tr>
                    <td colspan="4" class="text-right">Tunai / Bayar</td>
                    <td class="text-right">Rp {{ number_format($prescription->amount_paid ?? $prescription->total_price, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="text-right">Kembalian</td>
                    <td class="text-right">Rp {{ number_format($prescription->change_amount ?? 0, 0, ',', '.') }}</td>
                </tr>
            @endif
        </tfoot>
    </table>

    <div class="petugas-section">
        <p>Petugas Kasir / Farmasi,</p>
        <div class="petugas-name">( {{ $petugas }} )</div>
    </div>

    <div class="footer">
        <p>Terima kasih atas kepercayaan Anda.</p>
        <p>Dokumen ini adalah bukti transaksi yang sah.</p>
    </div>

</body>
</html>