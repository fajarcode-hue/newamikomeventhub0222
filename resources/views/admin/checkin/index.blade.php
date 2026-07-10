@extends('layouts.admin', ['title' => 'Check-in Peserta'])
@section('content')
<header class="mb-10">
    <h1 class="text-3xl font-black">Check-in Peserta</h1>
    <p class="text-slate-500 font-medium">Scan QR tiket peserta untuk verifikasi kehadiran.</p>
</header>

<div class="max-w-lg mx-auto">
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-8">
        <div id="qr-reader" class="rounded-2xl overflow-hidden"></div>
        <div id="result-box" class="mt-6 hidden p-6 rounded-2xl text-center"></div>
        <button id="scan-again" class="hidden w-full mt-4 py-3 bg-slate-100 text-slate-600 rounded-xl font-bold hover:bg-slate-200 transition">
            Scan Lagi
        </button>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    const resultBox = document.getElementById('result-box');
    const scanAgainBtn = document.getElementById('scan-again');
    let isProcessing = false;
    const html5QrCode = new Html5Qrcode("qr-reader");

    function startScanner() {
        resultBox.classList.add('hidden');
        scanAgainBtn.classList.add('hidden');
        isProcessing = false;

        html5QrCode.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: 250 },
            onScanSuccess
        ).catch(err => {
            resultBox.classList.remove('hidden');
            resultBox.className = 'mt-6 p-6 rounded-2xl text-center bg-rose-50 text-rose-600 font-bold';
            resultBox.innerText = 'Gagal mengakses kamera: ' + err;
        });
    }

    function onScanSuccess(decodedText) {
        if (isProcessing) return;
        isProcessing = true;
        html5QrCode.stop().then(() => verifyTicket(decodedText));
    }

    function verifyTicket(qrData) {
        fetch("{{ route('admin.checkin.verify') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ qr_data: qrData })
        })
        .then(res => res.json())
        .then(data => {
            resultBox.classList.remove('hidden');
            scanAgainBtn.classList.remove('hidden');

            let bgClass = 'bg-rose-50 text-rose-600';
            let icon = '❌';
            if (data.status === 'success') { bgClass = 'bg-emerald-50 text-emerald-600'; icon = '✅'; }
            else if (data.status === 'already_checked_in') { bgClass = 'bg-amber-50 text-amber-600'; icon = '⚠️'; }

            resultBox.className = 'mt-6 p-6 rounded-2xl text-center ' + bgClass;

            let detail = '';
            if (data.data) {
                detail = `
                    <p class="font-black text-lg mt-2">${data.data.name}</p>
                    <p class="text-sm mt-1">${data.data.event}</p>
                    <p class="text-xs font-mono mt-1 opacity-70">${data.data.order_id}</p>
                `;
            }

            resultBox.innerHTML = `<p class="text-3xl">${icon}</p><p class="font-bold mt-2">${data.message}</p>${detail}`;
        })
        .catch(() => {
            resultBox.classList.remove('hidden');
            scanAgainBtn.classList.remove('hidden');
            resultBox.className = 'mt-6 p-6 rounded-2xl text-center bg-rose-50 text-rose-600 font-bold';
            resultBox.innerText = 'Terjadi kesalahan saat verifikasi.';
        });
    }

    scanAgainBtn.onclick = startScanner;
    document.addEventListener('DOMContentLoaded', startScanner);
</script>
@endsection