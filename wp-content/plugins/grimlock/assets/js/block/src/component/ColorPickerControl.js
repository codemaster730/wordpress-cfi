import { Component } from '@wordpress/element';
import { BaseControl, ColorIndicator, ColorPicker, Button, ColorPalette, Dropdown } from '@wordpress/components';
import { useSelect } from '@wordpress/data';

class ColorPickerControl extends Component {
    constructor( props ) {
        super( props );
        this.state = { isOpen: false };
    }

    render() {
        const { label, help, value, colors, onChange } = this.props;

        return (
            <BaseControl label={ label } help={ help } className="grimlock-color-picker-control">

                <ColorIndicator colorValue={ value } />

                <ColorPalette value={ value }
                              colors={ colors }
                              onChange={ onChange }
                              disableCustomColors
                              clearable={false} />

                <div style={ { textAlign: 'right' } }>

                    <Dropdown position="bottom right"
                              renderToggle={ ( { isOpen, onToggle } ) => (
                                  <Button style={ { marginRight: '16px' } } isLink onClick={ onToggle } aria-expanded={ isOpen }>
                                      { __( 'Custom color', 'grimlock' ) }
                                  </Button>
                              ) }
                              renderContent={ () => (
                                  <ColorPicker color={ value }
											   enableAlpha
											   onChangeComplete={ ( color ) => {
												   if ( !!color.color )
													   onChange( color.color.toRgbString() ); // Backwards compat with WP < 5.9
												   else
													   onChange( color.hex );
											   } } />
                              ) } />

                    <Button onClick={ () => onChange( '' ) } isSecondary isSmall>
                        { __( 'Clear', 'grimlock' ) }
                    </Button>

                </div>

            </BaseControl>
        );
    }
}

export default ( props ) => {
    const colorPickerSettings = useSelect( ( select ) => {
        const settings = select( 'core/block-editor' ).getSettings();
        return { colors: settings.colors };
    } );

    return <ColorPickerControl { ...{ ...colorPickerSettings, ...props } } />;
}
