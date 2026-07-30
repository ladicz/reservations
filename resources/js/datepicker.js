import 'flowbite';
import { cs } from 'date-fns/locale';

Alpine.data('datepickerComponent', (format, minDate) => ({
    init() {
        new Datepicker(this.$refs.picker, {
            autohide: true,
            buttons: true,
            autoSelectToday: 1,
            format: format,
            minDate: minDate,
            locale: cs
        });
    }
}));
