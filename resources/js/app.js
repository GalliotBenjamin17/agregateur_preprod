import AlpineFloatingUI from '@awcodes/alpine-floating-ui'
import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css'; // optional for styling
import Tooltip from "@ryangjchandler/alpine-tooltip";

Alpine.plugin(AlpineFloatingUI)
Alpine.plugin(Tooltip);

function successNotification(title) {
    new FilamentNotification().title(title).success().send();
}

tippy('.tippy', {
    allowHTML: true,
});

function maskInput() {
    return {
        value: '',
        updateValue() {
            // Remove any characters that aren't digits or decimal points.
            this.value = this.value.replace(/[^0-9.]/g, '');

            // Remove leading zeros.
            if (this.value.length && this.value[0] === '0' && this.value[1] !== '.') {
                this.value = this.value.substr(1);
            }

            // Ensure there's only one decimal point.
            const decimalPoints = this.value.split('.').length - 1;
            if (decimalPoints > 1) {
                const parts = this.value.split('.');
                this.value = parts.shift() + '.' + parts.join('');
            }

            // Handle decimal places.
            const [integerPart, decimalPart] = this.value.split('.');
            if (decimalPart && decimalPart.length > 2) {
                this.value = integerPart + '.' + decimalPart.substr(0, 2);
            }

            // Add thousands separator.
            this.value = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ' ') + (decimalPart ? '.' + decimalPart : '');
        }
    }
}
