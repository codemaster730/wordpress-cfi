import { useInstanceId } from '@wordpress/compose';
import { BaseControl } from '@wordpress/components';

export default function SelectControlWithOptGroup( {
    help,
    label,
    multiple = false,
    onChange,
    optgroups = [],
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
        !! optgroups && !! optgroups.length && (
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
                    { optgroups.map( ( { label: optgroupLabel, options }, optgroupIndex ) => (
                        <optgroup label={ optgroupLabel } key={ optgroupIndex }>
                            { options.map( ( option, optionIndex ) => (
                                <option key={ `${ option.label }-${ option.value }-${ optionIndex }` }
                                        value={ option.value }
                                        disabled={ option.disabled }>
                                    { option.label }
                                </option>
                            ) ) }
                        </optgroup>
                    ) ) }
                </select>
            </BaseControl>
        )
    );
}