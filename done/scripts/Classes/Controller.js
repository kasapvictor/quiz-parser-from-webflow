import settings from '../quiz-settings.js';
import View from './View.js';

export default class Controller {
	constructor (form, settings) {
		this.form = form;
		this.attachments = form.querySelector(".file-wrapper input");
		this.countFiles = 1;

		/*
		* если есть input type=file
		* то слушать все изменения
		*/
		if (form.querySelector('.file-wrapper') !== null) {
			this.fileWrapperClone = form.querySelector('.file-wrapper').cloneNode(true);
			this.attachments.addEventListener('change', this.changeInputFile.bind(this));
		}

		form.addEventListener('submit',  this.submit.bind(this));

		// вадидация полей required
		this.requiredFields();
	}

	/*
	* Изменение input type="file"
	*/
	changeInputFile (e) {
		const el = e.target;
		const filesWrapper = el.closest('.quiz-files-wrapper'); // родительский узел
		const quizCart = filesWrapper.closest('.quiz-step');
		const fileWrapper = el.closest('.file-wrapper');
		const fileWrapperClone = this.fileWrapperClone.cloneNode(true);
		const fileWrapperInput = fileWrapperClone.querySelector('input');
		const deleteFile = fileWrapper.querySelector('.delete-file');

		// если размер больше указанного лимита в settings.limitSize
		// то вернет тру
		// если не тру то добавит файл
		if (el.files[0].size > settings.limitSize) {
			const limit = Math.floor((settings.limitSize / 1024 / 1024).toFixed(2));

			View.showInfo (
				quizCart.querySelector('.step-info'),
				`Максимальный объем всех файлов не должен превышать ${limit}Mb`
			);
			return false;
		}

		// если типа файла не соответствует из перечисленных в settings.types
		// то вернет false
		if (!this.checkTypeFile(el)) {
			const types = settings.types.join(', ');

			View.showInfo (
				quizCart.querySelector('.step-info'),
				`Допустимые типы файлоф для загрузки ${types}`
			);
			return false;
		}


		// удаялем атрибут required у всех склонированных элементов input
		fileWrapperInput.required = "";

		// вешаем клик на кнопку удаления файла
		deleteFile.addEventListener('click', this.deleteFile.bind(this));

		// всталвяем выбранное изображение во враппер
		View.previewFile(e.target);
		fileWrapper.classList.add('has-file');

		// вставляем склонированную обертку инпута
		// если после выбранного ипута пусто
		// и общее количество файлов не превышет лимит - settings.limitCount
		// то вставлем конец родительского узла новую обертку
		if (fileWrapper.nextElementSibling === null && this.countFiles < settings.limitCount) {
				filesWrapper.append(fileWrapperClone);
		}

		// вешаем слушателя на новый инпут
		fileWrapperInput.addEventListener('change', this.changeInputFile.bind(this));
		this.countFiles++;

	}

	/*
	*	 Удаление файлов
	*/
	deleteFile (e) {
		const el = e.target;
		const required = el.closest('[data-required]');
		const filesWrapper = el.closest('.quiz-files-wrapper'); // родительский узел
		const fileWrapper = el.closest('.file-wrapper');
		const fileWrapperClone = this.fileWrapperClone.cloneNode(true);
		const fileWrapperInput = fileWrapperClone.querySelector('input');

		// если число файлов больше 1 то отнять от this.countFiles 1
		this.countFiles = this.countFiles === 1 ? 1 : this.countFiles - 1;

		// удалить блок с вложением
		fileWrapper.remove();

		// если кол-во файлов - "this.countFiles" === 1
		// и у обертки блока есть атрибут data-required
		// то добавить последнему инпуту атрибут required
		if (this.countFiles === 1 && required !== null) {
			const lastInput = required.querySelector('input');

			lastInput.setAttribute('required', '');
		}

		// если текущее количество файлов равно лимиту
		// то при удалении добавить склонированный инпут файл в конец
		if (this.countFiles === settings.limitCount) {
			filesWrapper.append(fileWrapperClone);
			fileWrapperInput.addEventListener('change', this.changeInputFile.bind(this));
		}
	}

	/*
	*	 Проверка типа файла
	*/
	checkTypeFile (el) {
		const currentType = el.files[0].type.split('/')[1];

		return settings.types.includes(currentType);
	}

	/*
	*	 Валидация полей с атрибутом required
	*/
	requiredFields () {
		const required = this.form.querySelectorAll('[required]:not([type="file"])');

		required.forEach(field => {
			console.log(field.type);
		});
	}


	/*
	 * проверка формы перед отправкой
	 */
	submit (e) {
		e.preventDefault();
		this.send(new FormData(this.form));
	}

	/*
	 * отправляет данные формы в файл mail.php
	 * ответом приходит статус отправки
	 */
	async send (formData) {
		const form = this;
		const response = await fetch('mailer/quiz-mail.php', {
			method: 'POST',
			body: formData
		});

		const result = await response.text();

		console.log(result);
	}

}