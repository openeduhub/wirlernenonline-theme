import variables from '../data/variables';
import collectionService from '../services/collection-service';

const collectionProperties = [
	{
		label: 'Titel',
		key: 'title',
	},
	{
		label: 'URL',
		key: 'properties.cclom:location',
	},
];

class PlaceholderService {
	/**
	 * Replaces placeholders of the form $VARIABLE$ within the `promptText` and returns the modified
	 * text.
	 *
	 * @param {string} promptText
	 * @param {{[key: string]: string;}} selectedVariables
	 * @returns {string}
	 */
	replaceVariablePlaceholders(promptText, selectedVariables) {
		const placeholders = [
			...Object.entries(selectedVariables).map(([key, value]) =>
				this._getKeyValueLabels(key, value),
			),
		];
		return this._replacePlaceholders(promptText, placeholders);
	}

	async replaceCollectionPlaceholders(promptText, collectionId) {
		try {
			const placeholders = await this.getCollectionPlaceholders(collectionId);
			return this._replacePlaceholders(promptText, placeholders);
		} catch (e) {
			return promptText;
		}
	}

	async getCollectionPlaceholders(collectionId) {
		const collectionData = await collectionService.getCollectionData(collectionId);
		const result = [];
		for (const property of collectionProperties) {
			const value = this._getCollectionValue(property.key, collectionData);
			if (value) {
				result.push([property.label, value]);
			}
		}
		return result;
	}

	_replacePlaceholders(text, placeholders) {
		let result = text;
		for (const [variableLabel, optionLabel] of placeholders) {
			result = result.replace(`$${variableLabel.toUpperCase()}$`, optionLabel);
		}
		return result;
	}

	/**
	 * Returns a tuple of variable and option label for the given variable and value.
	 */
	_getKeyValueLabels(key, value) {
		const variable = variables.find((v) => v.key === key);
		const option = variable.options.find((o) => o.value === value);
		return [variable.label, option.label];
	}

	_getCollectionValue(key, collectionData) {
		let data = collectionData;
		for (const fragment of key.split('.')) {
			data = data?.[fragment];
		}
		return data;
	}
}

export default new PlaceholderService();
