export function getKey(selectValues) {
	return Object.keys(selectValues)
		.sort()
		.map((key) => `${key}:${selectValues[key]}`)
		.join('|');
}