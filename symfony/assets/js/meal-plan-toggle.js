document.addEventListener('turbo:load', () => {
    document.querySelectorAll('.meal-plan-toggle').forEach((toggle) => {
        const targetId = toggle.getAttribute('aria-controls');
        const panel = document.getElementById(targetId);
        const icon = toggle.querySelector('.meal-plan-toggle-icon');

        if (!panel || !icon) {
            return;
        }

        toggle.addEventListener('click', () => {
            const isExpanded = toggle.getAttribute('aria-expanded') === 'true';

            toggle.setAttribute('aria-expanded', String(!isExpanded));
            panel.style.gridTemplateRows = isExpanded ? '0fr' : '1fr';
            icon.classList.toggle('rotate-180', !isExpanded);
        });
    });
});
