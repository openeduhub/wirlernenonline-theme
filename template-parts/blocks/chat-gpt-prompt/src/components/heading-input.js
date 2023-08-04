import { TextControl } from '@wordpress/components';

export default function HeadingInput({ headingText, setHeadingText }) {
	return (
		<div className="heading-input">
			<TextControl label="Überschrift" value={headingText} onChange={setHeadingText} />
		</div>
	);
}
