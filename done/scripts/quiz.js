// import './quiz-mode-1.js'; // поведение формы и валидация ответов в карточка
import Controller from './Classes/Controller.js';

const forms = document.querySelectorAll('[data-wrap-quiz] form');

forms.forEach(form => new Controller(form));
