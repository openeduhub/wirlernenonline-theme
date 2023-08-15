import { TextareaControl } from '@wordpress/components';
import placeholderService from '../services/placeholder-service';
import { getKey } from '../utils/responseTexts';

export default function ResponseTextarea({
	selectValues,
	originalResponseTexts,
	responseTexts,
	setResponseTexts,
	promptText,
	isLoading,
}) {
	const label =
		'Die Antwort von ChatGPT für die Anfrage ' +
		`"${placeholderService.replaceVariablePlaceholders(promptText, selectValues)}"`;

	function onChange(text) {
		const key = getKey(selectValues);
		const changed = originalResponseTexts[key].text !== text.trim();
		setResponseTexts({
			...responseTexts,
			[key]: { text, changed, editedBy: responseTexts[key]?.editedBy },
		});
	}

	return (
		<div className="prompt-textarea">
			<TextareaControl
				className="textarea"
				label={label}
				help="Sie können die Antwort selbst anpassen."
				value={responseTexts[getKey(selectValues)]?.text ?? ''}
				onChange={onChange}
				disabled={isLoading}
			/>
		</div>
	);
}
