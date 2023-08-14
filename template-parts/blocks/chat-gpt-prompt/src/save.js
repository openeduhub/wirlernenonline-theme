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
	function getResponseTexts(responseTexts) {
		const result = {};
		for (const [key, value] of Object.entries(responseTexts)) {
			let { text, changed, editedBy } = value;
			if (changed && !editedBy?.includes(currentUser.name)) {
				editedBy = [...(editedBy ?? []), currentUser.name];
			}
			result[key] = { text: text.trim(), editedBy };
		}
		return result;
	}

	// The save function is both the entry point to first see parsed attributes and the last
	// function to modify attributes before they get written into page content.
	//
	// We use this position to...
	if (typeof attributes.responseTexts === 'string') {
		// ...parse a JSON string from an attribute when initially opening the editor and convert it
		// to an object for further modification by the `Edit` function
		attributes.responseTexts = JSON.parse(attributes.responseTexts);
	} else if (typeof attributes.responseTexts === 'object') {
		// ...and to to do final processing before saving after the `Edit` function has modified the
		// object.
		attributes.responseTexts = getResponseTexts(attributes.responseTexts);
	}

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
