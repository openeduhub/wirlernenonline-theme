import { TextareaControl, Button } from '@wordpress/components';
import variables from '../variables.json';
import { useState } from '@wordpress/element';

const variableLabels = variables.map((variable) => variable.label);
const helpText =
	'Formulieren Sie eine Anfrage an ChatGPT. ' +
	`Die Platzhalter ${variableLabels
		.map((label) => `$${label.toLocaleUpperCase()}$`)
		.join(', ')} werden automatisch durch die jeweiligen Werte ersetzt. ` +
	'Wenn Sie "Senden" klicken, werden die Antworten von ChatGPT eingeholt und überschreiben die vorigen Antworten inklusive aller eigener Anpassungen.';

export default function PromptTextarea({ promptText, setPromptText, sendChatGptRequests }) {
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
			<Button className="send-button" variant="primary" onClick={onClick}>
				Senden
			</Button>
		</div>
	);
}
