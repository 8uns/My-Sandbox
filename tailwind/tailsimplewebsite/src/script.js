// absolute top-14 left-0 bg-slate-800 divide-gray-900 divide-y-2 w-full 
// absolute top-14 w-full left-0 bg-slate-800 divide-gray-900 divide-y-2
// <i class="fa-solid fa-xmark"></i>

const menu = document.querySelector('.menu')
const hamburgermenu = document.querySelector('.hamburger-menu')
const hamburgermenuicon = document.querySelector('.hamburger-menu-icon')

hamburgermenu.addEventListener('click', displayMenu)
hamburgermenuicon.addEventListener('click', changeIcon)

function changeIcon() {
    if (hamburgermenuicon.classList.contains('fa-bars')) {
        hamburgermenuicon.classList.add('fa-xmark')
        hamburgermenuicon.classList.remove('fa-bars')
    } else {
        hamburgermenuicon.classList.remove('fa-xmark')
        hamburgermenuicon.classList.add('fa-bars')
    }
}
function displayMenu() {
    if (menu.classList.contains('absolute')) {
        menu.classList.add('hidden')

        menu.classList.remove('absolute')
        menu.classList.remove('top-14')
        menu.classList.remove('w-full')
        menu.classList.remove('left-0')
        menu.classList.remove('bg-slate-800')
        menu.classList.remove('divide-gray-900')
        menu.classList.remove('divide-y-2')

    } else {
        menu.classList.remove('hidden')

        menu.classList.add('absolute')
        menu.classList.add('top-14')
        menu.classList.add('w-full')
        menu.classList.add('left-0')
        menu.classList.add('bg-slate-800')
        menu.classList.add('divide-gray-900')
        menu.classList.add('divide-y-2')
    }
}
