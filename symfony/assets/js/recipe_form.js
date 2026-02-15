document.addEventListener('turbo:load', () => {
    const addIgredientButton = document.getElementById('add-ingredient');
    const ingredientsList = document.getElementById('ingredients-list');

    if(!addIgredientButton) return;

    let ingredientCounter = ingredientsList.dataset.index;
    const ingredientPrototype = ingredientsList.dataset.prototype;
    const deleteIcon = ingredientsList.dataset.deleteIcon;

    const addNewIngredientForm = () => {
        let newForm = ingredientPrototype.replace(/__name__/g, ingredientCounter);

        let newLi =  document.createElement('li');
        newLi.className = 'shadow p-2 bg-gray-50 rounded';
        newLi.innerHTML = newForm;

        addDeleteButton(newLi);
        ingredientsList.appendChild(newLi);
        ingredientCounter++;
    }

    const addDeleteButton = (item) => {
        let deleteButton = document.createElement('button');
        deleteButton.type = 'button';
        deleteButton.className = 'mt-2 p-2 bg-red-500 text-white rounded hover:bg-red-600 hover:cursor-pointer';
        deleteButton.innerHTML = deleteIcon;

        deleteButton.addEventListener('click', () => {
            item.remove();
        })

        item.appendChild(deleteButton);
    }

    if(addIgredientButton) {
        addIgredientButton.addEventListener('click', addNewIngredientForm)
    }

    document.querySelectorAll('ul#ingredients-list li').forEach(item => {
        addDeleteButton(item);
    })
})
