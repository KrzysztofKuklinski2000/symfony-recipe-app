document.addEventListener('turbo:load', () => {
    const categoryButton = document.querySelector('#category-button')
    const categoryList = document.querySelector('#category-list')

    if(!categoryButton || !categoryList) return;

    categoryButton.addEventListener('click', (e) => {
         e.stopPropagation();
        categoryList.classList.toggle('hidden')
    })

    document.addEventListener('click', (e) => {
        if(!categoryButton.contains(e.target) && !categoryList.contains(e.target)) {
            categoryList.classList.add('hidden');
        }
    });
})
