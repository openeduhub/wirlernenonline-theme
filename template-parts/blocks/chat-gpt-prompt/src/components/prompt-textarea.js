import { Button, Spinner, TextareaControl } from '@wordpress/components';
import { useState } from '@wordpress/element';
import variables from '../data/variables';

const variableLabels = variables.map((variable) => variable.label);
const helpText =
	'Formulieren Sie eine Anfrage an ChatGPT. ' +
	`Die Platzhalter ${variableLabels
		.map((label) => `$${label.toLocaleUpperCase()}$`)
		.join(', ')} werden automatisch durch die jeweiligen Werte ersetzt. ` +
	'Wenn Sie "Senden" klicken, werden die Antworten von ChatGPT eingeholt und überschreiben die vorigen Antworten inklusive aller eigener Anpassungen.';

export default function PromptTextarea({
	promptText,
	setPromptText,
	sendChatGptRequests,
	isLoading,
}) {
	const [currentPromptText, setCurrentPromptText] = useState(promptText);

	function onClick() {
		setPromptText(currentPromptText);
		sendChatGptRequests(currentPromptText);
	}

	return (
		<div className="prompt-textarea">
			<TextareaControl
				className="textarea"
				label="Prompt für ChatGPT"
				help={helpText}
				value={currentPromptText}
				onChange={setCurrentPromptText}
			/>
			<Button className="send-button" variant="primary" onClick={onClick} disabled={isLoading}>
				{isLoading ? <Spinner /> : 'Senden'}
			</Button>
		</div>
	);
}
