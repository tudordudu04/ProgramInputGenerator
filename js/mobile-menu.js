document.addEventListener('DOMContentLoaded', function() { 
    const header = document.querySelector('.site-header .margin');
    const nav = document.querySelector('.site-header nav');
    
    if (header && nav) { 
        const mobileToggle = document.createElement('button');
        mobileToggle.className = 'mobile-menu-toggle';
        mobileToggle.innerHTML = '☰';
        mobileToggle.setAttribute('aria-label', 'Toggle mobile menu');
         
        const logo = header.querySelector('.logo');
        if (logo && logo.parentNode) {
            logo.parentNode.insertBefore(mobileToggle, nav);
        }
         
        mobileToggle.addEventListener('click', function() {
            nav.classList.toggle('active');
            mobileToggle.innerHTML = nav.classList.contains('active') ? '✕' : '☰';
        });
         
        document.addEventListener('click', function(e) {
            if (!header.contains(e.target) && nav.classList.contains('active')) {
                nav.classList.remove('active');
                mobileToggle.innerHTML = '☰';
            }
        });
         
        const navLinks = nav.querySelectorAll('a');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                nav.classList.remove('active');
                mobileToggle.innerHTML = '☰';
            });
        });
    }
});