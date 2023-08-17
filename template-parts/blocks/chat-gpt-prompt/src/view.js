import { getKey } from './utils/responseTexts';

window.registerChatGptBlock = (id) => {
	function addTextToElement(text, element) {
		const paragraphs = text
			.split('\n')
			.map((p) => p.trim())
			.filter((p) => p !== '');
		for (const paragraph of paragraphs) {
			console.log(paragraph);
			element.append(jQuery('<p></p>').append(paragraph));
		}
	}

	const blockElement = jQuery(`#${id}`);
	const responseTexts = JSON.parse(blockElement.attr('data-response-texts'));
	const responseTextElement = blockElement.find('.response-text');
	const editedByElement = blockElement.find('.edited-by');
	window.pageVariablesSubject.subscribe((pageVariables) => {
		const key = getKey(pageVariables);
		const responseText = responseTexts[key];
		if (responseText) {
			responseTextElement.empty();
			addTextToElement(responseText.text, responseTextElement);
			if (responseText.editedBy?.length) {
				editedByElement.text(
					`generiert mit ChatGPT und bearbeitet von ${responseText.editedBy.join(', ')}`,
				);
			} else {
				editedByElement.text(`generiert mit ChatGPT`);
			}
		} else {
			responseTextElement.html('<p>Hier gibt es noch keinen Text.</p>');
		}
	});
};
