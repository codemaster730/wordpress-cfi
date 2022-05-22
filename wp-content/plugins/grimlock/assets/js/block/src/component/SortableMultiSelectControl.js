import { Component } from '@wordpress/element';
import { BaseControl } from '@wordpress/components';
import Select, { components } from 'react-select';
import { SortableContainer, SortableElement } from 'react-sortable-hoc';

const SortableMultiValue = SortableElement( ( props ) => {
    // This prevents the menu from being opened/closed when the user clicks on a value to begin dragging it.
    const onMouseDown = ( e ) => {
        e.preventDefault();
        e.stopPropagation();
    };

    return <components.MultiValue { ...props } innerProps={ { onMouseDown } } />;
});

const SortableSelect = SortableContainer( Select );

export default class SortableMultiSelectControl extends Component {
    handleSortEnd( oldIndex, newIndex, selectedOptions ) {
        selectedOptions = selectedOptions.slice();
        selectedOptions.splice( newIndex < 0 ? selectedOptions.length + newIndex : newIndex, 0, selectedOptions.splice( oldIndex, 1 )[0] );
        this.props.onChange( selectedOptions );
    }

    render() {
        let { isLoading, label, help, value, options, onChange, inputValue, onInputChange } = this.props;

        return (
            <BaseControl label={ label } help={ help } className="grimlock-sortable-multi-select-control">

                <SortableSelect // react-sortable-hoc props:
                                axis="xy"
                                onSortEnd={ ( { oldIndex, newIndex } ) => this.handleSortEnd( oldIndex, newIndex, value ) }
                                distance={ 4 }

                                // small fix for https://github.com/clauderic/react-sortable-hoc/pull/352:
                                getHelperDimensions={ ( { node } ) => node.getBoundingClientRect() }

                                // react-select props:
                                isLoading={ isLoading }
                                isMulti
                                isClearable={ false }
                                options={ options || [] }
                                value={ value || [] }
                                onChange={ onChange }
                                inputValue={ inputValue || '' }
                                onInputChange={ ( inputValue, { action } ) => action === 'input-change' && onInputChange( inputValue ) }
                                components={ { MultiValue: SortableMultiValue } }
                                closeMenuOnSelect={ false }
                                styles={ {
                                    multiValue: ( provided ) => ( {
                                        ...provided,
                                        flexGrow: 1,
                                        justifyContent: 'space-between',
                                    } ),
                                    input: ( provided ) => ( {
                                        ...provided,
                                        width: '100%',
                                    } ),
                                } }
                                className="grimlock-sortable-multi-select"
                                classNamePrefix="grimlock" />

            </BaseControl>
        );
    }
}
