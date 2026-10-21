<button
    type="button"
    data-theme-toggle
    @click="$store.theme.toggle()"
    :aria-label="$store.theme.isDark ? 'Aktifkan tema terang' : 'Aktifkan tema gelap'"
    :title="$store.theme.isDark ? 'Aktifkan tema terang' : 'Aktifkan tema gelap'"
    class="flex size-10 shrink-0 items-center justify-center rounded-full border border-zinc-200 bg-white text-zinc-700 transition hover:border-red-300 hover:text-red-700 active:scale-[0.96] dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-red-700 dark:hover:text-red-300"
>
    <svg x-cloak x-show="! $store.theme.isDark" class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <circle cx="12" cy="12" r="3.75" stroke="currentColor" stroke-width="1.7"/>
        <path d="M12 2.75v1.5m0 15.5v1.5m9.25-9.25h-1.5m-15.5 0h-1.5m15.29-6.54-1.06 1.06M7.02 16.98l-1.06 1.06m13.08 0-1.06-1.06M7.02 7.02 5.96 5.96" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
    </svg>
    <svg x-cloak x-show="$store.theme.isDark" class="size-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M20.2 15.1A8.4 8.4 0 0 1 8.9 3.8 8.5 8.5 0 1 0 20.2 15.1Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    <span class="sr-only" x-text="$store.theme.isDark ? 'Tema gelap aktif' : 'Tema terang aktif'"></span>
</button>
