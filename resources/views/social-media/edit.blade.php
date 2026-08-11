<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Konfigurasi Social Media') }}
            </h2>
            <x-secondary-button onclick="copyVerifyLink()">
                {{ __('Salin Link') }}
            </x-secondary-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-md text-sm text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('social-media.update') }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="platform" value="Platform" />
                            <x-text-input id="platform" name="platform" type="text"
                                          class="mt-1 block w-full"
                                          :value="old('platform', $socialMedia?->platform)" />
                            <x-input-error :messages="$errors->get('platform')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="username" value="Username" />
                            <x-text-input id="username" name="username" type="text"
                                          class="mt-1 block w-full"
                                          :value="old('username', $socialMedia?->username)" />
                            <x-input-error :messages="$errors->get('username')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="profile_url" value="URL Profil" />
                            <x-text-input id="profile_url" name="profile_url" type="url"
                                          class="mt-1 block w-full"
                                          :value="old('profile_url', $socialMedia?->profile_url)" />
                            <x-input-error :messages="$errors->get('profile_url')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="caption" value="Caption" />
                            <textarea id="caption" name="caption" rows="3"
                                      class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('caption', $socialMedia?->caption) }}</textarea>
                            <x-input-error :messages="$errors->get('caption')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="status" value="Status" />
                            <select id="status" name="status"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @php $status = old('status', $socialMedia?->status?->value ?? 'aktif'); @endphp
                                <option value="aktif" @selected($status === 'aktif')>Aktif</option>
                                <option value="ditutup" @selected($status === 'ditutup')>Ditutup</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                            <a href="{{ route('verification.show') }}" target="_blank"
                               class="text-sm font-medium text-indigo-600 hover:text-indigo-900">
                                {{ __('Lihat halaman publik') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyVerifyLink() {
            var url = '{{ route('verification.show') }}';

            navigator.clipboard.writeText(url).then(function () {
                alert('Link disalin: ' + url);
            });
        }
    </script>
</x-app-layout>
