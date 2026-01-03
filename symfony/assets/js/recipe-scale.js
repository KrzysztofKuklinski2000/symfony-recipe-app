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

        ingredientElements.forEach(igredientElement => {
            const originalAmount = parseFloat(igredientElement.dataset.originalAmount);
            const unit = (igredientElement.dataset.unit || '').trim().toLowerCase().replace('.','');

            const unitDisplayElement = igredientElement.parentElement.querySelector('.js-unit');

            if (!isNaN(originalAmount)) {
                let newAmount = originalAmount * ratio;
                let newUnit = unit;

                if (['g', 'gram', 'gramy', 'gr'].includes(unit)) {
                    if (newAmount >= 1000) {
                        newAmount = newAmount / 1000;
                        newUnit = 'kg';
                    }
                } else if (['kg', 'kilogram'].includes(unit)) {
                    if (newAmount < 1) {
                        newAmount = newAmount * 1000;
                        newUnit = 'g';
                    }
                }
                else if (['ml', 'mililitr'].includes(unit)) {
                    if (newAmount >= 1000) {
                        newAmount = newAmount / 1000;
                        newUnit = 'l';
                    }
                } else if (['l', 'litr'].includes(unit)) {
                    if (newAmount < 1) {
                        newAmount = newAmount * 1000;
                        newUnit = 'ml';
                    }
                }

                if (['szt', 'sztuka', 'opakowanie', 'puszka'].includes(unit)) {
                    newAmount = Math.round(newAmount * 2) / 2;
                } else if (newUnit === 'szczypta') {
                    newAmount = Math.round(newAmount);
                    if (newAmount < 1) newAmount = 1;
                } else {
                    if (['kg', 'l'].includes(newUnit)) {
                        newAmount = parseFloat(newAmount.toFixed(2));
                    } else {
                        newAmount = parseFloat(newAmount.toFixed(0));
                    }
                }

                igredientElement.innerText = newAmount;

                if (unitDisplayElement) {
                    if (newUnit !== unit || ['kg', 'g', 'l', 'ml'].includes(newUnit)) {
                        unitDisplayElement.innerText = newUnit;
                    }
                }
            }
        })

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
})
