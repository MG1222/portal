export const flesh = () => {
    document.querySelectorAll('.bnt-close').forEach(function(button) {
        button.addEventListener('click', function(event) {
            event.target.parentElement.classList.add('flash-message-out');
            setTimeout(function() {
                event.target.parentElement.remove();
            }, 500); // temps de l'animation en millisecondes
        });
    });

}
