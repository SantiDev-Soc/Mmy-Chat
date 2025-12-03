
export const utilidades = {

    contarNoLeidos(chat) {
        if (!chat) return 0;

        const idParticipante = this.normalizarId(chat.participant_id);
        const idChat = this.normalizarId(chat.id);

        if (idParticipante && this.contadores[idParticipante]) {
            return this.contadores[idParticipante];
        }

        if (idChat && this.contadores[idChat]) {
            return this.contadores[idChat];
        }

        return 0;
    },

    irAlUltimoMensaje() {
        this.$nextTick(() => {
            const contenedor = document.getElementById('messages-container');
            if (contenedor) {
                contenedor.scrollTop = contenedor.scrollHeight;
            }
        });
    },

    normalizarId(valor) {
        if (!valor) return null;
        return String(valor).toLowerCase();
    },

    formatoHora(fechaISO) {
        if (!fechaISO) return '';
        try {
            return new Date(fechaISO).toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch (e) {
            return '';
        }
    }
};
