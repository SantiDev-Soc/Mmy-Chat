export const apiContactos = {

    async buscarContactos() {
        if (this.busqueda.length < 1) {
            this.resultadosBusqueda = [];
            this.chatAbierto = false;
            return;
        }

        try {
            const respuesta = await fetch(`/contacts/search?q=${this.busqueda}`);
            this.resultadosBusqueda = await respuesta.json();
            this.chatAbierto = this.resultadosBusqueda.length > 0;
            this.indiceActivo = -1;

        } catch (error) {
            console.error("Error buscando contactos:", error);
        }
    },

    async cargarMisConversaciones(idUsuario) {

        try {
            const resChat = await fetch(`${this.api}/api/conversations/${idUsuario}`, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            const datosChat = await resChat.json();

            const listaDTOs = datosChat.success ? datosChat.message : [];

            if (listaDTOs.length === 0) {
                this.conversaciones = [];
                return;
            }

            listaDTOs.forEach(dto => {
                if (dto.contact_id) {
                    const idNormalizado = String(dto.contact_id).toLowerCase();
                    this.contadores[idNormalizado] = dto.unread_count;
                }
            });

            const idsParaPedir = listaDTOs.map(dto => dto.contact_id);
            const idsParam = idsParaPedir.join(',');

            const resUsuarios = await fetch(`/users/batch?ids=${idsParam}`);
            const datosUsuarios = await resUsuarios.json();

            this.conversaciones = listaDTOs.map(dto => {
                const idContacto = dto.contact_id;

                const usuario = datosUsuarios.find(u =>
                    String(u.id).toLowerCase() === String(idContacto).toLowerCase()
                );

                return {
                    id: idContacto,
                    participant_id: idContacto,
                    name: usuario ? usuario.name : 'Usuario Desconocido',
                    created_at: new Date().toISOString()
                };
            });

        } catch (e) {
            console.error("Error cargando conversaciones:", e);
        }
    },

    async obtenerNombreContacto(idUsuario) {
        try {
            const resp = await fetch(`/user/${idUsuario}`);
            const usuario = await resp.json();

            const chat = this.conversaciones.find(c =>
                String(c.id || c.participant_id).toLowerCase() === String(idUsuario).toLowerCase()
            );
            if (chat && usuario.name) chat.name = usuario.name;

        } catch (e) {
            console.warn("Error nombre contacto:", e);
        }
    }
};
