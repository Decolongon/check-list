<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ __('Welcome') }} - {{ config('app.name', 'Check List') }}</title>
    <link rel="icon" href="/favicon.ico" sizes="any" />
    <link rel="icon" href="/favicon.svg" type="image/svg+xml" />
    <link rel="apple-touch-icon" href="/apple-touch-icon.png" />
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
    <style>body{font-family:'Instrument Sans',ui-sans-serif,system-ui,sans-serif}</style>
    <script>
        // Early theme apply for welcome (works with Flux + fallback) — ensures light/dark visible instantly
        (function(){
            try{
                var stored = localStorage.getItem('flux.appearance') || localStorage.getItem('theme');
                var mql = window.matchMedia('(prefers-color-scheme: dark)');
                var isDark = stored ? (stored === 'dark' || stored === '"dark"') : mql.matches;
                document.documentElement.classList.toggle('dark', isDark);
                // keep sync for Flux
                if(!localStorage.getItem('flux.appearance') && stored) localStorage.setItem('flux.appearance', isDark ? 'dark' : 'light');
            }catch(e){}
        })();
    </script>
</head>
<body class="min-h-screen bg-[#FDFDFC] text-[#1b1b18] antialiased dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
    <!-- Header -->
    <header class="w-full max-w-5xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between gap-4">
        <a href="/" class="flex items-center gap-2.5 shrink-0">
            <span class="size-8 rounded-lg bg-[#4F46E5] flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="size-5" aria-hidden="true">
                    <rect width="32" height="32" rx="7" fill="#4F46E5"/>
                    <rect x="7" y="7" width="6" height="6" rx="1.4" fill="#ffffff"/>
                    <path d="M9 10.2 L10.9 12.1 L13.1 9.2" stroke="#4F46E5" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                    <rect x="15.2" y="8.1" width="9.8" height="2" rx="1" fill="#ffffff"/>
                    <rect x="15.2" y="11" width="7" height="1.35" rx="0.67" fill="#ffffff" opacity="0.65"/>
                    <rect x="7" y="14.2" width="6" height="6" rx="1.4" fill="#ffffff"/>
                    <path d="M9 17.4 L10.9 19.3 L13.1 16.4" stroke="#4F46E5" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                    <rect x="15.2" y="15.3" width="9.8" height="2" rx="1" fill="#ffffff"/>
                    <rect x="15.2" y="18.2" width="7" height="1.35" rx="0.67" fill="#ffffff" opacity="0.65"/>
                    <rect x="7" y="21.4" width="6" height="6" rx="1.4" fill="none" stroke="#ffffff" stroke-width="1.2" opacity="0.95"/>
                    <rect x="15.2" y="22.5" width="9.8" height="2" rx="1" fill="#ffffff" opacity="0.9"/>
                    <rect x="15.2" y="25.4" width="5.5" height="1.35" rx="0.67" fill="#ffffff" opacity="0.5"/>
                </svg>
            </span>
            <span class="text-[15px] font-medium tracking-tight">{{ config('app.name', 'Check List') }}</span>
        </a>
        <div class="flex items-center gap-2 sm:gap-3 shrink-0">
            <!-- Light / Dark toggle - works on all screen sizes, persists -->
            <button id="theme-toggle" type="button" aria-label="Toggle light and dark mode" class="inline-flex size-8 items-center justify-center rounded-md border border-[#e3e3e0] bg-white hover:bg-[#f5f5f3] dark:border-[#2a2a28] dark:bg-[#161615] dark:hover:bg-[#1e1e1c] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#4F46E5]">
                <svg class="size-4 hidden dark:block" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M12 3h.01"/><path d="M21 12a9 9 0 1 1-9-9c2.5 0 4.8 1 6.5 2.5"/><path d="M12 16a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/><path d="M12 22v-2"/><path d="M20 12h2"/><path d="M4 12H2"/><path d="M18 7l1.5-1.5"/><path d="M6 17l-1.5 1.5"/></svg>
                <svg class="size-4 block dark:hidden" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="M4.93 4.93l1.41 1.41"/><path d="M17.66 17.66l1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="M6.34 17.66l-1.41 1.41"/><path d="M19.07 4.93l-1.41 1.41"/></svg>
            </button>
            @if (Route::has('login'))
                <nav class="flex items-center gap-2 sm:gap-3 text-sm">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-md bg-[#1b1b18] px-4 py-1.5 text-white hover:bg-black dark:bg-white dark:text-[#1b1b18] dark:hover:bg-[#EDEDEC]">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="hidden sm:inline px-3 py-1.5 hover:opacity-70">Log in</a>
                        <a href="{{ route('login') }}" class="sm:hidden px-3 py-1.5 text-sm hover:opacity-70">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="rounded-md border border-[#19140035] px-3 sm:px-4 py-1.5 hover:border-[#1915014a] dark:border-[#3E3E3A] dark:hover:border-[#62605b]">Register</a>
                        @endif
                    @endauth
                </nav>
            @endif
        </div>
    </header>

    <main class="w-full">
        <!-- Hero - centered, works on all screens -->
        <section class="px-4 sm:px-6 pt-6 sm:pt-10 pb-8 flex justify-center">
            <div class="w-full max-w-[560px] text-center">
                <div class="mx-auto mb-5 sm:mb-6 flex size-14 sm:size-14 items-center justify-center rounded-2xl bg-[#4F46E5] shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="size-8" aria-hidden="true">
                        <rect width="32" height="32" rx="7" fill="#4F46E5"/>
                        <rect x="7" y="7" width="6" height="6" rx="1.4" fill="#ffffff"/>
                        <path d="M9 10.2 L10.9 12.1 L13.1 9.2" stroke="#4F46E5" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                        <rect x="15.2" y="8.1" width="9.8" height="2" rx="1" fill="#ffffff"/>
                        <rect x="15.2" y="11" width="7" height="1.35" rx="0.67" fill="#ffffff" opacity="0.65"/>
                        <rect x="7" y="14.2" width="6" height="6" rx="1.4" fill="#ffffff"/>
                        <path d="M9 17.4 L10.9 19.3 L13.1 16.4" stroke="#4F46E5" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                        <rect x="15.2" y="15.3" width="9.8" height="2" rx="1" fill="#ffffff"/>
                        <rect x="15.2" y="18.2" width="7" height="1.35" rx="0.67" fill="#ffffff" opacity="0.65"/>
                        <rect x="7" y="21.4" width="6" height="6" rx="1.4" fill="none" stroke="#ffffff" stroke-width="1.2" opacity="0.95"/>
                        <rect x="15.2" y="22.5" width="9.8" height="2" rx="1" fill="#ffffff" opacity="0.9"/>
                        <rect x="15.2" y="25.4" width="5.5" height="1.35" rx="0.67" fill="#ffffff" opacity="0.5"/>
                    </svg>
                </div>
                <h1 class="text-[26px] sm:text-[30px] font-medium tracking-tight leading-none">Check List</h1>
                <p class="mt-3 text-[14px] sm:text-[15px] leading-6 text-[#706f6c] dark:text-[#A1A09A] px-2 sm:px-0">
                    A simple, focused way to organize tasks.<br class="hidden sm:block"> Stay on track — one check at a time.
                </p>
                <div class="mt-7 flex flex-col sm:flex-row items-stretch sm:items-center justify-center gap-3 px-2 sm:px-0">
                    @auth
                        <a href="{{ route('dashboard') }}" class="w-full sm:w-auto inline-flex justify-center rounded-md bg-[#4F46E5] px-6 py-2.5 text-sm font-medium text-white hover:bg-[#4338CA] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#4F46E5]">Go to dashboard</a>
                    @else
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex justify-center rounded-md bg-[#4F46E5] px-6 py-2.5 text-sm font-medium text-white hover:bg-[#4338CA] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#4F46E5]">Get started</a>
                        @endif
                        <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex justify-center rounded-md border border-[#e3e3e0] bg-white px-6 py-2.5 text-sm font-medium hover:bg-[#f5f5f3] dark:border-[#3E3E3A] dark:bg-[#161615] dark:hover:bg-[#1e1e1c]">Log in</a>
                    @endauth
                </div>
                <p class="mt-4 text-xs text-[#A1A09A] dark:text-[#6b6a65]">No clutter. Just your lists. Works offline.</p>
            </div>
        </section>

        <!-- Additional Content: Features + Preview - responsive grid, light/dark visible -->
        <section class="w-full max-w-5xl mx-auto px-4 sm:px-6 pb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-4">
                <div class="rounded-xl border border-[#e3e3e0] bg-white p-4 sm:p-5 dark:border-[#2a2a28] dark:bg-[#161615]">
                    <div class="size-8 rounded-lg bg-[#EEF2FF] dark:bg-[#2a2a6b] flex items-center justify-center mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-4 text-[#4F46E5] dark:text-[#818cf8]" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="m9 14 2 2 4-4"/></svg>
                    </div>
                    <h3 class="text-sm font-medium">Organize by category</h3>
                    <p class="mt-1 text-sm leading-5 text-[#706f6c] dark:text-[#A1A09A]">Group tasks into lists — work, personal, shopping. Switch in one tap.</p>
                </div>
                <div class="rounded-xl border border-[#e3e3e0] bg-white p-4 sm:p-5 dark:border-[#2a2a28] dark:bg-[#161615]">
                    <div class="size-8 rounded-lg bg-[#EEF2FF] dark:bg-[#2a2a6b] flex items-center justify-center mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-4 text-[#4F46E5] dark:text-[#818cf8]" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 8v4l3 3"/><circle cx="12" cy="12" r="9"/></svg>
                    </div>
                    <h3 class="text-sm font-medium">Track progress</h3>
                    <p class="mt-1 text-sm leading-5 text-[#706f6c] dark:text-[#A1A09A]">See completed vs pending at a glance. Bulk check, clear done.</p>
                </div>
                <div class="rounded-xl border border-[#e3e3e0] bg-white p-4 sm:p-5 dark:border-[#2a2a28] dark:bg-[#161615]">
                    <div class="size-8 rounded-lg bg-[#EEF2FF] dark:bg-[#2a2a6b] flex items-center justify-center mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-4 text-[#4F46E5] dark:text-[#818cf8]" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/><path d="m9 12 2 2 4-4"/></svg>
                    </div>
                    <h3 class="text-sm font-medium">Simple & private</h3>
                    <p class="mt-1 text-sm leading-5 text-[#706f6c] dark:text-[#A1A09A]">No ads, no complexity. Your lists stay yours — light or dark mode.</p>
                </div>
            </div>
        </section>

        <!-- Preview / How it works - responsive -->
        <section class="w-full max-w-5xl mx-auto px-4 sm:px-6 pb-10">
            <div class="rounded-xl border border-[#e3e3e0] bg-white overflow-hidden dark:border-[#2a2a28] dark:bg-[#161615]">
                <div class="grid grid-cols-1 lg:grid-cols-2">
                    <div class="p-5 sm:p-6 lg:p-8">
                        <h2 class="text-base font-medium">Your lists, at a glance</h2>
                        <p class="mt-1.5 text-sm leading-5 text-[#706f6c] dark:text-[#A1A09A]">Create categories, add checks, and focus on what is next. Works great on phone, tablet and desktop.</p>
                        <ul class="mt-5 space-y-2.5 text-sm">
                            <li class="flex items-center gap-2.5"><span class="size-5 rounded-md border border-[#4F46E5] bg-[#4F46E5] flex items-center justify-center shrink-0"><svg class="size-3 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"/></svg></span><span class="line-through text-[#A1A09A] dark:text-[#6b6a65]">Buy groceries</span></li>
                            <li class="flex items-center gap-2.5"><span class="size-5 rounded-md border border-[#4F46E5] bg-[#4F46E5] flex items-center justify-center shrink-0"><svg class="size-3 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"/></svg></span><span class="line-through text-[#A1A09A] dark:text-[#6b6a65]">Finish report</span></li>
                            <li class="flex items-center gap-2.5"><span class="size-5 rounded-md border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#0a0a0a] shrink-0"></span><span>Plan weekend trip</span></li>
                            <li class="flex items-center gap-2.5"><span class="size-5 rounded-md border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#0a0a0a] shrink-0"></span><span>Call mom</span></li>
                        </ul>
                        <div class="mt-6 flex gap-3">
                            <span class="inline-flex items-center rounded-full bg-[#EEF2FF] dark:bg-[#2a2a6b] px-2.5 py-1 text-xs font-medium text-[#4F46E5] dark:text-[#c7d2fe]">3 done</span>
                            <span class="inline-flex items-center rounded-full border border-[#e3e3e0] dark:border-[#2a2a28] px-2.5 py-1 text-xs dark:text-[#A1A09A]">2 pending</span>
                        </div>
                    </div>
                    <div class="bg-[#F5F5FF] dark:bg-[#0f0f1e] p-5 sm:p-6 lg:p-8 flex items-center border-t lg:border-t-0 lg:border-l border-[#e3e3e0] dark:border-[#2a2a28]">
                        <div class="w-full rounded-lg border border-[#e3e3e0] bg-white p-4 dark:border-[#2a2a28] dark:bg-[#1a1a1a]">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-medium tracking-wide opacity-70">TODAY</span>
                                <span class="text-xs px-2 py-0.5 rounded-full bg-[#4F46E5] text-white">4 items</span>
                            </div>
                            <div class="space-y-2">
                                <div class="h-2.5 rounded-full bg-[#4F46E5] w-[85%]"></div>
                                <div class="h-2 rounded-full bg-[#e3e3e0] dark:bg-[#2a2a28] w-[70%]"></div>
                                <div class="h-2 rounded-full bg-[#e3e3e0] dark:bg-[#2a2a28] w-[55%]"></div>
                            </div>
                            <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                                <div class="rounded-md bg-[#FDFDFC] dark:bg-[#0a0a0a] border border-[#e3e3e0] dark:border-[#2a2a28] py-2"><div class="text-sm font-medium">12</div><div class="text-[11px] opacity-60">Total</div></div>
                                <div class="rounded-md bg-[#4F46E5] text-white py-2"><div class="text-sm font-medium">7</div><div class="text-[11px] opacity-80">Done</div></div>
                                <div class="rounded-md bg-[#FDFDFC] dark:bg-[#0a0a0a] border border-[#e3e3e0] dark:border-[#2a2a28] py-2"><div class="text-sm font-medium">5</div><div class="text-[11px] opacity-60">Left</div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Responsive note -->
            <p class="mt-3 text-center text-[11px] text-[#A1A09A] dark:text-[#6b6a65]">Responsive — try resizing or switching light/dark. <span class="hidden sm:inline">Optimized for phone, tablet & desktop.</span></p>
        </section>
    </main>

    <footer class="py-6 text-center text-xs text-[#A1A09A] dark:text-[#6b6a65] border-t border-[#e3e3e0]/60 dark:border-[#1a1a1a]">
        © {{ date('Y') }} {{ config('app.name', 'Check List') }} — Simple lists, done.
    </footer>

    <script>
        // Light / Dark toggle - works across reloads, respects system, syncs with Flux
        (function(){
            var btn = document.getElementById('theme-toggle');
            if(!btn) return;
            function isDark(){ return document.documentElement.classList.contains('dark'); }
            function setDark(dark){
                document.documentElement.classList.toggle('dark', dark);
                try{
                    localStorage.setItem('theme', dark ? 'dark' : 'light');
                    localStorage.setItem('flux.appearance', dark ? 'dark' : 'light');
                    // Flux also uses 'flux.appearance' JSON string? store both
                    localStorage.setItem('flux:appearance', JSON.stringify(dark ? 'dark' : 'light'));
                }catch(e){}
            }
            btn.addEventListener('click', function(){ setDark(!isDark()); });
            // keep in sync with system changes if no explicit choice
            try{
                var mql = window.matchMedia('(prefers-color-scheme: dark)');
                mql.addEventListener('change', function(e){
                    var stored = localStorage.getItem('flux.appearance') || localStorage.getItem('theme');
                    if(!stored) setDark(e.matches);
                });
            }catch(e){}
        })();
    </script>
</body>
</html>
