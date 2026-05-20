<script>
    document.addEventListener('DOMContentLoaded', function() {

        document.querySelectorAll('button[type="submit"]:not(.no-auto-submit)')
            .forEach(function(btn) {

                btn.addEventListener('click', function(e) {

                    if (btn.disabled) {
                        e.preventDefault();
                        return;
                    }

                    const form = btn.closest('form');

                    // Keep HTML5 validation
                    if (!form.checkValidity()) {
                        form.reportValidity();
                        return;
                    }

                    // Delay disabling slightly so submit can proceed
                    setTimeout(function() {

                        btn.disabled = true;

                        const icon = btn.querySelector('.fa');
                        if (icon) {
                            icon.classList.add('fa-spin');
                        }

                    }, 0);

                });

            });

    });
</script>

{{-- Excluded button --}}
{{-- <button type="submit" class="no-auto-submit"> --}}