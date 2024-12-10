//import './bootstrap.js';

/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */

import './styles/app.css';
import '/node_modules/bootstrap/dist/css/bootstrap.min.css'
import '/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js'
import '/node_modules/bootstrap-icons/font/bootstrap-icons.css'
import '/node_modules/tom-select/dist/css/tom-select.css'
import './css/dashboard.css'
import '/node_modules/flatpickr/dist/flatpickr.css'
//flatpickr
import  '/node_modules/flatpickr/dist/l10n/fr.js'

import TomSelect from 'tom-select'
document.addEventListener('DOMContentLoaded', () => {
    const selectFields = document.querySelector('.js-tom-select');
    new TomSelect(selectFields, {
        plugins: {
            remove_button: {
                title: 'Supprimer cet élément'
            },
        }
    })

});

