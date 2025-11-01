import './bootstrap';
import contactSearch from './contactSearch'

window.Alpine = Alpine

Alpine.data('contactSearch', () => contactSearch(routeUrl)) // puedes pasar la URL desde Blade

Alpine.start()
