export function getKey(selectValues, enabledVariables) {
	return Object.keys(selectValues)
		.filter((key) => enabledVariables.some((variable) => variable.key === key))
		.sort()
		.map((key) => `${key}:${selectValues[key]}`)
		.join('|');
}
