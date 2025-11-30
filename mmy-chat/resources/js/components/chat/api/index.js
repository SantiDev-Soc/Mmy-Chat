
import { apiMensajes } from './apis/mensajes';
import { apiContactos } from './apis/contactos';

export const peticionesApi = {
    ...apiMensajes,
    ...apiContactos
};
