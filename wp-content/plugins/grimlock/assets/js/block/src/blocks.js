import { __ } from '@wordpress/i18n';
import { registerBlockStyle, unregisterBlockStyle, registerBlockCollection } from '@wordpress/blocks';
import domReady from '@wordpress/dom-ready';
import { addFilter } from '@wordpress/hooks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { Fragment } from '@wordpress/element';
import { PanelBody, RangeControl } from '@wordpress/components';
import { InspectorControls } from '@wordpress/editor';
import Block from './class/Block';

// Init Grimlock blocks
if ( window.grimlock_blocks ) {
    const { blocks } = window.grimlock_blocks;

    if ( blocks ) {
        Object.keys( blocks ).forEach( ( idBase ) => {
            new Block( blocks[ idBase ], idBase );
        } );
    }
}

// Register a block collection for all Grimlock blocks
registerBlockCollection( 'grimlock', { title: 'Grimlock', icon: (
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 150 150">
            <path fill="#4ACE9B" fill-rule="evenodd" d="M75 0a75 75 0 11-48 133h13c10 0 22 3 22 3 10 4 26 5 26 5 12 0 16-10 16-10v-2-1s2-2 2-5c0 0-1-2-2-1 0 0 0 3-2 4l-1-1s3-5 2-10c0 0 0-3-2-2l-4 10-5-2h1l2-10s-1-3-3-1c0 0-1 6-4 9l-3-1v-1h-1v1l-2-1a18 18 0 001-9s0-2-2-1l-4 9-2-1v-1h-4c0-2 2-5 1-9 0 0 0-3-2-1 0 0-1 5-4 8h-1v-4h-1l-2 3-2-1c1-1 3-5 2-9 0 0 0-3-2-1 0 0-1 5-4 8h-1c1-1 2-3 1-5 0 0 0-2-1-1l-2 5-3-2 1-7s0-2-2-1l-3 7v-1-4s0-3-2-1l-1 4-2-1 1-2s-1-2-2-1v2l-2-1 2-7s-1-2-2-1l-3 6-1-1 1-3s-1-2-2-1l-1 3-4-4-20-9-8-3a75 75 0 012-20h22l-1 6h2l2-6 4 1-1 6c0 2 2 1 2 1l2-7 6 1c0 5-2 9-2 9 1 2 3 0 3 0 2-2 3-6 3-7h2l-2 10c1 2 3 0 3 0l3-9 1 1-1 5c1 2 3 0 3 0l2-4-2 11c1 2 3 0 3 0 3-4 4-9 4-10h2l-1 7c1 2 2 0 2 0l3-6h1l-1 9c1 2 3 0 3 0l3-9h1l-2 9c1 2 3 0 3 0l3-8h3l-1 5c1 3 3 1 3 1l2-6h2l-1 10c1 2 2 0 3 0l3-9h1l-1 7c1 2 3 0 3 0l2-7h1l-2 12c1 3 4 0 4 0 3-5 4-11 4-11h1l-1 6c1 2 2 0 2 0l2-6 1 1-2 10c1 2 3 0 3 0 3-4 3-8 3-9h1v4c1 2 2 1 2 1l1-3-1 8c1 3 3 1 3 1l3-8 1 1-2 8c0 2 2 0 2 0l3-6 1-1c11-18-3-32-3-32-3-9-18-16-18-16-6-8-23-13-23-13-7-1-15-9-15-9-8-6-19-3-19-3 8-3 17-5 27-5zm14 33s6-2 12 2l15 9s6 5 2 10c0 0-3 1-10-3l-14-5s-4-2-5-5l-2-5s0-3 2-3zM37 16s18 2 27 18c0 0 4 6-2 10 0 0-20 12-30 5 0 0-12-11-5-25 0 0 3-6 10-8z"/>
        </svg>
    ) } );

// Register block styles
registerBlockStyle( 'core/image' , {
	name: 'small-rounded',
	label: __( 'Small Rounded', 'grimlock' ),
	isDefault: false,
} );

registerBlockStyle( 'core/image' , {
    name: 'cut-corner',
    label: __( 'Cut Corner', 'grimlock' ),
    isDefault: false,
} );

registerBlockStyle( 'core/image' , {
	name: 'triangle',
	label: __( 'Triangle', 'grimlock' ),
	isDefault: false,
} );

registerBlockStyle( 'core/image' , {
	name: 'diamond',
	label: __( 'Diamond', 'grimlock' ),
	isDefault: false,
} );

registerBlockStyle( 'core/image' , {
	name: 'hexagon',
	label: __( 'Hexagon', 'grimlock' ),
	isDefault: false,
} );

registerBlockStyle( 'core/image' , {
	name: 'angle',
	label: __( 'Angle', 'grimlock' ),
	isDefault: false,
} );

registerBlockStyle( 'core/image' , {
	name: 'shadow',
	label: __( 'Shadow', 'grimlock' ),
	isDefault: false,
} );

registerBlockStyle( 'core/image' , {
	name: 'parallel',
	label: __( 'Parallel', 'grimlock' ),
	isDefault: false,
} );

registerBlockStyle( 'core/button' , {
    name: 'primary',
    label: __( 'Primary', 'grimlock' ),
    isDefault: true,
} );

registerBlockStyle( 'core/button' , {
    name: 'secondary',
    label: __( 'Secondary', 'grimlock' ),
    isDefault: false,
} );

registerBlockStyle( 'core/button' , {
	name: 'primary-outline',
	label: __( 'Primary Outline', 'grimlock' ),
	isDefault: false,
} );

registerBlockStyle( 'core/button' , {
	name: 'secondary-outline',
	label: __( 'Secondary Outline', 'grimlock' ),
	isDefault: false,
} );

registerBlockStyle( 'core/button' , {
	name: 'primary-inverse',
	label: __( 'Primary Inverse', 'grimlock' ),
	isDefault: false,
} );

registerBlockStyle( 'core/button' , {
	name: 'secondary-inverse',
	label: __( 'Secondary Inverse', 'grimlock' ),
	isDefault: false,
} );

registerBlockStyle( 'core/button' , {
	name: 'current-outline',
	label: __( 'Current Outline', 'grimlock' ),
	isDefault: false,
} );

registerBlockStyle( 'core/button' , {
    name: 'link',
    label: __( 'Link', 'grimlock' ),
    isDefault: false,
} );

domReady( () => {
    unregisterBlockStyle( 'core/button', 'fill' );
    unregisterBlockStyle( 'core/button', 'outline' );
} );

// Add responsive columns controls to the core columns block
addFilter( 'editor.BlockEdit', 'grimlock/change-columns-block-edit', createHigherOrderComponent( ( BlockEdit ) => {
    return ( props ) => {
        if ( props.name !== 'core/columns' )
            return <BlockEdit { ...props } />;

        const { attributes, setAttributes } = props;

        /**
         * Get the number of columns for a given device
         * @param {string} device The device to get the columns for
         * @return {number} The number of columns for the given device
         */
        const getColumns = ( device ) => {
            let columns = 0;
            if ( attributes.className ) {
                let classNames = attributes.className.split( ' ' );
                classNames.forEach( ( className ) => {
                    if ( className.match( new RegExp( `^grimlock-columns-${device}-[0-9]$` ) ) )
                        columns = parseInt( className.split( `grimlock-columns-${device}-` )[1] );
                } );
            }
            return columns;
        };

        /**
         * Update the block's classes depending on the number of columns for each device
         * @param {number} columns The new number of columns
         * @param {string} device The device that the columns should be applied to
         */
        const updateColumnsClassName = ( columns, device ) => {
            let classNames = [];

            // Remove previous class
            if ( attributes.className ) {
                classNames = attributes.className.split( ' ' );
                classNames.forEach( ( className, key ) => {
                    if ( className.match( new RegExp( `^grimlock-columns-${device}-[0-9]$` ) ) )
                        classNames.splice( key, 1 );
                } );
            }

            // Add new class
            if ( columns )
                classNames.push( `grimlock-columns-${device}-${columns}` );

            // Update attributes
            setAttributes( { className: classNames.join( ' ' ) } );
        };

        return (
            <Fragment>
                <BlockEdit { ...props } />
                <InspectorControls>
                    <PanelBody title={ __( 'Responsive', 'grimlock' ) }
                               initialOpen={ false }>
                        <RangeControl label={ __( 'Columns on desktop', 'grimlock' ) }
                                      value={ getColumns( 'desktop' ) }
                                      onChange={ ( value ) => updateColumnsClassName( value, 'desktop' ) }
                                      min={ 0 }
                                      max={ 6 }
                                      step={ 1 } />
                        <RangeControl label={ __( 'Columns on tablet', 'grimlock' ) }
                                      value={ getColumns( 'tablet' ) }
                                      onChange={ ( value ) => updateColumnsClassName( value, 'tablet' ) }
                                      min={ 0 }
                                      max={ 6 }
                                      step={ 1 } />
                        <RangeControl label={ __( 'Columns on mobile', 'grimlock' ) }
                                      value={ getColumns( 'mobile' ) }
                                      onChange={ ( value ) => updateColumnsClassName( value, 'mobile' ) }
                                      min={ 0 }
                                      max={ 6 }
                                      step={ 1 } />
                    </PanelBody>
                </InspectorControls>
            </Fragment>
        );
    };
}, 'changeColumnsBlockEdit' ) );
