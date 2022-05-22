import { useInstanceId } from '@wordpress/compose';
import { BaseControl } from '@wordpress/components';

export default function SelectControlWithOptGroup( {
    help,
    label,
    multiple = false,
    onChange,
    options = [],
    className,
    hideLabelFromVision,
    ...props
} ) {
    const instanceId = useInstanceId( SelectControlWithOptGroup );
    const id         = `inspector-select-control-${ instanceId }`;

    const onChangeValue = ( event ) => {
        if ( multiple ) {
            const selectedOptions = [ ...event.target.options ].filter( ( { selected } ) => selected );
            const newValues = selectedOptions.map( ( { value } ) => value );
            onChange( newValues );
            return;
        }
        onChange( event.target.value );
    };

    return (
        !! options && !! options.length && (
            <BaseControl label={ label }
                         hideLabelFromVision={ hideLabelFromVision }
                         id={ id }
                         help={ help }
                         className={ className }>
                <select id={ id }
                        className="components-select-control__input"
                        onChange={ onChangeValue }
                        aria-describedby={ !! help ? `${ id }__help` : undefined }
                        multiple={ multiple }
                        { ...props }>
                    { options.map( ( { label, value, disabled, options }, optionIndex ) => {
                    	if ( !! options && !! options.length ) {
							return (
								<optgroup label={ label } key={ optionIndex }>
									{ options.map( ( subOption, subOptionIndex ) => (
										<option key={ `${ subOption.label }-${ subOption.value }-${ subOptionIndex }` }
												value={ subOption.value }
												disabled={ subOption.disabled }>
											{ subOption.label }
										</option>
									) ) }
								</optgroup>
							);
                    	}

                    	return (
							<option key={ `${ label }-${ value }-${ optionIndex }` }
									value={ value }
									disabled={ disabled }>
								{ label }
							</option>
						);
					} ) }
                </select>
            </BaseControl>
        )
    );
}