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

import { useEffect, useState } from '@wordpress/element';
import HeadingInput from './components/heading-input';
import PromptTextarea from './components/prompt-textarea';
import ResponseTextarea from './components/response-textarea';
import VariableSelector from './components/variable-selector';
import variables from './data/variables';

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
	const [headingText, setHeadingText] = useState(attributes.headingText ?? 'Chat GPT');
	const [promptText, setPromptText] = useState(attributes.promptText);
	const [responseTexts, setResponseTexts] = useState(attributes.responseTexts ?? {});
	const [selectValues, setSelectValues] = useState(initialSelectValues);
	useEffect(() => {
		attributes.headingText = headingText;
		attributes.promptText = promptText;
		attributes.responseTexts = responseTexts;
	}, [headingText, promptText, responseTexts]);

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
			<HeadingInput headingText={headingText} setHeadingText={setHeadingText} />
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
