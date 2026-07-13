import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

const indicador = () => document.getElementById('cargando-navegacion');
const mostrarCarga = () => indicador()?.classList.remove('hidden');
const ocultarCarga = () => indicador()?.classList.add('hidden');

document.addEventListener('click', (evento) => {
    const enlace = evento.target.closest('a[href]');

    if (! enlace || evento.defaultPrevented || evento.button !== 0 || evento.ctrlKey || evento.metaKey || evento.shiftKey) return;
    if (enlace.target === '_blank' || enlace.hasAttribute('download')) return;

    const destino = new URL(enlace.href, window.location.href);
    if (destino.origin === window.location.origin && destino.href !== window.location.href && ! destino.hash) {
        mostrarCarga();
    }
});

document.addEventListener('submit', (evento) => {
    mostrarCarga();

    if (evento.submitter) {
        evento.submitter.disabled = true;
        evento.submitter.classList.add('opacity-60');
    }
});
window.addEventListener('pageshow', ocultarCarga);
