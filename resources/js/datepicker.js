import 'flowbite';

const czechLocale = {
    days: ['Neděle', 'Pondělí', 'Úterý', 'Středa', 'Čtvrtek', 'Pátek', 'Sobota'],
    daysShort: ['Ned', 'Pon', 'Úte', 'Stř', 'Čtv', 'Pát', 'Sob'],
    daysMin: ['Ne', 'Po', 'Út', 'St', 'Čt', 'Pá', 'So'],
    months: ['Leden', 'Únor', 'Březen', 'Duben', 'Květen', 'Červen', 'Červenec', 'Srpen', 'Září', 'Říjen', 'Listopad', 'Prosinec'],
    monthsShort: ['Led', 'Úno', 'Bře', 'Dub', 'Kvě', 'Čer', 'Čnc', 'Srp', 'Zář', 'Říj', 'Lis', 'Pro'],
    today: 'Dnes',
    clear: 'Vymazat',
    monthsTitle: 'Měsíc',
    weekStart: 1,
    format: 'dd.mm.yyyy'
};

Alpine.data('datepickerComponent', (format, minDate) => ({
    init() {
        const datepicker = new Datepicker(this.$refs.picker, {
            autohide: true,
            buttons: true,
            autoSelectToday: 1,
            format: format,
            minDate: minDate
        });

        if (document.documentElement.lang === 'cs') {
            const nativeDatepicker = datepicker.getDatepickerInstance();

            nativeDatepicker.constructor.locales.cs = czechLocale;
            nativeDatepicker.setOptions({ language: 'cs' });
        }
    }
}));
