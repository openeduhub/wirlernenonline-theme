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

import { Notice } from '@wordpress/components';
import { useEffect, useState } from '@wordpress/element';
import HeadingInput from './components/heading-input';
import PromptHeading from './components/prompt-heading';
import PromptTextarea from './components/prompt-textarea';
import ResponseHeading from './components/response-heading';
import ResponseTextarea from './components/response-textarea';
import VariableSelector from './components/variable-selector';
import variables from './data/variables';
import collectionService from './services/collection-service';
import placeholderService from './services/placeholder-service';
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
	 * The value of `promptText` with resolved placeholders for collection values.
	 */
	const [promptTextWithCollectionValues, setPromptTextWithCollectionValues] = useState(
		attributes.promptTextWithCollectionValues ?? '',
	);
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
	/** An error that occured when requesting data from Chat GPT. */
	const [error, setError] = useState();
	/** The collection id used for resolving collection placholders. */
	const [collectionId, setCollectionId] = useState(collectionService.getCurrentCollectionId());

	useEffect(() => {
		setAttributes({ headingText, promptText, promptTextWithCollectionValues, responseTexts });
	}, [headingText, promptText, promptTextWithCollectionValues, responseTexts]);

	// Use the initial temporary block ID as permanent element ID.
	useEffect(() => {
		if (!attributes.id) {
			setAttributes({ id: clientId });
		}
	});

	function updateCollectionId() {
		const currentCollectionId = collectionService.getCurrentCollectionId();
		if (currentCollectionId !== collectionId) {
			setCollectionId(currentCollectionId);
		}
	}

	async function sendChatGptRequests(currentPromptText) {
		setError(null);
		setIsLoading(true);
		try {
			const promptTextWithCollectionValues_ =
				await placeholderService.replaceCollectionPlaceholders(currentPromptText, collectionId);
			const responses = await getChatGptResponseTexts(promptTextWithCollectionValues_);
			const responseTexts = responses.reduce((acc, response) => {
				acc[getKey(response.combination)] = { text: response.response.trim() };
				return acc;
			}, {});
			setOriginalResponseTexts(responseTexts);
			setResponseTexts(responseTexts);
			setPromptTextWithCollectionValues(promptTextWithCollectionValues_);
		} catch (e) {
			setError(e);
		}
		setIsLoading(false);
	}

	return (
		<div {...useBlockProps()}>
			{error && (
				<Notice status="error" onRemove={() => setError(null)}>
					<p>{error.toString() ?? 'Fehler beim Senden der Anfrage'}</p>
				</Notice>
			)}
			<HeadingInput headingText={headingText} setHeadingText={setHeadingText} />
			<PromptHeading 
							collectionId={collectionId}
							updateCollectionId={updateCollectionId}
			/>
			<PromptTextarea
				promptText={promptText}
				setPromptText={setPromptText}
				sendChatGptRequests={sendChatGptRequests}
				isLoading={isLoading}

			/>
			<ResponseHeading />
			<VariableSelector
				selectValues={selectValues}
				setSelectValues={setSelectValues}
				isLoading={isLoading}
			/>
			<ResponseTextarea
				selectValues={selectValues}
				originalResponseTexts={originalResponseTexts}
				responseTexts={responseTexts}
				setResponseTexts={setResponseTexts}
				promptText={promptTextWithCollectionValues}
				isLoading={isLoading}
			/>
		</div>
	);
}
