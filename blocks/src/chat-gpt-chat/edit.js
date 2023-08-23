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
import {useEffect, useState} from "@wordpress/element";
import {TextareaControl, TextControl} from "@wordpress/components";

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 */
export default function Edit({attributes, setAttributes, clientId}) {

	useEffect(() => {
		if (!attributes.id) {
			setAttributes({id: clientId});
		}
	})
	const [systemPrompt, setSystemPrompt] = useState(attributes.systemPrompt || '');
	const [headingText, setHeadingText] = useState(attributes.headingText || 'Chatbot');

	useEffect(() => {
		setAttributes({
			systemPrompt,
			headingText
		});
	}, [systemPrompt, headingText]);

	return (
		<div { ...useBlockProps() }>
			<div className="heading-input">
				<TextControl label="Überschrift" value={headingText} onChange={setHeadingText} />
			</div>
			<TextareaControl
				label="Initialer Prompt"
				help={
					<>
						<p>
							Hier können Sie einen initialen Prompt eingeben, der für ChatGPT einen Gesprächskontext mitliefert.
						</p>
					</>
				}
			 	onChange={setSystemPrompt}
				value={systemPrompt}
			/>
		</div>
	);
}
