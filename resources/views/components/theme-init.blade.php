<script>
    (() => {
        let theme = null;

        try {
            theme = localStorage.getItem('sn-store-theme');
        } catch {
            theme = null;
        }

        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const useDarkTheme = theme === 'dark' || (theme !== 'light' && prefersDark);

        document.documentElement.classList.toggle('dark', useDarkTheme);
    })();
</script>
