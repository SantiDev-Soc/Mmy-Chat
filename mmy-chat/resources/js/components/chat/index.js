
import { estadoInicial  } from './state/state.js';
import { peticionesApi  } from './api/index.js';
import { manejadorEventos  } from './websockets/index.js';
import { logicaInterfaz  } from './actions/actions.js';
import { utilidades  } from './helpers/helpers.js';

export default function (UserIdloged) {
    return {
        ...estadoInicial(UserIdloged),
        ...peticionesApi,
        ...manejadorEventos,
        ...logicaInterfaz,
        ...utilidades,

        init() {
            console.log('Chat Componente Onload');
            if (this.userId) {
                this.cargarMisConversaciones(this.userId);
                this.iniciarEscuchaGlobal();
            }
        }
    };
}
