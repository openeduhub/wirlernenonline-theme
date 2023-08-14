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
export default function Edit({ attributes, setAttributes, clientId }) {
	/** Text to be displayed as h2 heading on top of the block. */
	const [headingText, setHeadingText] = useState(attributes.headingText || 'Chat GPT');
	/**
	 * Prompt text to be send to Chat GPT.
	 *
	 * Includes placeholders that will used to generate requests with different combinations.
	 */
	const [promptText, setPromptText] = useState(attributes.promptText ?? '');
	/**
	 * Response texts after requesting texts from ChatGPT or when loading the block editor,
	 * whichever happens later.
	 *
	 * Response texts are trimmed after requesting and when saving.
	 */
	const [originalResponseTexts, setOriginalResponseTexts] = useState(
		attributes.responseTexts ?? {},
	);
	/** Response texts including custom edits done in the current editor session. */
	const [responseTexts, setResponseTexts] = useState(originalResponseTexts);
	/** Currently selected placeholder values, used for response review. */
	const [selectValues, setSelectValues] = useState(initialSelectValues);
	/** Whether a request to Chapt GPT is currently in flight. */
	const [isLoading, setIsLoading] = useState(false);

	useEffect(() => {
		setAttributes({ headingText, promptText, responseTexts });
	}, [headingText, promptText, responseTexts]);

	// Use the initial temporary block ID as permanent element ID.
	useEffect(() => {
		if (!attributes.id) {
			setAttributes({ id: clientId });
		}
	});

	function sendChatGptRequests(currentPromptText) {
		setIsLoading(true);
		getChatGptResponseTexts(currentPromptText)
			.then((responses) => {
				const responseTexts = responses.reduce((acc, response) => {
					acc[getKey(response.combination)] = { text: response.response.trim() };
					return acc;
				}, {});
				setOriginalResponseTexts(responseTexts);
				setResponseTexts(responseTexts);
			})
			.finally(() => {
				setIsLoading(false);
			});
	}

	return (
		<div {...useBlockProps()}>
			<HeadingInput headingText={headingText} setHeadingText={setHeadingText} />
			<PromptTextarea
				promptText={promptText}
				setPromptText={setPromptText}
				sendChatGptRequests={sendChatGptRequests}
				isLoading={isLoading}
			/>
			<VariableSelector selectValues={selectValues} setSelectValues={setSelectValues} />
			<ResponseTextarea
				selectValues={selectValues}
				originalResponseTexts={originalResponseTexts}
				responseTexts={responseTexts}
				setResponseTexts={setResponseTexts}
				promptText={promptText}
			/>
		</div>
	);
}
