
import { estadoInicial  } from './state/state.js';
import { peticionesApi  } from './api/index.js';
import { manejadorEventos  } from './websockets/index.js';
import { logicaInterfaz  } from './actions/actions.js';
import { utilidades  } from './helpers/helpers.js';

export default function (loggedUserId) {
    return {
        ...estadoInicial(loggedUserId),
        ...peticionesApi,
        ...manejadorEventos,
        ...logicaInterfaz,
        ...utilidades,

        init() {
            console.log('Chat Componente Cargado');
            if (this.userId) {
                this.cargarMisConversaciones(this.userId);
                this.iniciarEscuchaGlobal();
            }
        }
    };
}
