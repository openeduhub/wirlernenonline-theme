import { Panel, PanelBody, PanelRow } from '@wordpress/components';
import variables from '../data/variables';
import CollectionPlaceholders from './collection-placeholders';

const variableLabels = variables.map((variable) => variable.label);

export default function PromptHeading({ collectionId, updateCollectionId }) {
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
			<Panel>
				<PanelBody
					title="Platzhalter aus Sammlungswerten"
					initialOpen={false}
					onToggle={updateCollectionId}
				>
					<PanelRow>
						{collectionId ? (
							<CollectionPlaceholders
								collectionId={collectionId}
								updateCollectionId={updateCollectionId}
							/>
						) : (
							<p>Geben Sie eine Sammlungs-URL an, um weitere Platzhalter zu nutzen.</p>
						)}
					</PanelRow>
				</PanelBody>
			</Panel>
		</div>
	);
}
