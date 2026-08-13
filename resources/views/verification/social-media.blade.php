<x-verification-layout>
    @if ($socialMedia !== null && $socialMedia->isComplete())
        <div class="mt-2">
            <!-- Menampilkan Card ala IG -->
            <x-social-media-card :config="$socialMedia" />
        </div>

        <div class="px-4 mt-4">
            <x-verification-form
                type="{{ \App\Enums\VerificationType::SocialMedia->value }}"
                label="Follow {{ $socialMedia->username }}"
                buttonClasses="w-full !bg-[#0095f6] bg-blue-500 hover:bg-blue-600 active:bg-blue-700 text-white font-semibold rounded-lg py-2.5 transition-colors duration-200"
            />
        </div>

    @else
        <div class="mt-6 mx-4 rounded-xl border border-gray-200 bg-gray-50 p-6 text-center">
            <p class="text-sm text-gray-500">Informasi belum tersedia. Hubungi pengirim.</p>
        </div>
    @endif
</x-verification-layout>
