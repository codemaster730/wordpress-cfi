import SortableMultiSelectControl from './SortableMultiSelectControl';
import { withSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

const loadTerms = ( select, ownProps ) => {
    let { selectedTerms, selectedTermIds, taxonomy, inputValue } = ownProps;

    let options = false;

    // Load terms to populate options
    let optionsResults = select( 'core' ).getEntityRecords( 'taxonomy', taxonomy, { search: inputValue } );

    // Populate options when we have results
    if ( optionsResults ) {
        options = optionsResults.length ?
            optionsResults.map( ( term ) => ( { value: term.id, label: term.name, } ) ) :
            [ { value: '', label: __( '-- No term found --', 'grimlock' ), isDisabled: true } ];
    }

    if ( ! selectedTermIds || ! selectedTermIds.length )
        // Empty select terms if we have no selected term ids
        selectedTerms = [];
    else if ( ! selectedTerms ) {
        // Load selected terms using selected term ids
        let selectedTermsResults = select( 'core' ).getEntityRecords( 'taxonomy', taxonomy, { include: selectedTermIds, orderby: 'include', order: 'asc' } );

        // Populate selected terms when we have results
        if ( selectedTermsResults ) {
            selectedTerms = selectedTermsResults.length ?
                selectedTermsResults.map( ( term ) => ( { value: term.id, label: term.name } ) ) : [];
        }
    }

    return { options, value: selectedTerms };
};

let previousTaxonomy;
const SortableTermsMultiSelectControl = withSelect( loadTerms )( ( withSelectProps ) => {
    // Clear selected terms if taxonomy has changed
    if ( previousTaxonomy && previousTaxonomy !== withSelectProps.taxonomy )
        withSelectProps.onChange( [] );

    // Cache taxonomy to allow for detecting its changes
    previousTaxonomy = withSelectProps.taxonomy;

    // Determine loading state
    let isLoading = false;
    if ( ! withSelectProps.options || ! withSelectProps.value )
        isLoading = true;

    return <SortableMultiSelectControl { ...withSelectProps } isLoading={ isLoading } />;
} );

export default SortableTermsMultiSelectControl;