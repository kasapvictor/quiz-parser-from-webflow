import settings from '../quiz-settings.js';

export default class View {
	constructor () {
		
	}

	/*
	* создает превью прикрепленного файла
	*/
	static previewFile (el) {
		const parent = el.closest('.file-wrapper');
		const imageWrapper = parent.querySelector('.wrap-preview-file');
		const file = el.files[0];
		const fileName = file.name.replace(/\s/gm, "_"); // меняем пробелы на "_"

		// сбрасываем содержимое обертки
		imageWrapper.style.backgroundImage = '';
		imageWrapper.innerHTML = '';

		// если тип файла изображение
		// то вставить картинку
		// если нет , то вставить шаблон из settings.filePlaceHolder
		if (!file.type.startsWith('image/')) {
			imageWrapper.innerHTML = settings.filePlaceHolder;
		}  else {
			this.prototype.renderImageBackground(imageWrapper, file);
		}

		// добавляем имя файла
		imageWrapper.insertAdjacentHTML('beforeend', `<span>${fileName}</span>`);
	}

	renderImageBackground (el, file) {
		const reader = new FileReader();
		reader.onload = function (e) {
			el.style.backgroundImage = `url(${e.target.result})`;
		};
		reader.readAsDataURL(file);
	}

	/*
	* выводит информационное сообщение
	*/
	static showInfo (el, msg) {
		el.innerHTML = msg;
		setTimeout( () => el.innerHTML = '', 3000);
	}

}



