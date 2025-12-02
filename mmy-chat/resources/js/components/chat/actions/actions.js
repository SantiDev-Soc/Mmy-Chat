export const logicaInterfaz = {

    async seleccionarChat(contacto) {

        this.busqueda = ''; // limpiar el nombre del inpt al seleccionar el contacto
        this.contactoSeleccionado = { id: contacto.id, name: contacto.name };
        this.chatAbierto = false;
        this.mensajes = [];

        const idNormalizado = String(contacto.id).toLowerCase();
        this.contadores = { ...this.contadores, [idNormalizado]: 0 };

        const indice = this.conversaciones.findIndex(c =>
            String(c.participant_id || c.id).toLowerCase() === idNormalizado
        );

        if (indice === -1) {
            this.conversaciones.unshift({
                id: contacto.id,
                participant_id: contacto.id,
                name: contacto.name,
                created_at: new Date().toISOString()
            });
        } else {
            const chatMover = this.conversaciones.splice(indice, 1)[0];
            this.conversaciones.unshift(chatMover);
        }

        if (!this.userId) {return;}

        this.mensajes = await this.obtenerMensajes(contacto.id);
        this.scrollAlFondo();
        this.procesarNoLeidos();
        this.conectarChatEspecifico(contacto.id);
    },

    scrollAlFondo() {
        this.$nextTick(() => {
            const contenedor = document.getElementById('messages-container');
            if (contenedor) {
                contenedor.scrollTop = contenedor.scrollHeight;
            }
        });
    },

    procesarNoLeidos() {
        const idMio = String(this.userId).toLowerCase();

        // Filtrar los mensajes que NO son míos y NO tienen fecha de lectura
        const idsPendientes = this.mensajes
            .filter(m => String(m.sender_id).toLowerCase() !== idMio && !m.read_at)
            .map(m => m.id);

        if (idsPendientes.length > 0) {
            idsPendientes.forEach(id => this.notificarLectura(id));
        }
    },

    contarNoLeidos(chat) {
        if (!chat) return 0;

        const id1 = chat.participant_id ? String(chat.participant_id).toLowerCase() : null;
        const id2 = chat.id ? String(chat.id).toLowerCase() : null;

        if (id1 && this.contadores[id1]) return this.contadores[id1];
        if (id2 && this.contadores[id2]) return this.contadores[id2];

        return 0;
    },

    siguienteResultado() {
        if (this.resultadosBusqueda.length > 0 && this.indiceActivo < this.resultadosBusqueda.length - 1) {
            this.indiceActivo++;
        }
    },

    anteriorResultado() {
        if (this.indiceActivo  > 0) {
            this.indiceActivo --;
        }
    },

    elegirResultado() {
        if (this.indiceActivo  >= 0 && this.resultadosBusqueda[this.indiceActivo ]) {
            this.seleccionarChat(this.resultadosBusqueda[this.indiceActivo ]);
        }
    }
};
