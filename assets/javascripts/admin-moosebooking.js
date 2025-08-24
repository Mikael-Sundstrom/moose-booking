console.log('Admin Moose Booking JS Loaded');
document.addEventListener('DOMContentLoaded', function() {
    // Hitta menyn
    const parentMenu = document.querySelector('#toplevel_page_moosebooking');

    if (!parentMenu) return;

    // Leta efter alla li under submenu
    const submenuItems = parentMenu.querySelectorAll('ul.wp-submenu li');

    submenuItems.forEach(function(li) {
        const link = li.querySelector('a');
        if (link && link.href.includes('page=moosebooking-template-editor')) {
            li.remove();
        }
    });
});
