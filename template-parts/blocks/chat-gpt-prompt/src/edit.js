/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

import VariableSelector from './components/variable-selector';
import PromptTextarea from './components/prompt-textarea';
import ResponseTextarea from './components/response-textarea';
import { useState, useEffect } from '@wordpress/element';
import variables from './variables.json';

import { getChatGptResponseTexts } from './utils/chatGpt';
import { getKey } from './utils/responseTexts';

const initialSelectValues = variables.reduce((acc, variable) => {
	acc[variable.key] = variable.options[0].value;
	return acc;
}, {});

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit({ attributes }) {
	const [promptText, setPromptText] = useState(attributes.promptText);
	const [responseTexts, setResponseTexts] = useState(attributes.responseTexts ?? {});
	const [selectValues, setSelectValues] = useState(initialSelectValues);
	useEffect(() => {
		attributes.promptText = promptText;
		attributes.responseTexts = responseTexts;
	}, [promptText, responseTexts]);

	function sendChatGptRequests(currentPromptText) {
		getChatGptResponseTexts(currentPromptText).then((responses) => {
			setResponseTexts(
				responses.reduce((acc, response) => {
					acc[getKey(response.combination)] = response.response;
					return acc;
				}, {}),
			);
		});
	}

	return (
		<div {...useBlockProps()}>
			<PromptTextarea
				promptText={promptText}
				setPromptText={setPromptText}
				sendChatGptRequests={sendChatGptRequests}
			/>
			<VariableSelector selectValues={selectValues} setSelectValues={setSelectValues} />
			<ResponseTextarea
				selectValues={selectValues}
				responseTexts={responseTexts}
				setResponseTexts={setResponseTexts}
			/>
		</div>
	);
}
