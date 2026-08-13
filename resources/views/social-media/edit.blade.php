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

            <div x-data="{
                platform: @js(old('platform', $socialMedia?->platform ?? '')),
                username: @js(old('username', $socialMedia?->username ?? '')),
                profile_url: @js(old('profile_url', $socialMedia?->profile_url ?? '')),
                caption: @js(old('caption', $socialMedia?->caption ?? '')),
            }" class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-100 sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form method="POST" action="{{ route('social-media.update') }}" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <div>
                                <x-input-label for="platform" value="Platform" />
                                <x-text-input id="platform" name="platform" type="text"
                                              x-model="platform"
                                              class="mt-1 block w-full"
                                              :value="old('platform', $socialMedia?->platform)" />
                                <x-input-error :messages="$errors->get('platform')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="username" value="Username" />
                                <x-text-input id="username" name="username" type="text"
                                              x-model="username"
                                              class="mt-1 block w-full"
                                              :value="old('username', $socialMedia?->username)" />
                                <x-input-error :messages="$errors->get('username')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="profile_url" value="URL Profil" />
                                <x-text-input id="profile_url" name="profile_url" type="url"
                                              x-model="profile_url"
                                              class="mt-1 block w-full"
                                              :value="old('profile_url', $socialMedia?->profile_url)" />
                                <x-input-error :messages="$errors->get('profile_url')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="caption" value="Caption" />
                                <textarea id="caption" name="caption" rows="3" x-model="caption"
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
                                <a href="{{ route('verification.social-media') }}" target="_blank"
                                   class="text-sm font-medium text-indigo-600 hover:text-indigo-900">
                                    {{ __('Lihat halaman publik') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="lg:sticky lg:top-6">
                    <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-100 sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Pratinjau</h3>
                            <p class="mt-1 text-xs text-gray-400">Tampilan kartu profil di halaman publik saat admin melengkapi data.</p>

                            <div class="mt-4 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-lg">
                                <div class="flex items-center gap-3 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white p-4">
                                    <div class="flex-shrink-0">
                                        <div class="h-12 w-12 rounded-full bg-gradient-to-tr from-yellow-400 via-red-500 to-purple-600 p-[2px]">
                                            <div class="flex h-full w-full items-center justify-center rounded-full bg-white">
                                                <span class="text-sm font-bold text-gray-700" x-text="(platform || '?').charAt(0).toUpperCase()"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-semibold text-gray-900" x-text="username || '—'"></p>
                                        <p class="text-xs text-gray-500" x-text="platform || '—'"></p>
                                    </div>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-600">
                                        @ <span x-text="username || '—'"></span>
                                    </span>
                                </div>

                                <div class="p-4">
                                    <p class="text-sm leading-relaxed text-gray-800" x-text="caption || '—'"></p>
                                    <div class="mt-3 flex items-center gap-2 rounded-lg border border-gray-100 bg-gray-50 p-2.5 text-sm text-blue-500">
                                        <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                        </svg>
                                        <span class="truncate" x-text="profile_url || '—'"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyVerifyLink() {
            var url = '{{ route('verification.social-media') }}';

            navigator.clipboard.writeText(url).then(function () {
                alert('Link disalin: ' + url);
            });
        }
    </script>
</x-app-layout>
