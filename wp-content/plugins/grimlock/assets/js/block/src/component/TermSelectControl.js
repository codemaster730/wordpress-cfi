import {
    Spinner,
    SelectControl,
    BaseControl,
} from '@wordpress/components';
import { withSelect } from '@wordpress/data';

const SelectControlWithTermsLoading = withSelect( ( select, ownProps ) => {
	let { value, onChange } = ownProps;

    let options = false;

    // Load terms from the given taxonomy
    let terms = select( 'core' ).getEntityRecords( 'taxonomy', ownProps.taxonomy, { per_page: -1, ...ownProps.queryArgs } );

    // Populate options when we have results
    if ( terms ) {
        options = terms.length ? [
            { value: '', label: ownProps.emptyChoice },
            ...terms.map( ( term ) => {
                return {
                    value: term.id,
                    label: term.name,
                };
            } )
        ] : [];
    }

    // Reset currently selected value if it isn't available as an option anymore
    if ( value && value !== '' && options && options.length && ! options.some( ( option ) => option.value == value ) ) {
		onChange('');
	}

    return { options, value };
} )( ( props ) => {
    if ( ! props.options )
        // Display loader if props.options is false
        return (
            <BaseControl label={ props.label } help={ props.help }>
                <div>
                    <Spinner />
                </div>
            </BaseControl>
        );
    else if ( props.options && ! props.options.length )
        // Hide the field if there are no options
        return null;
    else
        // Display a select field if we have some options
        return <SelectControl { ...props } />;
} );

export default SelectControlWithTermsLoading;