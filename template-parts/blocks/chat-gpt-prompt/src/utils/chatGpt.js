import variables from '../variables.json';

export function getChatGptResponseTexts(prompt) {
	const promises = [];
	const combinations = getOptionCombinations();
	for (const combination of combinations) {
		let combinationPrompt = prompt;
		for (const [key, value] of Object.entries(combination)) {
			const { variableLabel, optionLabel } = getKeyValueLabels(key, value);
			combinationPrompt = combinationPrompt.replace(
				`$${variableLabel.toLocaleUpperCase()}$`,
				optionLabel,
			);
		}
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
function getOptionCombinations(vars = variables) {
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

function getKeyValueLabels(key, value) {
	const variable = variables.find((v) => v.key === key);
	const option = variable.options.find((o) => o.value === value);
	return { variableLabel: variable.label, optionLabel: option.label };
}

async function getChatGptResponseText(prompt) {
	const response = await fetch(ajaxurl, {
		method: 'post',
		body: getFormData({
			action: 'wloChatGptPrompt',
			prompt,
		}),
	});
	const json = await response.json();
	return json.responses[0];
}

function getFormData(data) {
	const formData = new FormData();
	for (const [key, value] of Object.entries(data)) {
		formData.append(key, value);
	}
	return formData;
}
