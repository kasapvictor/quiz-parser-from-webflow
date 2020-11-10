export default (() => {
	const wrapQuiz = document.querySelector('.wrap-quiz');
	const quizForm = wrapQuiz.querySelector('form');
	const quizSteps = quizForm.querySelectorAll('.quiz-step');
	let distance = wrapQuiz.offsetWidth;
	let step = 0;

	/*
	 * стили для враппера
	 */
	wrapQuiz.style.overflow = 'hidden';

	/*
	 * стили для формы квиза
	 */
	quizForm.style.padding = '0px';
	quizForm.style.display = 'flex';
	quizForm.style.transition = 'all .5s ease-in-out';

	/*
	 * стили для карточек
	 */
	quizSteps.forEach( el => {
		el.style.borderRadius = '0px';
		el.style.minWidth = `100%`;
		el.style.margin = '0px';
	});

	/*
	 * проверка выбран ли вариант ответа в карточке
	 */
	quizForm.addEventListener( 'click', checkAnswer );

	/* functions */

	/*
	 * проверяет наличие выбранного ответа в карточке
	 */
	function checkAnswer (e) {
		const el = e.target;

		// первая карточка квиза
		if (el.dataset.quizSubmit === 'start') {
			step++;
			quizForm.style.transform = `translate(-${step * distance}px, 0px)`;
		}

		// селдующий шаг
		if (el.dataset.quizSubmit === 'next') {
			const quizCard = el.closest('[data-quiz-cart]');
			const questions = quizCard.querySelectorAll('input:not([type="file"])');
			const answers = Array.prototype.slice.call(questions);
			let checked = answers.some(answer => answer.checked);
			const info = quizCard.querySelector('.step-info');
			const files = quizCard.querySelector('[type="file"]');

			if (questions.length > 0 || files) {
				// если есть input type="file" и есть required
				// то проверить длинну массива файлов
				if (files !== null && files.files.length > 0 && files.required) checked = true;

				// если нету required то пункт не обязательный
				if (files !== null && !files.required) checked = true;

				// если карточка содержит варианты ответов
				// то хотя бы один инпут должен быть выбран
				if (checked) {
					step++;
					quizForm.style.transform = `translate(-${step * distance}px, 0px)`;
				} else {
					info.innerHTML = 'Нельзя пропустить';
					setTimeout(() => {
						info.innerHTML = '';
					}, 2000);
				}
			}
			// если карточка не содержит инпутов то проверка пропускается
			else {
				step++;
				quizForm.style.transform = `translate(-${step * distance}px, 0px)`;
			}
		}

		// пркдыдущий шаг
		if (el.dataset.quizSubmit === 'prev') {
			step--;
			quizForm.style.transform = `translate(-${step * distance}px, 0px)`;
		}
	}
})()
