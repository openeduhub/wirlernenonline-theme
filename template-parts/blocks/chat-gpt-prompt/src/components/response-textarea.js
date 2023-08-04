import { TextareaControl } from '@wordpress/components';
import { getKey } from '../utils/responseTexts';
import variables from '../variables.json';

export default function ResponseTextarea({ selectValues, responseTexts, setResponseTexts }) {
	const valuePairs = Object.entries(selectValues).map(([key, value]) => {
		const variable = variables.find((v) => v.key === key);
		const option = variable.options.find((o) => o.value === value);
		return `${variable.label}: ${option.label}`;
	});
	const label = `Die Antwort von ChatGPT für die Werte ${valuePairs.join(', ')}`;

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
