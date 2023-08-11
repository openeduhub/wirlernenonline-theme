import { TextareaControl } from '@wordpress/components';
import { replacePromptPlaceholders } from '../utils/chatGpt';
import { getKey } from '../utils/responseTexts';

export default function ResponseTextarea({
	selectValues,
	responseTexts,
	setResponseTexts,
	promptText,
}) {
	const label = `Die Antwort von ChatGPT für die Anfrage "${replacePromptPlaceholders(
		promptText,
		selectValues,
	)}"`;

	return (
		<div className="prompt-textarea">
			<TextareaControl
				className="textarea"
				label={label}
				help="Sie können die Antwort selbst anpassen"
				value={responseTexts[getKey(selectValues)] ?? ''}
				onChange={(text) => setResponseTexts({ ...responseTexts, [getKey(selectValues)]: text })}
			/>
		</div>
	);
}
