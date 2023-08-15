import { Notice, Spinner } from '@wordpress/components';
import { useEffect, useState } from '@wordpress/element';
import placeholderService from '../services/placeholder-service';

export default function CollectionPlaceholders({ collectionId }) {
	const [placeholders, setPlaceholders] = useState();
	const [loadingState, setLoadingState] = useState();
	const [error, setError] = useState();

	useEffect(() => {
		setLoadingState('loading');
		placeholderService
			.getCollectionPlaceholders(collectionId)
			.then((placeholders) => {
				setLoadingState('success');
				setPlaceholders(placeholders);
			})
			.catch((e) => {
				setError(e);
				setLoadingState('error');
			});
	}, [collectionId]);

	function getPlaceholdersList() {
		return (
			<table className="placeholders-table">
				{placeholders.map(([placeholder, value]) => (
					<tr>
						<td>
							<code>${placeholder.toUpperCase()}$</code>
						</td>
						<td>{value}</td>
					</tr>
				))}
			</table>
		);
	}

	function getContent() {
		switch (loadingState) {
			case 'loading':
				return <Spinner />;
			case 'success':
				return getPlaceholdersList();
			case 'error':
				return (
					error && (
						<Notice status="error" onRemove={() => setError(null)}>
							<p>{error.toString() ?? 'Error loading collection data'}</p>
						</Notice>
					)
				);
		}
	}

	return <div className="collection-placeholders">{getContent()}</div>;
}
