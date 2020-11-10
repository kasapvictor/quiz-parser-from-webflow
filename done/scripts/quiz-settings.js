export default {
	limitCount : 6, // маскимальное кол-во файлов
	limitSize : 10500000, // маскимальный общий размер файлов 10500000 -> 10mb, 5400000 -> 5mb
	types: ['jpeg','jpg','png','gif','pdf','svg+xml','tiff','ico','bmp','zip'], // допустимые форматы файлов например ['jpg', 'png', 'zip', 'pdf']
	filePlaceHolder: `<svg id="Capa_1" enable-background="new 0 0 512 512" height="100%" width="100%" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><g><g><path d="m465.817 196.528v274.389c0 22.695-18.389 41.084-41.073 41.084h-337.487c-19.388 0-35.634-13.434-39.951-31.503l-.165-23.488-.958-139.878v-276.048c0-22.695 18.389-41.084 41.074-41.084h182.033c16.359 0 69.341 45.173 116.173 92.747 42.732 43.422 80.354 88.843 80.354 103.781z" fill="#ddeafb"/><path d="m465.817 196.528v100.515c0-.113-.01-.227-.01-.35-3.091-123.333-103.554-129.04-136.406-161.893l56.062-42.052c42.732 43.421 80.354 88.842 80.354 103.78z" fill="#cbe2ff"/><path d="m269.284 0h-100.515c.113 0 .227.01.35.01 123.333 3.091 129.04 103.554 161.893 136.406l42.052-56.062c-43.422-42.732-88.842-80.354-103.78-80.354z" fill="#cbe2ff"/><path d="m465.816 196.533v17.377c0-36.585-29.667-66.253-66.253-66.253h-40.324c-22.684 0-41.081-18.397-41.081-41.081v-40.323c.001-36.586-29.667-66.253-66.252-66.253h17.377c31.29 0 61.308 12.433 83.433 34.557l78.543 78.543c22.124 22.125 34.557 52.143 34.557 83.433z" fill="#bed8fb"/></g><g fill="#617881"><path d="m396.456 240.032h-280.918c-4.267 0-7.726-3.459-7.726-7.726s3.459-7.726 7.726-7.726h280.918c4.267 0 7.726 3.459 7.726 7.726.001 4.267-3.458 7.726-7.726 7.726z"/><path d="m396.456 281.131h-280.918c-4.267 0-7.726-3.459-7.726-7.726s3.459-7.726 7.726-7.726h280.918c4.267 0 7.726 3.459 7.726 7.726s-3.458 7.726-7.726 7.726z"/><path d="m396.456 322.23h-280.918c-4.267 0-7.726-3.459-7.726-7.726s3.459-7.726 7.726-7.726h280.918c4.267 0 7.726 3.459 7.726 7.726.001 4.267-3.458 7.726-7.726 7.726z"/><path d="m218.733 363.329h-103.195c-4.267 0-7.726-3.459-7.726-7.726s3.459-7.726 7.726-7.726h103.195c4.267 0 7.726 3.459 7.726 7.726s-3.459 7.726-7.726 7.726z"/><path d="m218.733 126.038h-103.195c-4.267 0-7.726-3.459-7.726-7.726s3.459-7.726 7.726-7.726h103.195c4.267 0 7.726 3.459 7.726 7.726s-3.459 7.726-7.726 7.726z"/></g></g></svg>`,
}