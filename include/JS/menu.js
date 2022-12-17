let menu = document.getElementById('menu-bar');
let divStatus = document.getElementById('name-status');
let profileDiv = document.getElementById('profile');


//CLOSE MENU WHEN CLICK OUTSIDE THE ELEMENT.
document.addEventListener('click', function(event) {
    if (menu.className === 'menu-slideIn' || menu.className === 'menu-open') {
        let isClickInsideElement = menu.contains(event.target);
        if (!isClickInsideElement) {
            console.log('outside');
            //PHONE SCREEN.
            if (window.matchMedia("(max-width: 480px)").matches) {
                if (menu.className = 'menu-slideIn') {
                    menu.classList.remove('menu-slideIn');
                    menu.classList.add('menu-slideOut');
                    menu.style.left = -999;
                    console.log('480px');
                }
            //TABLET SCREEN.    
            } else if ((window.matchMedia("(min-width: 481px)").matches)) {
                menu.classList.remove('menu-open');
                menu.classList.add('menu-close');
                menu.classList.remove('menu.text-display-none');
                menu.style = '';
                console.log('>481px')
            }
        }
    }
});
// UPDATE THE MENU FIELDS WHEN THE DEVICE ORIENTATION CHANGES
window.addEventListener("orientationchange", event => {
    menu.className = '';
    menu.style = '';
});

function slideIn() {
    // PHONE SCREEN - VERTICAL
    if (window.matchMedia("(max-width: 480px)").matches) {
        console.log('480');
        // CLOSE THE MENU - if open class is on, remove class add close class.
        if (menu.classList.contains('menu-slideIn')) {
            menu.classList.remove('menu-slideIn');
            menu.classList.add('menu-slideOut');
            menu.style.left = -999;
            divStatus.classList.remove('name-status-open');
            divStatus.classList.add('name-status-close');
        // OPEN THE MENU - if close class is on, remove class and add open class.
        } else {
            menu.classList.remove('menu-slideOut');
            menu.classList.add('menu-slideIn');
            menu.style.left = 0;
            divStatus.classList.remove('name-status-close');
            divStatus.classList.add('name-status-open');
        }
    // TABLET AND HORIZONTAL PHONE SCREEN.
    } else if ((window.matchMedia("(min-width: 481px)").matches)) {
        //Remove any possible style from vertical mobile device.
        menu.classList.remove('menu-slideIn');
        menu.classList.remove('menu-slideOut');
        menu.style.left = 0;
        //CLOSE THE MENU - If it's open, remove the open class and add the close class.
        if (menu.classList.contains('menu-open')) {
            menu.classList.remove('menu-open');
            menu.classList.add('menu-close');
            menu.style.width = '';
            divStatus.classList.remove('name-status-open');
            divStatus.classList.add('name-status-close');
            profileDiv.style.height = '100px';
            /*changeAllMenuText('none');*/
        } else {
        //OPEN THE MENU - If it's close, remove the close class and add the open class.
            menu.classList.remove('menu-close');
            menu.classList.add('menu-open');
            menu.style.width = 250;
            divStatus.classList.remove('name-status-close');
            divStatus.classList.add('name-status-open');
            profileDiv.style.height = '220px';
        }
    }
}

function resize() {
    menu.className = '';
    menu.style = '';
    profileDiv.style.height = '';
}