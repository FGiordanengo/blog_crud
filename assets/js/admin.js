
import '../scss/admin.scss';

import $ from 'jquery';
import 'popper.js'
import 'bootstrap'
import toggleStatus from "./modules/toggle"

global.$ = global.jQuery = $

document.querySelectorAll('.js-switch').forEach(link => {
    link.addEventListener('click', toggleStatus)
})
