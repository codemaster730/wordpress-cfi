import { __ } from '@wordpress/i18n';
import { registerBlockType, createBlock } from '@wordpress/blocks';
import {
    Disabled,
    PanelBody,
	Panel,
	PanelHeader,
    TextControl,
    TextareaControl,
    RadioControl,
    RangeControl,
    ToggleControl,
    SelectControl,
    DatePicker,
    BaseControl,
    Dropdown,
    Button,
} from '@wordpress/components';
import {
    InspectorControls,
	BlockIcon,
    __experimentalColorGradientControl as ColorGradientControl,
} from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import { Fragment, useState, useLayoutEffect } from '@wordpress/element';
import he from 'he';
import ImageSelectorControl from '../component/ImageSelectorControl';
import ColorPickerControl from '../component/ColorPickerControl';
import SelectControlWithOptGroup from '../component/SelectControlWithOptGroup';
import SortablePostsMultiSelectControl from '../component/SortablePostsMultiSelectControl';
import TermSelectControl from '../component/TermSelectControl';
import SortableTermsMultiSelectControl from '../component/SortableTermsMultiSelectControl';
import AlignmentMatrixControl from '../component/AlignmentMatrixControl';

export default class Block {
    constructor( { name, args, panels }, idBase ) {
        this.name = name;
        this.args = args;
        this.panels = panels;
        this.idBase = idBase;

        this.init();
    }

    /**
     * Initialize block
     */
    init() {
        registerBlockType( this.name, {
            ...this.args,
			transforms: {
				from: [
					{
						type: 'block',
						blocks: [ 'core/legacy-widget' ],
						isMatch: ( { idBase, instance } ) => {
							if ( ! instance?.raw ) {
								// Can't transform if raw instance is not shown in REST API.
								return false;
							}
							return idBase === this.idBase.replace( 'block', 'widget' ) ;
						},
						transform: ( { instance } ) => {
							const {
								background_gradient_first_color,
								background_gradient_second_color,
								background_gradient_direction,
								background_gradient_position,
								classes: className,
								...widgetAttributes
							} = instance.raw;

							let blockAttributes = { align: 'full', className, ...widgetAttributes };

							if ( !! widgetAttributes.background_gradient_displayed ) {
								blockAttributes.background_gradient = `linear-gradient(${background_gradient_direction}, ${background_gradient_second_color} ${background_gradient_position}%, ${background_gradient_first_color} 100%)`;
							}

							return createBlock( this.name, blockAttributes );
						},
					},
				]
			},
            edit: ( { attributes, setAttributes } ) => {

                // Apply init logic on first render
                useLayoutEffect( () => {
                    Object.keys( this.panels ).map( ( panelKey ) => {
                        const panel = this.panels[ panelKey ];

                        // Bail if panel has no field
                        if ( ! panel.fields )
                            return;

                        panel.fields.forEach( ( field ) => {
                            // If applicable, convert old attribute names to new and clean old value
                            if ( attributes[ field['old_name'] ] !== undefined ) {
                                setAttributes( {
                                    [ field[ 'name' ] ]: attributes[ field[ 'old_name' ] ],
                                    [ field[ 'old_name' ] ]: undefined,
                                } );
                            }
                        } );
                    } );
                }, [] );

				if ( wp.customize )
					return (
						<>
							<Panel>

								<PanelHeader>
									<div style={ { display: 'flex', alignItems: 'center' } }>
										<div style={ { marginRight: '5px' } }>
											<BlockIcon style={ { marginRight: '5px' } } icon={this.args.icon} />
										</div>
										<div style={ { fontWeight: '500' } }>
											{this.args.title}
										</div>
									</div>
								</PanelHeader>

								{ this.renderPanels( attributes, setAttributes ) }
							</Panel>
						</>
					);

                return (
                    <>
                        <InspectorControls>

                            { this.renderPanels( attributes, setAttributes ) }

                        </InspectorControls>

                        <Disabled>
                            <ServerSideRender block={ this.name }
                                              attributes={ attributes } />
                        </Disabled>
                    </>
                );
            },
            save: () => null,
        } );
    }

	/**
	 * Render block panels
	 *
	 * @param attributes Object containing the block attributes
	 * @param setAttributes Function used to update the block attributes
	 *
	 * @return array of react elements
	 */
	renderPanels( attributes, setAttributes ) {
		return Object.keys( this.panels ).map( ( panelKey ) => {
			const panel = this.panels[ panelKey ];

			// Bail if panel has no field
			if ( ! panel.fields )
				return;

			return (
				<PanelBody title={ panel.label } key={ panelKey } initialOpen={false}>

					{ this.renderFields( panel.fields, attributes, setAttributes ) }

				</PanelBody>
			);
		} );
	}

    /**
     * Render block fields
     *
     * @param fields Array of fields to render
     * @param attributes Object containing the block attributes
     * @param setAttributes Function used to update the block attributes
     *
     * @return Array of react elements
     */
    renderFields( fields, attributes, setAttributes ) {
        return fields.map( ( field, key ) => {
            // Bail if field has no type
            if ( ! field[ 'type' ] )
                return;

            // Get the render function for this field type
            const RenderField = this[ field[ 'type' ] + 'Field' ];

            let displayed = true;
            if ( field['conditional_logic'] && field['conditional_logic'].length )
                displayed = this.processConditions( field['conditional_logic'], attributes );

            // Bail if a render function doesn't exist for this field type
            if ( ! RenderField || ! displayed )
                return;

            // Hack to clone the field parameter before modifying it
            let fieldArgs = { ...field };

            if ( fieldArgs['label'] )
                fieldArgs[ 'label' ] = he.decode( fieldArgs['label'] );

            // Replace field args with attribute value where necessary
            Object.keys( fieldArgs ).forEach( ( fieldArgKey ) => {
                let fieldArg = fieldArgs[ fieldArgKey ];
                // If fieldArg is a string contained in brackets, we replace it by the value of the attribute with the name in brackets
                if ( typeof fieldArg === 'string' && fieldArg.match( /^{.+}$/g ) ) {
                    // Remove the brackets
                    let attributeName = fieldArg.slice( 1, fieldArg.length - 1 );

                    // Replace with attribute value
                    fieldArgs[ fieldArgKey ] = attributes[ attributeName ];
                }
            } );

            return (
                <RenderField key={ key } args={ fieldArgs } attributes={ attributes } setAttributes={ setAttributes } />
            );
        } );
    }

    /**
     * Process conditional logic to determine whether a field should be displayed or not
     *
     * @param conditionalLogic Array of conditional logic
     * @param attributes Block attributes from which we pull values to do the comparisons
     * @param relation Relation to apply between conditions. Automatically alternates between AND and OR for each nested array.
     * @return {*}
     */
    processConditions( conditionalLogic, attributes, relation = 'AND' ) {
        let finalResult;

        conditionalLogic.forEach( ( conditions ) => {
            let result;

            if ( conditions.length ) {
                // If we have a nested array, use recursion to get the result of the nested conditions
                result = this.processConditions( conditions, attributes, relation === 'AND' ? 'OR' : 'AND' );
            }
            else if ( conditions['field'] && conditions['operator'] && conditions['value'] !== undefined ) {
                if ( undefined === attributes[ conditions['field'] ] ) {
                    // If the attribute is undefined (the field doesn't exist) we can't check the condition properly, so we assume true
                    result = true;
                }
                else {
                    // Get the result of the condition
                    switch ( conditions[ 'operator' ] ) {
                        case '===':
                            result = attributes[ conditions[ 'field' ] ] === conditions[ 'value' ];
                            break;
                        case '==':
                            result = attributes[ conditions[ 'field' ] ] == conditions[ 'value' ];
                            break;
                        case '!==':
                            result = attributes[ conditions[ 'field' ] ] !== conditions[ 'value' ];
                            break;
                        case '!=':
                            result = attributes[ conditions[ 'field' ] ] != conditions[ 'value' ];
                            break;
                        case '>':
                            result = attributes[ conditions[ 'field' ] ] > conditions[ 'value' ];
                            break;
                        case '>=':
                            result = attributes[ conditions[ 'field' ] ] >= conditions[ 'value' ];
                            break;
                        case '<':
                            result = attributes[ conditions[ 'field' ] ] < conditions[ 'value' ];
                            break;
                        case '<=':
                            result = attributes[ conditions[ 'field' ] ] <= conditions[ 'value' ];
                            break;
                    }
                }
            }

            if ( undefined !== result ) {
                if ( undefined === finalResult )
                    finalResult = result;
                else
                    finalResult = relation === 'AND' ? finalResult && result : finalResult || result;
            }
        } );

        return finalResult;
    }

    /**
     * Render an "Edit menu" link
     *
     * @param args Array of field args
     */
    editMenuLinkField( { args } ) {
        let menuId = args['menu_ids'][ args['menu'] ];

        if ( ! menuId )
            return null;

        return (
            <div className="components-base-control">
                {
                    !!wp.customize ?
                        <a href="#" onClick={ () => wp.customize.section( 'nav_menu[' + menuId + ']' ).focus() }>
                            { args['link_label'] || '' }
                        </a> :
                        <a href={ args['admin_url'] + 'nav-menus.php?action=edit&menu=' + menuId } target="_blank">
                            { args['link_label'] || '' }
                        </a>
                }
            </div>
        );
    }

    /**
     * Render a separator
     */
    separatorField() {
        return <hr />;
    }

    /**
     * Render a text field
     *
     * @param args Array of field args
     * @param attributes Object containing the block attributes
     * @param setAttributes Function used to update the block attributes
     */
    textField( { args, attributes, setAttributes } ) {
        // Bail if field has no name
        if ( ! args['name'] )
            return;

        return (
            <TextControl label={ args['label'] || '' }
                         help={ args['help'] || '' }
                         value={ attributes[ args['name'] ] || '' }
                         onChange={ ( value ) => setAttributes( { [ args['name'] ] : value } ) } />
        );
    }

    /**
     * Render a date field
     *
     * @param args Array of field args
     * @param attributes Object containing the block attributes
     * @param setAttributes Function used to update the block attributes
     */
    dateField( { args, attributes, setAttributes } ) {
        // Bail if field has no name
        if ( ! args['name'] )
            return;

        return (
            <BaseControl label={ args['label'] || '' } help={ args['help'] || '' }>
                <div>
                    <Dropdown position="bottom right"
                              renderToggle={ ( { onToggle } ) => (
                                  <>
                                      <input type="text" readOnly value={ attributes[ args['name'] ] || '' } onClick={ onToggle } />
                                      <Button style={ { marginLeft: '8px' } } isSecondary isSmall onClick={ () => setAttributes( { [ args['name'] ] : '' } ) }>
                                          { __( 'Clear', 'grimlock' ) }
                                      </Button>
                                  </>
                              ) }
                              renderContent={ () => (
                                  <DatePicker currentDate={ attributes[ args['name'] ] || null }
                                              onChange={ ( date ) => { setAttributes( { [ args['name'] ] : date.split( 'T' )[0] } ); } } />
                              ) } />
                </div>
            </BaseControl>
        );
    }

    /**
     * Render a number field
     *
     * @param args Array of field args
     * @param attributes Object containing the block attributes
     * @param setAttributes Function used to update the block attributes
     */
    numberField( { args, attributes, setAttributes } ) {
        // Bail if field has no name
        if ( ! args['name'] )
            return;

        return (
            <TextControl label={ args['label'] || '' }
                         help={ args['help'] || '' }
                         value={attributes[ args[ 'name' ] ] || 0}
                         type={'number'}
                         onChange={( value ) => setAttributes( { [ args[ 'name' ] ]: `${value}` /* Hack to force value to be a string */ } )} />
        );
    }

    /**
     * Render a textarea field
     *
     * @param args Array of field args
     * @param attributes Object containing the block attributes
     * @param setAttributes Function used to update the block attributes
     */
    textareaField( { args, attributes, setAttributes } ) {
        // Bail if field has no name
        if ( ! args['name'] )
            return;

        return (
            <TextareaControl label={ args['label'] || '' }
                             help={ args['help'] || '' }
                             value={ attributes[ args['name'] ] || '' }
                             onChange={ ( value ) => setAttributes( { [ args['name'] ] : value } ) } />
        );
    }

    /**
     * Render a image field
     *
     * @param args Array of field args
     * @param attributes Object containing the block attributes
     * @param setAttributes Function used to update the block attributes
     */
    imageField( { args, attributes, setAttributes } ) {
        // Bail if field has no name
        if ( ! args['name'] )
            return;

        return (
            <ImageSelectorControl label={ args['label'] || '' }
                                  help={ args['help'] || '' }
                                  value={ attributes[ args['name'] ] || 0 }
                                  onChange={ ( value ) => setAttributes( { [ args['name'] ] : value } ) }
                                  gallery={ !! args['gallery'] } />
        );
    }

    /**
     * Render a toggle field
     *
     * @param args Array of field args
     * @param attributes Object containing the block attributes
     * @param setAttributes Function used to update the block attributes
     */
    toggleField( { args, attributes, setAttributes } ) {
        // Bail if field has no name
        if ( ! args['name'] )
            return;

        return (
            <ToggleControl label={ args['label'] || '' }
                           help={ args['help'] || '' }
                           checked={ !! attributes[ args['name'] ] }
                           onChange={ ( value ) => setAttributes( { [ args['name'] ] : value } ) } />
        );
    }

    /**
     * Render a select field
     *
     * @param args Array of field args
     * @param attributes Object containing the block attributes
     * @param setAttributes Function used to update the block attributes
     */
    selectField( { args, attributes, setAttributes } ) {
        // Bail if field has no name or no choice
        if ( ! args['name'] || ! args['choices'] )
            return;

        let hasSubOptions = false;
        const options = Object.keys( args['choices'] ).map( ( option ) => {
            if ( args['choices'][ option ]['subchoices'] ) {
                hasSubOptions = true;

                const subOptions = Object.keys( args['choices'][ option ]['subchoices'] ).map( ( subOption ) => {
                    return { value: subOption, label: args['choices'][ option ]['subchoices'][ subOption ] };
                } );

                return { label: he.decode( args['choices'][ option ]['label'] ), options: subOptions };
            }

            return { value: option, label: he.decode( args['choices'][ option ] ) };
        } );

        return hasSubOptions || args['multiple'] ? (
            <SelectControlWithOptGroup label={ args['label'] || '' }
                                       help={ args['help'] || '' }
                                       value={ attributes[ args['name'] ] || '' }
                                       onChange={ ( value ) => setAttributes( { [ args['name'] ] : value } ) }
                                       options={ options }
                                       multiple={ args['multiple'] } />
        ) : (
            <SelectControl label={ args['label'] || '' }
                           help={ args['help'] || '' }
                           value={ attributes[ args['name'] ] || '' }
                           onChange={ ( value ) => setAttributes( { [ args['name'] ] : value } ) }
                           options={ options } />
        );
    }

    /**
     * Render a term select field
     *
     * @param args Array of field args
     * @param attributes Object containing the block attributes
     * @param setAttributes Function used to update the block attributes
     */
    termSelectField( { args, attributes, setAttributes } ) {
        // Bail if field has no name or no taxonomy
        if ( ! args['name'] || ! args['taxonomy'] )
            return;

        const [ inputValue, setInputValue ] = useState( '' );
        const [ selectValue, setSelectValue ] = useState( false );

        if ( args['multiple'] ) {

            return (
                <SortableTermsMultiSelectControl label={ args['label'] || '' }
                                                 help={ args['help'] || '' }
                                                 selectedTermIds={ attributes[ args['name'] ] || [] }
                                                 selectedTerms={ selectValue }
                                                 taxonomy={ args['taxonomy'] }
                                                 onChange={ ( value ) => {
                                                     setSelectValue( value );
                                                     setAttributes( { [ args['name'] ] : value && value.length ? value.map( ( option ) => option.value ) : [] } );
                                                 } }
                                                 inputValue={ inputValue }
                                                 onInputChange={ setInputValue } />
            );
        }
        else {
            return (
                <TermSelectControl label={ args[ 'label' ] || '' }
                                   help={ args['help'] || '' }
                                   value={ attributes[ args[ 'name' ] ] || '' }
                                   taxonomy={ args[ 'taxonomy' ] }
                                   queryArgs={ args[ 'query_args' ] }
                                   emptyChoice={ args[ 'empty_choice' ] }
                                   onChange={ ( value ) => setAttributes( { [ args[ 'name' ] ]: value } ) } />
            );
        }
    }

    /**
     * Render a post select field
     *
     * @param args Array of field args
     * @param attributes Object containing the block attributes
     * @param setAttributes Function used to update the block attributes
     */
    postSelectField( { args, attributes, setAttributes } ) {
        // Bail if field has no name or no taxonomy
        if ( ! args['name'] || ! args['post_type'] )
            return;

        const [ inputValue, setInputValue ] = useState( '' );
        const [ selectValue, setSelectValue ] = useState( false );

        return (
            <SortablePostsMultiSelectControl label={ args['label'] || '' }
                                             help={ args['help'] || '' }
                                             selectedPostIds={ attributes[ args['name'] ] || [] }
                                             selectedPosts={ selectValue }
                                             postType={ args['post_type'] }
                                             onChange={ ( value ) => {
                                                 setSelectValue( value );
                                                 setAttributes( { [ args['name'] ] : value && value.length ? value.map( ( option ) => option.value ) : [] } );
                                             } }
                                             inputValue={ inputValue }
                                             onInputChange={ setInputValue } />
        );
    }

    /**
     * Render a radio image field
     *
     * @param args Array of field args
     * @param attributes Object containing the block attributes
     * @param setAttributes Function used to update the block attributes
     */
    radioImageField( { args, attributes, setAttributes } ) {
        // Bail if field has no name or no choice
        if ( ! args['name'] || ! args['choices'] )
            return;

        const options = Object.keys( args['choices'] ).map( ( option ) => {
            return { value: option, label: <img src={ args['choices'][ option ] } alt={ option } /> };
        } );

        return (
            <RadioControl label={ args['label'] || '' }
                          help={ args['help'] || '' }
                          selected={ attributes[ args['name'] ] || '' }
                          onChange={ ( value ) => setAttributes( { [ args['name'] ] : value } ) }
                          options={ options }
                          className={ `grimlock-radio-image grimlock-block-field-${args['name']}` } />
        );
    }

    /**
     * Render an alignment matrix field
     *
     * @param args Array of field args
     * @param attributes Object containing the block attributes
     * @param setAttributes Function used to update the block attributes
     */
    alignmentMatrixField( { args, attributes, setAttributes } ) {
        // Bail if field has no name or no choice
        if ( ! args['name'] )
            return;

        return (
            <AlignmentMatrixControl label={ args['label'] || '' }
                                    help={ args['help'] || '' }
                                    value={ attributes[ args['name'] ] || '' }
                                    onChange={ ( value ) => setAttributes( { [ args['name'] ] : value } ) } />
        );
    }

    /**
     * Render a range field
     *
     * @param args Array of field args
     * @param attributes Object containing the block attributes
     * @param setAttributes Function used to update the block attributes
     */
    rangeField( { args, attributes, setAttributes } ) {
        // Bail if field has no name or no choice
        if ( ! args['name'] )
            return;

        return (
            <RangeControl label={ args['label'] || '' }
                          help={ args['help'] || '' }
                          value={ attributes[ args['name'] ] || 0 }
                          onChange={ ( value ) => setAttributes( { [ args['name'] ] : value } ) }
                          min={ args['min'] }
                          max={ args['max'] }
                          step={ args['step'] } />
        );
    }

    /**
     * Render a color field
     *
     * @param args Array of field args
     * @param attributes Object containing the block attributes
     * @param setAttributes Function used to update the block attributes
     */
    colorField( { args, attributes, setAttributes } ) {
        // Bail if field has no name or no choice
        if ( ! args['name'] )
            return;

        return (
            <ColorPickerControl label={ args['label'] || '' }
                                help={ args['help'] || '' }
                                value={ attributes[ args['name'] ] || '#ffffff' }
                                onChange={ ( value ) => setAttributes( { [ args['name'] ] : value } ) } />
        );
    }

    /**
     * Render a gradient field
     *
     * @param args Array of field args
     * @param attributes Object containing the block attributes
     * @param setAttributes Function used to update the block attributes
     */
    gradientField( { args, attributes, setAttributes } ) {
        // Bail if field has no name or no choice
        if ( ! args['name'] )
            return;

        return (
            <ColorGradientControl label={ args['label'] || '' }
                                  help={ args['help'] || '' }
                                  gradientValue={ attributes[ args['name'] ] || '' }
                                  onGradientChange={ ( value ) => setAttributes( { [ args['name'] ] : value } ) } />
        );
    }
}
