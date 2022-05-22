import { BaseControl, __experimentalAlignmentMatrixControl as AlignmentMatrixControl, } from '@wordpress/components';

export default ( props ) => (
    <BaseControl label={ props.label } help={ props.help } className="grimlock-alignment-matrix-control">
        <AlignmentMatrixControl {...props} />
    </BaseControl>
);
