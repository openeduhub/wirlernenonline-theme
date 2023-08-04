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
	return (
		<div
			{...useBlockProps.save()}
			id={id}
			data-response-texts={JSON.stringify(attributes.responseTexts)}
		>
			<h2>Chat GPT Block</h2>
			<p className="response-text"></p>
			<script>{`jQuery(document).ready(() => registerChatGptBlock('${id}'))`}</script>
		</div>
	);
}
