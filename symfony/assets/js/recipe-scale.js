document.addEventListener('turbo:load', () => {
    const decreaseBtn = document.getElementById('btn-decrease');
    const increaseBtn = document.getElementById('btn-increase');
    const servingsDisplay = document.getElementById('servings-display');
    const ingredientElements = document.querySelectorAll('.ingredient-amount');

    if (!decreaseBtn || !servingsDisplay) return;

    const baseServings = parseInt(servingsDisplay.dataset.baseServings) || 1;
    let currentServings = baseServings;

    function updateServings() {
        let label = 'porcji';
        if (currentServings === 1) label = 'porcja';
        else if (currentServings > 1 && currentServings < 5) label = 'porcje';

        servingsDisplay.textContent = `(${currentServings} ${label})`;

        const ratio = currentServings / baseServings;

        ingredientElements.forEach(ingredientElement => {
            const originalAmount = parseFloat(ingredientElement.dataset.originalAmount);
            let unit = (ingredientElement.dataset.unit || '').trim().toLowerCase();
            const unitDisplayElement = ingredientElement.parentElement.querySelector('.js-unit');

            if (!isNaN(originalAmount)) {
                let newAmount = originalAmount * ratio;
                let newUnit = unit;

                if (['g', 'ml'].includes(unit)) {
                    if (newAmount >= 1000) {
                        newAmount = newAmount / 1000;
                        newUnit = unit === 'g' ? 'kg' : 'l';
                    }
                }
                else if (['kg', 'l'].includes(unit)) {
                    if (newAmount < 1) {
                        newAmount = newAmount * 1000;
                        newUnit = unit === 'kg' ? 'g' : 'ml';
                    }
                }

                if (['kg', 'l'].includes(newUnit)) {
                    newAmount = parseFloat(newAmount.toFixed(2));
                } else {
                    newAmount = Math.round(newAmount);
                }

                ingredientElement.innerText = newAmount;
                if (unitDisplayElement) {
                    if (newUnit !== unit || ['kg', 'g', 'l', 'ml'].includes(newUnit)) {
                        unitDisplayElement.innerText = newUnit;
                    }
                }
            }
        });

        const hiddenInputs = document.querySelectorAll('.js-servings-input');
        hiddenInputs.forEach(input => {
            input.value = currentServings;
        });
    }

    increaseBtn.addEventListener('click', () => {
        currentServings++;
        updateServings();
    });

    decreaseBtn.addEventListener('click', () => {
        if (currentServings > 1) {
            currentServings--;
            updateServings();
        }
    });
});
