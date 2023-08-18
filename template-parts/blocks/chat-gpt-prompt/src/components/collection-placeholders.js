import { Notice, Spinner, Button } from '@wordpress/components';
import { useEffect, useState } from '@wordpress/element';
import placeholderService from '../services/placeholder-service';

export default function CollectionPlaceholders({ collectionId, updateCollectionId }) {
	const [placeholders, setPlaceholders] = useState();
	const [loadingState, setLoadingState] = useState();
	const [error, setError] = useState();

	useEffect(() => {
		setError(null);
		if (!collectionId) {
			setLoadingState(null);
			setPlaceholders(null);
			return;
		}
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
							<code>{placeholderService.getPlaceholderKey(placeholder)}</code>
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
				return <Spinner className="collection-placeholders-spinner" />;
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

	return (
		<div className="collection-placeholders">
			{getContent()}
			<Button variant="link" onClick={updateCollectionId} disabled={loadingState === 'loading'}>
				Aktualisiere Sammlungswerte
			</Button>
		</div>
	);
}
