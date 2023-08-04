import { getKey } from './utils/responseTexts';

window.registerChatGptBlock = (id) => {
	const blockElement = jQuery(`#${id}`);
	const responseTexts = JSON.parse(blockElement.attr('data-response-texts'));
	const responseTextElement = blockElement.find('.response-text');
	window.pageVariablesSubject.subscribe((pageVariables) => {
		const key = getKey(pageVariables);
		const responseText = responseTexts[key];
		responseTextElement.text(responseText);
	});
};
