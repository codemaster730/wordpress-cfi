import { __ } from '@wordpress/i18n';
import { Component } from '@wordpress/element';
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { BaseControl, Button, ResponsiveWrapper, Spinner } from '@wordpress/components';
import { compose } from '@wordpress/compose';
import { withSelect } from '@wordpress/data';

const ALLOWED_MEDIA_TYPES = [ 'image' ];
const UNAUTHORIZED = <p>{ __( 'To edit the image, you need permission to upload media.', 'grimlock' ) }</p>;

class ImageSelectorControl extends Component {
    render() {
        const { label, help, value, onChange, gallery, image } = this.props;

        return (
            <BaseControl label={ label } help={ help } className="grimlock-image-selector-control">

                <MediaUploadCheck fallback={ UNAUTHORIZED }>
                    <MediaUpload onSelect={ ( value ) => onChange( ! gallery ? value.id : value.map( ( image ) => image.id ) ) }
                                 allowedTypes={ ALLOWED_MEDIA_TYPES }
                                 multiple={ !! gallery }
                                 gallery={ !! gallery }
                                 value={ value }
                                 render={ ( { open } ) => (
                                     <>
                                         <Button className={ ! value ? 'editor-post-featured-image__toggle' : 'editor-post-featured-image__preview' }
                                                 onClick={ open }>

                                             { ! value && ( __( 'No image selected', 'grimlock' ) ) }
                                             { !! value && ! image && <Spinner /> }
                                             { !! value && image &&
                                             ( image.length ?
                                                 <div style={ { textAlign: 'left' } }>
                                                     { image.map( ( item ) => !! item && (
                                                         <div style={ { display: 'inline-block', width: '33%', padding: '2px' } }>
                                                             <ResponsiveWrapper naturalWidth={ item.media_details.sizes.thumbnail.width }
                                                                                naturalHeight={ item.media_details.sizes.thumbnail.height }>
                                                                 <img src={ item.media_details.sizes.thumbnail.source_url } alt={ label } />
                                                             </ResponsiveWrapper>
                                                         </div>
                                                     ) ) }
                                                 </div> :
                                                 <ResponsiveWrapper naturalWidth={ image.media_details.width }
                                                                    naturalHeight={ image.media_details.height }>
                                                     <img src={ image.source_url } alt={ label } />
                                                 </ResponsiveWrapper> ) }

                                         </Button>

                                         <Button style={ { margin: '10px 10px 0 0' } } onClick={ open } isPrimary>
                                             { ! gallery ?
                                                 ( ! value ? __( 'Select Image', 'grimlock' ) : __( 'Change Image', 'grimlock' ) ) :
                                                 ( ! value ? __( 'Select Images', 'grimlock' ) : __( 'Change Images', 'grimlock' ) ) }
                                         </Button>

                                         { !! value &&
                                         <Button onClick={ () => onChange( 0 ) } isLink isDestructive>
                                             { ! gallery ? __( 'Remove image', 'grimlock' ) : __( 'Remove all images', 'grimlock' ) }
                                         </Button> }
                                     </>
                                 ) } />
                </MediaUploadCheck>

            </BaseControl>
        );
    }
}

export default compose(
    withSelect( ( select, props ) => {
        const { getMedia } = select( 'core' );
        const { value } = props;
        let image = null;

        if ( value.length ) {
            image = value.map( ( imageId ) => getMedia( imageId ) );

            // If some images are falsy (meaning not finished loading) we force the value to null
            if ( image.some( ( item ) => ! item ) )
                image = null;
        }
        else if ( value )
            image = getMedia( value );

        return { image };
    } ),
)( ImageSelectorControl );
