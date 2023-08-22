import { SelectControl } from '@wordpress/components';

export default function VariableSelector({ selectValues, setSelectValues, isLoading, variables }) {
	const selects = variables.map((variable) => (
		<SelectControl
			label={variable.label}
			value={selectValues[variable.key]}
			options={variable.options}
			onChange={(value) => setSelectValues({ ...selectValues, [variable.key]: value })}
			disabled={isLoading}
		/>
	));
	return <div className="variable-selector">{selects}</div>;
}
