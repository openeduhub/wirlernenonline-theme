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
	const prompt = placeholderService.replaceVariablePlaceholders(promptText, selectValues);
	let label = 'Die Antwort von ChatGPT';
	if (prompt) {
		label += ' für die Anfrage ' + `"${prompt}"`;
	}
	const enabledVariables = placeholderService.getEnabledVariables(promptText);

	function onChange(text) {
		const key = getKey(selectValues, enabledVariables);
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
				value={responseTexts[getKey(selectValues, enabledVariables)]?.text ?? ''}
				rows={responseTexts[getKey(selectValues, enabledVariables)]?.text ? 10 : 4}
				onChange={onChange}
				disabled={isLoading}
			/>
		</div>
	);
}
