document.addEventListener('turbo:load', () => {
    const toggle = document.querySelector('.meal-plan-toggle');
    const panel = document.querySelector('.meal-plan-panel');
    const icon = document.querySelector('.meal-plan-toggle-icon');

    if (!toggle || !panel || !icon) {
        return;
    }

    toggle.addEventListener('click', () => {
        const isExpanded = toggle.getAttribute('aria-expanded') === 'true';

        toggle.setAttribute('aria-expanded', String(!isExpanded));
        panel.style.gridTemplateRows = isExpanded ? '0fr' : '1fr';
        icon.classList.toggle('rotate-180', !isExpanded);
    });
});
