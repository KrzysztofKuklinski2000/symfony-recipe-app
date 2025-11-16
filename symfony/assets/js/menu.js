
document.addEventListener('DOMContentLoaded', () => {
    const menuButton = document.querySelector('#mobile-menu-button')

    if(!menuButton) return;
    menuButton.addEventListener('click', () => {
        const mobileMenu = document.querySelector('#mobile-menu')
        mobileMenu.classList.toggle('hidden')
    })
})
