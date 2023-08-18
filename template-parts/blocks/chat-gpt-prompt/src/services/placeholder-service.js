import variables from '../data/variables';
import collectionService from '../services/collection-service';
import collectionPlaceholders from '../data/collection-placeholders.json';

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

	getPlaceholderKey(label) {
		label = label.replace(/[\s-\*]/, '_');
		label = label.toUpperCase();
		return '$' + label + '$';
	}

	async replaceCollectionPlaceholders(promptText, collectionId) {
		try {
			const placeholderValues = await this.getCollectionPlaceholders(collectionId);
			return this._replacePlaceholders(promptText, placeholderValues);
		} catch (e) {
			return promptText;
		}
	}

	async getCollectionPlaceholders(collectionId) {
		const collectionData = await collectionService.getCollectionData(collectionId);
		const collectionHierarchy = await collectionService.getCollectionHierarchy(collectionId);
		let result = [];
		for (const placeholder of collectionPlaceholders) {
			const values = this._getCollectionPlaceholderValues(
				placeholder,
				collectionData,
				collectionHierarchy,
			);
			result = [...result, ...values];
		}
		return result;
	}

	_replacePlaceholders(text, placeholders) {
		let result = text;
		for (const [variableLabel, optionLabel] of placeholders) {
			result = result.replace(this.getPlaceholderKey(variableLabel), optionLabel);
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

	/**
	 * @returns {[label: string, value: string][]}
	 */
	_getCollectionPlaceholderValues(placeholder, collectionData, collectionHierarchy) {
		let values;
		if (placeholder.property) {
			values = collectionData.properties[placeholder.property];
		} else if (placeholder.computeKey) {
			values = this._getComputedPlaceholderValues(placeholder, collectionData, collectionHierarchy);
		}
		if (values?.[0]?.trim()) {
			if (placeholder.multiple) {
				return [
					[placeholder.multipleLabel ?? placeholder.label, values.join(', ')],
					...values.map((value, index) => [`${placeholder.label}_${index}`, value]),
				];
			} else {
				return [[placeholder.label, values[0]]];
			}
		} else {
			return [];
		}
	}

	_getComputedPlaceholderValues(placeholder, collectionData, collectionHierarchy) {
		switch (placeholder.computeKey) {
			case 'collectionUrl':
				return [
					window.chatGptPromptConfig.eduSharingUrl +
						'components/collections?id=' +
						collectionData.ref.id,
				];
			case 'hierarchy':
				const [portal, ...parents] = collectionHierarchy;
				return parents.map((node) => node.title);
			default:
				return [];
		}
	}
}

export default new PlaceholderService();
