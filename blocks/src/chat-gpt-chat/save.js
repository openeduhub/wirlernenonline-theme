/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';
import {TextControl} from "@wordpress/components";

/**
 * The save function defines the way in which the different attributes should
 * be combined into the final markup, which is then serialized by the block
 * editor into `post_content`.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#save
 *
 */
export default function save({attributes}) {
	return (
		<div
			{ ...useBlockProps.save() }
			id={attributes.id}
			data-system-prompt={attributes.systemPrompt}
		>
			<h2>{attributes.headingText}</h2>
			<div className="bubble-container">
				<div className="single-bubble-container">
					<img src="/wp-content/themes/wir-lernen-online/src/assets/img/robot.svg"/>
					<div className="bubble left">Hallo! Möchtest Du mit mir üben?
					</div>
				</div>
				<div className="bubble right">Ja, gerne!</div>
				<div className="single-bubble-container">
					<img src="/wp-content/themes/wir-lernen-online/src/assets/img/robot.svg"/>
					<div className="bubble left">Ok! Was ist korrekt? Der Wald oder das Wald?</div>
				</div>
				<div className="bubble right">Der Wald</div>
				<div className="single-bubble-container">
					<img src="/wp-content/themes/wir-lernen-online/src/assets/img/robot.svg"/>
					<div className="bubble left">Super! Was gehört in die Lücke: Der Bauer geht in ___ Wald.</div>
				</div>
			</div>

			<div className="chatbot-input-container">
				<input type="text" id={attributes.id + "-input"} name="text"/>
				<button type="button" id={attributes.id + "-submit"}>Abschicken</button>
			</div>
		</div>
	);
}
