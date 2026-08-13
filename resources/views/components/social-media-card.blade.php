@props(['config' => null])

<div class="bg-white border-b border-gray-100 sm:border sm:rounded-lg sm:my-2 overflow-hidden">

    <!-- Post Header -->
    <div class="flex items-center justify-between px-3 py-3">
        <div class="flex items-center gap-3">
            <!-- Story Ring / Profile Picture -->
            <div class="h-9 w-9 rounded-full bg-gradient-to-tr from-yellow-400 via-red-500 to-purple-500 p-[2px]">
                <div class="flex h-full w-full items-center justify-center rounded-full border-2 border-white bg-gray-100">
                    <span class="text-xs font-bold text-gray-700 uppercase">
                        {{ substr($config->username, 0, 1) }}
                    </span>
                </div>
            </div>
            <!-- Username & Location/Platform -->
            <div class="flex flex-col leading-tight">
                <span class="text-sm font-semibold text-gray-900">{{ $config->username }}</span>
                <span class="text-xs text-gray-500">{{ $config->platform ?? 'Instagram' }}</span>
            </div>
        </div>
        <!-- 3 Dots Menu Icon -->
        <button class="text-gray-900 hover:text-gray-500">
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                <circle cx="5" cy="12" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="19" cy="12" r="1.5"/>
            </svg>
        </button>
    </div>

    <!-- Post Content / Link Area -->
    <div class="relative flex aspect-square w-full items-center justify-center bg-gray-50 border-y border-gray-100 group">
        <a href="{{ $config->profile_url }}" target="_blank" rel="noopener" class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center hover:bg-black/5 transition-colors">
            <svg class="h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
            </svg>
            <span class="text-sm font-medium text-[#0095f6] break-all group-hover:underline">
                {{ $config->profile_url }}
            </span>
        </a>
    </div>

    <!-- Post Actions & Caption -->
    <div class="px-3 py-3">
        <!-- Interaction Icons (Like, Comment, Share, Save) -->
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-4">
                <!-- Heart / Like -->
                <svg class="h-6 w-6 text-gray-900 cursor-pointer hover:text-gray-600 transition" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                </svg>
                <!-- Comment -->
                <svg class="h-6 w-6 text-gray-900 cursor-pointer hover:text-gray-600 transition" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 01-.923 1.785A5.969 5.969 0 006 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337z" />
                </svg>
                <!-- Share (Paper Airplane) -->
                <svg class="h-6 w-6 text-gray-900 cursor-pointer hover:text-gray-600 transition" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                </svg>
            </div>
            <!-- Bookmark / Save -->
            <svg class="h-6 w-6 text-gray-900 cursor-pointer hover:text-gray-600 transition" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" />
            </svg>
        </div>

        <!-- Caption Area -->
        @if (filled($config->caption))
            <div class="mt-2 text-sm text-gray-900 leading-relaxed">
                <span class="font-semibold mr-1">{{ $config->username }}</span>
                <span>{{ $config->caption }}</span>
            </div>
        @endif
    </div>
</div>
