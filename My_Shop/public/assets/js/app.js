// Menu mobile + petites améliorations (aucun script inline : compatible avec la CSP)
document.addEventListener('DOMContentLoaded', function () {
    var toggle = document.getElementById('menu-toggle');
    var nav = document.getElementById('main-nav');
    if (toggle && nav) {
        toggle.addEventListener('click', function () {
            var open = nav.classList.toggle('open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }

    // Confirmation avant les actions destructrices : <form data-confirm="Message">
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!window.confirm(form.getAttribute('data-confirm'))) {
                e.preventDefault();
            }
        });
    });

    // Tri : envoi automatique du formulaire de filtres
    document.querySelectorAll('select[data-autosubmit]').forEach(function (select) {
        select.addEventListener('change', function () {
            if (select.form) { select.form.submit(); }
        });
    });
});
