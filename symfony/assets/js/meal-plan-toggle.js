document.addEventListener('click', (event) => {
    const toggle = event.target.closest('.meal-plan-toggle');

    if (!toggle) {
        return;
    }

    const targetId = toggle.getAttribute('aria-controls');
    const panel = document.getElementById(targetId);
    const icon = toggle.querySelector('.meal-plan-toggle-icon');

    if (!panel || !icon) {
        return;
    }

    const isExpanded = toggle.getAttribute('aria-expanded') === 'true';

    toggle.setAttribute('aria-expanded', String(!isExpanded));
    panel.style.gridTemplateRows = isExpanded ? '0fr' : '1fr';
    icon.classList.toggle('rotate-180', !isExpanded);
});
