/**
 * Returns the value of the ACF field with the given name.
 *
 * In case multiple fields are found, returns the first one to have a value.
 */
function getAcfField(name) {
	const fields = acf.getFields({ name });
	return fields.map((field) => field.val()).find((value) => !!value);
}

function getCollectionId(url) {
	const search = new URL(url).search;
	const searchParams = new URLSearchParams(search);
	const id = searchParams.get('id');
	return id;
}

class CollectionService {
	/**
	 * The ID of the collection for which data has been fetched / is being fetched.
	 *
	 * @type {string}
	 */
	_collectionId;
	/**
	 * A Promise resolving to collection data matching `_collectionId`.
	 *
	 * @type {Promise}
	 */
	_collectionData;
	/**
	 * @type {Promise}
	 */
	_collectionsHierarchy;

	/**
	 * Returns the ID of the collection given by the ACF field 'collection_url'.
	 */
	getCurrentCollectionId() {
		const collectionUrl = getAcfField('collection_url');
		if (collectionUrl) {
			try {
				const collectionId = getCollectionId(collectionUrl);
				return collectionId;
			} catch (e) {
				return null;
			}
		}
		return null;
	}

	/**
	 * Gets collection data of the given collection.
	 *
	 * Data is fetched once from the API. For subsequent requests, the cached data object is
	 * returned.
	 *
	 * @returns {Promise}
	 */
	getCollectionData(collectionId) {
		this._updateCollectionId(collectionId);
		this._collectionData ??= this._fetchCollectionData(collectionId);
		return this._collectionData;
	}

	getCollectionHierarchy(collectionId) {
		this._updateCollectionId(collectionId);
		this._collectionsHierarchy ??= this._fetchCollectionHierarchy(collectionId);
		return this._collectionsHierarchy;
	}

	async _updateCollectionId(collectionId) {
		if (!collectionId || collectionId !== this._collectionId) {
			this._collectionId = collectionId;
			this._collectionData = null;
			this._collectionsHierarchy = null;
		}
	}

	async _fetchCollectionData(collectionId) {
		const response = await fetch(
			window.chatGptPromptConfig.eduSharingUrl +
				'rest/collection/v1/collections/-home-/' +
				collectionId,
		);
		if (response.ok) {
			const data = await response.json();
			return data.collection;
		} else {
			throw new Error(`Failed to fetch collection data for id ${collectionId}`);
		}
	}

	async _fetchCollectionHierarchy(collectionId) {
		const response = await fetch(
			window.chatGptPromptConfig.eduSharingUrl +
				'rest/node/v1/nodes/-home-/' +
				collectionId +
				'/parents',
		);
		if (response.ok) {
			const data = await response.json();
			return data.nodes.reverse();
		} else {
			throw new Error(`Failed to fetch collection hierarchy for id ${collectionId}`);
		}
	}
}

export default new CollectionService();
