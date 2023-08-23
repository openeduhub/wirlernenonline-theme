import { Button, Spinner, TextareaControl } from '@wordpress/components';
import { useState } from '@wordpress/element';

export default function PromptTextarea({
	promptText,
	setPromptText,
	sendChatGptRequests,
	isLoading,
}) {
	const [currentPromptText, setCurrentPromptText] = useState(promptText);

	function send() {
		setPromptText(currentPromptText);
		sendChatGptRequests(currentPromptText);
	}

	return (
		<div className="prompt-textarea">
			<TextareaControl
				className="textarea"
				label="Prompt für ChatGPT"
				help={
					<>
						<p>
							Wenn Sie "Senden" klicken, werden die Antworten von ChatGPT eingeholt und
							überschreiben die vorigen Antworten inklusive aller eigener Anpassungen.
						</p>
					</>
				}
				value={currentPromptText}
				onChange={setCurrentPromptText}
			/>
			<Button className="send-button" variant="primary" onClick={send} disabled={isLoading}>
				{isLoading ? <Spinner /> : 'Senden'}
			</Button>
		</div>
	);
}
