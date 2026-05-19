// Reusable Modal Component
// Handles opening, closing, and click-outside-to-close functionality

const Modal = {
    open(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('active');
        }
    },

    close(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('active');
        }
    },

    toggle(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.toggle('active');
        }
    },

    // Setup click-outside-to-close for all modals
    initClickOutside() {
        window.addEventListener('click', (event) => {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('active');
            }
        });
    }
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    Modal.initClickOutside();
});
