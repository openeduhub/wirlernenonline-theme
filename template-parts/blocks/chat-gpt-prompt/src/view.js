import { getKey } from './utils/responseTexts';

window.registerChatGptBlock = (id) => {
	const blockElement = jQuery(`#${id}`);
	const responseTexts = JSON.parse(blockElement.attr('data-response-texts'));
	const responseTextElement = blockElement.find('.response-text');
	const editedByElement = blockElement.find('.edited-by');
	window.pageVariablesSubject.subscribe((pageVariables) => {
		const key = getKey(pageVariables);
		const responseText = responseTexts[key];
		responseTextElement.text(responseText.text);
		if (responseText.editedBy?.length) {
			editedByElement.text(
				`generiert mit ChatGPT und bearbeitet von ${responseText.editedBy.join(', ')}`,
			);
		} else {
			editedByElement.text(`generiert mit ChatGPT`);
		}
	});
};
