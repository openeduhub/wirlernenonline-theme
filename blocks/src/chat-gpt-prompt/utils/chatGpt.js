import placeholderService from '../services/placeholder-service';

export function getChatGptResponseTexts(prompt) {
	const promises = [];
	const enabledVariables = placeholderService.getEnabledVariables(prompt);
	const combinations = getOptionCombinations(enabledVariables);
	for (const combination of combinations) {
		const combinationPrompt = placeholderService.replaceVariablePlaceholders(prompt, combination);
		promises.push(
			getChatGptResponseText(combinationPrompt).then((response) => ({
				combination,
				response,
			})),
		);
	}
	return Promise.all(promises);
}

/**
 * Returns an array with all key-value combination of variables.
 *
 * E.g.
 * ```
 * [
 *  { role: 'student', 'native-language': 'german' },
 *  { role: 'student', 'native-language': 'english' },
 *  ...
 * ]
 * ```
 *
 * @returns {{[key: string]: string}[]}
 */
function getOptionCombinations(vars) {
	if (vars.length === 0) {
		return [{}];
	}
	const [head, ...tail] = vars;
	const tailCombinations = getOptionCombinations(tail);
	return head.options.flatMap((option) => {
		return tailCombinations.map((combination) => ({
			...combination,
			[head.key]: option.value,
		}));
	});
}

async function getChatGptResponseText(prompt) {
	const response = await fetch(ajaxurl, {
		method: 'post',
		body: getFormData({
			action: 'wloChatGptPrompt',
			prompt,
		}),
	});
	if (response.ok) {
		const json = await response.json();
		return json.responses[0];
	} else {
		console.error(response);
		throw new Error('Fehler bei Chat-GPT-Anfrage: ' + response.status);
	}
}

function getFormData(data) {
	const formData = new FormData();
	for (const [key, value] of Object.entries(data)) {
		formData.append(key, value);
	}
	return formData;
}