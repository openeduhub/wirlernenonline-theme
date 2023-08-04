import { SelectControl } from '@wordpress/components';
import variables from '../variables.json';

export default function VariableSelector({ selectValues, setSelectValues }) {
	const selects = variables.map((variable) => (
		<SelectControl
			label={variable.label}
			value={selectValues[variable.key]}
			options={variable.options}
			onChange={(value) => setSelectValues({ ...selectValues, [variable.key]: value })}
		/>
	));
	return <div className="variable-selector">{selects}</div>;
}
