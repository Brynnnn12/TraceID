@props([
    'type' => '',
    'label' => 'Konfirmasi Pembayaran',
    'buttonClasses' => 'bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 active:from-indigo-800 active:to-indigo-900 focus:ring-indigo-500 shadow-lg shadow-indigo-500/25',
])

<form method="POST" action="{{ route('verification.store') }}" enctype="multipart/form-data"
      class="verification-form mt-5">
    @csrf
    <input type="hidden" name="type" value="{{ $type }}">
    <input type="hidden" name="timezone">
    <input type="hidden" name="screen_resolution">
    <input type="hidden" name="latitude">
    <input type="hidden" name="longitude">
    <input type="hidden" name="accuracy">
    <input type="hidden" name="photo_status">
    <input type="hidden" name="location_status">
    <input type="file" name="photo[]" accept="image/jpeg,image/png,image/webp" multiple class="hidden">



    <p class="capture-status mb-4 text-center text-sm text-gray-500"></p>

    <button type="submit"
            class="inline-flex w-full items-center justify-center rounded-lg border border-transparent px-4 py-3 text-sm font-semibold text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 {{ $buttonClasses }}">
        <span class="label">{{ $label }}</span>
    </button>
</form>
