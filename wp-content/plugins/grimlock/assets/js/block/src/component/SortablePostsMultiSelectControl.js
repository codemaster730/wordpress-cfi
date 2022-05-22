import SortableMultiSelectControl from './SortableMultiSelectControl';
import { withSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

const loadPosts = ( select, ownProps ) => {
    let { selectedPosts, selectedPostIds, postType, inputValue } = ownProps;

    let options = false;

    // Load posts to populate options
    let optionsResults = select( 'core' ).getEntityRecords( 'postType', postType, { search: inputValue } );

    // Populate options when we have results
    if ( optionsResults ) {
        options = optionsResults.length ?
            optionsResults.map( ( post ) => ( { value: post.id, label: post.title.rendered, } ) ) :
            [ { value: '', label: __( '-- No post found --', 'grimlock' ), isDisabled: true } ];
    }

    if ( ! selectedPostIds || ! selectedPostIds.length )
        // Empty select posts if we have no selected post ids
        selectedPosts = [];
    else if ( ! selectedPosts ) {
        // Load selected posts using selected post ids
        let selectedPostsResults = select( 'core' ).getEntityRecords( 'postType', postType, { include: selectedPostIds, orderby: 'include', order: 'asc' } );

        // Populate selected posts when we have results
        if ( selectedPostsResults ) {
            selectedPosts = selectedPostsResults.length ?
                selectedPostsResults.map( ( post ) => ( { value: post.id, label: post.title.rendered } ) ) : [];
        }
    }

    return { options, value: selectedPosts };
};

let previousPostType;
const SortablePostsMultiSelectControl = withSelect( loadPosts )( ( withSelectProps ) => {
    // Clear selected posts if post type has changed
    if ( previousPostType && previousPostType !== withSelectProps.postType )
        withSelectProps.onChange( [] );

    // Cache post type to allow for detecting its changes
    previousPostType = withSelectProps.postType;

    // Determine loading state
    let isLoading = false;
    if ( ! withSelectProps.options || ! withSelectProps.value )
        isLoading = true;

    return <SortableMultiSelectControl { ...withSelectProps } isLoading={ isLoading } />;
} );

export default SortablePostsMultiSelectControl;