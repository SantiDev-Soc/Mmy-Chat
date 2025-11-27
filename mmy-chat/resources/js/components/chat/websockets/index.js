
import { iniciadoresSockets } from './events/starters.js';
import { logicaGlobal } from './events/handlers.js';
import { logicaDeChat } from './events/chatHandler.js';

export const manejadorEventos = {
    ...iniciadoresSockets,
    ...logicaGlobal,
    ...logicaDeChat
};
