/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * The save function defines the way in which the different attributes should
 * be combined into the final markup, which is then serialized by the block
 * editor into `post_content`.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#save
 *
 * @return {WPElement} Element to render.
 */
export default function save({ attributes }) {
	const id = attributes.id ?? Math.random().toString(36).replace('0.', 'chat-gpt-block-');
	const currentUser = wp.data.select('core').getCurrentUser();

	/**
	 * Gets the response text to be saved from the attribute `responseTexts` populated by `Edit`.
	 *
	 * Looks for the property `changed` and adds the current user to the `editedBy` array if
	 * `changed` is `true`.
	 */
	function getResponseTexts() {
		const responseTexts = {};
		for (const [key, value] of Object.entries(attributes.responseTexts)) {
			let { text, changed, editedBy } = value;
			if (changed && !editedBy?.includes(currentUser.name)) {
				editedBy = [...(editedBy ?? []), currentUser.name];
			}
			responseTexts[key] = { text: text.trim(), editedBy };
		}
		return responseTexts;
	}

	attributes.responseTexts = getResponseTexts();
	
	return (
		<div
			{...useBlockProps.save()}
			id={id}
			data-response-texts={JSON.stringify(attributes.responseTexts)}
		>
			<h2>{attributes.headingText}</h2>
			<p className="response-text"></p>
			<p className="edited-by"></p>
			<script>{`jQuery(document).ready(() => registerChatGptBlock('${id}'))`}</script>
		</div>
	);
}
