// js/main.js
import { initializeEventListeners } from './events.js';
import { initializeElements } from './dom.js';

document.addEventListener('DOMContentLoaded', () => {
    initializeElements();
    initializeEventListeners();
});