import { Button } from '@wordpress/components';
import variables from '../data/variables';
import CollectionPlaceholders from './collection-placeholders';

const variableLabels = variables.map((variable) => variable.label);

export default function PromptTextarea({ collectionId, updateCollectionId }) {
	return (
		<div className="prompt-heading">
			<h3>Anfrage</h3>
			<p>Formulieren Sie eine Anfrage an ChatGPT.</p>
			<p>
				Die Platzhalter{' '}
				{variableLabels.map((label, index) => (
					<>
						<code>${label.toLocaleUpperCase()}$</code>
						{index < variableLabels.length - 1 ? ', ' : ' '}
					</>
				))}
				werden automatisch durch die jeweils auf der Seite eingestellten Werte ersetzt.
			</p>

			<p>
				{collectionId
					? 'Weitere Platzhalter mit Werten aus der angegebenen Sammlung.'
					: 'Weitere Platzhalter können durch Angeben einer Sammlungs-URL aus den Sammlungsdaten genutzt werden.'}
				<Button variant="link" onClick={updateCollectionId}>
					Aktualisiere Sammlungswerte
				</Button>
			</p>
			{collectionId && <CollectionPlaceholders collectionId={collectionId} />}
		</div>
	);
}
