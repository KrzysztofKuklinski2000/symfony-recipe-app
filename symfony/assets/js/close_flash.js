
document.addEventListener('turbo:load', () => {
    const closeFlashMessageButton = document.querySelector('#close-flash-message')

    if(!closeFlashMessageButton) return;
    closeFlashMessageButton.addEventListener('click', () => {
        const flashMessage = document.querySelector('#flash-message')
        flashMessage.classList.add('hidden')
    })
})
