@if (config('app.assets_driver') === 'cdn')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toggle = document.getElementById('admin-menu-toggle');
            var sheet = document.getElementById('admin-menu-sheet');
            var backdrop = document.getElementById('admin-menu-backdrop');

            if (!toggle || !sheet || !backdrop) {
                return;
            }

            var open = function () {
                sheet.classList.remove('translate-y-full');
                backdrop.classList.remove('hidden');
                toggle.setAttribute('aria-expanded', 'true');
                document.body.classList.add('overflow-hidden');
            };

            var close = function () {
                sheet.classList.add('translate-y-full');
                backdrop.classList.add('hidden');
                toggle.setAttribute('aria-expanded', 'false');
                document.body.classList.remove('overflow-hidden');
            };

            toggle.addEventListener('click', function () {
                if (sheet.classList.contains('translate-y-full')) {
                    open();
                } else {
                    close();
                }
            });

            backdrop.addEventListener('click', close);

            sheet.querySelectorAll('a').forEach(function (link) {
                link.addEventListener('click', close);
            });
        });
    </script>
@endif
